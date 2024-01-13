<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';

// session

$verbindung = get_verbindung();

$pid = $_GET['pid'];
$fid = $_GET['fid'];
$sid = $_GET['sid'];

$betray = false;

$builder = '';

// CHEATSCHUTZ ANFANG
if (!is_null($pid) && !is_null($fid)) {
    $builder = 'planet';
    // fid abfrage

    $abfrage11 = mysqli_query($verbindung, "SELECT * FROM planet2 WHERE pid='$pid'");
    while ($test11 = mysqli_fetch_array($abfrage11)) {
        splitfeld($test11[$planet->xx], $a, $b, $c, $d, $e);
        if ($a != 4 || $e != 1 || $b != 0) {
            $betray = true;
        }
    }

    if ($fid <= 0) {
        $betray = true;
    }
    if (!ctype_digit($pid)) {
        $betray = true;
    }
    if (!ctype_digit($fid)) {
        $betray = true;
    }
    $tmp = mysqli_query($verbindung, "SELECT besitzer FROM planeten WHERE id='$pid'");
    while ($testtmp = mysqli_fetch_array($tmp)) {
        if ($_SESSION['Id'] != $testtmp['besitzer']) {
            $betray = true;
        }
    }
} elseif (!is_null($sid)) {
    $builder = 'ship';
    // check here if ship is belonging to owner
    // and if ship is constructor

    $query = '
        SELECT 1
        FROM schiffe s 
        JOIN bauplan p 
            on s.klasse = p.klasse 
        WHERE s.besitzer = %d
        AND  p.skillbau = 1
    ';

    if (mysqli_num_rows(mysqli_query($verbindung, sprintf($query, $_SESSION['Id']))) == 0) {
        $betray = true;
    }
}

if ($betray) {
    echo 'Du bist nicht eingeloggt oder du versucht auf fremde Accounts zuzugreifen...';
} else {
    // CHEATSCHUTZ ENDE

    $slots = getSlot($_SESSION['Id']);

    if ($slots >= 50) {
        echo 'Dein Schiffslimit ist leider ausgereizt. <br /><br />Du hast ', $slots, ' von 50 SLotpl&auml;tze verbraucht!<br />Frachter und Tanker verbrauchen 1 SLot, Oberth 1 SLot, Miranda 2 Slots, Constitution 3 SLots, Raumstationen 2 Slots!';
    } else {
        if (isset($_POST['pschiff'])) {
            // schiffbau

            $b = new Bauplan_Schiffe(mysqli_real_escape_string($verbindung, $_POST['pschiff']));
            $p = $builder == "planet" ? new Planeten($pid) : new Schiffe($sid);

            // check Waren
            $buildit = true;
            for ($i = 0; $i < sizeof($p->frachtraum->fracht); ++$i) {
                if ($p->frachtraum->fracht[$i]->anzahl < $b->kosten->fracht[$i + 1]->anzahl) {
                    $buildit = false;
                }
            }

            if ($buildit && $p->energie >= $b->kosten->fracht[0]->anzahl) {
                // we can build now
                for ($i = 0; $i < sizeof($p->frachtraum->fracht); ++$i) {
                    $p->frachtraum->fracht[$i]->anzahl -= $b->kosten->fracht[$i + 1]->anzahl;
                }
                $p->energie -= $b->kosten->fracht[0]->anzahl;

                mysqli_query($verbindung, "insert into schiffe 
                       (id,name,x,y,orbit,system,besitzer,energie,warpkern,warpkernstatus,hull,schilde,schildstatus,alarmstufe,typ,frachtraum,flotte,phaser,torpedohitze,gondeln,nachricht,tarnung,klasse,dock,loot,kills,defend) values 
                       (NULL,'unbenannt','".$p->position->x."','".$p->position->y."','".$p->position->orbit."','".$p->position->system->id."','".$_SESSION['Id']."','0','0','0','".$b->maxhull."','0','0','green','','".$b->bauzeit."','0','0','0','0','".$pid.'/'.$fid."','0','".$b->klasse."','0','0','0','0')");

                mysqli_query($verbindung, "update planeten set energie='".$p->energie."' where id = ".$p->id);
                $p->frachtraum->save();

                echo '<span class="success">Ein Schiff der '.$b->klasse.'-Klasse wird gebaut und ist in '.$b->bauzeit.' Ticks fertig!</span><br />';
            }
        }

        // frachtraum

        echo '<h3>Schiffsbau</h3>';

        // check if already building
        $q = mysqli_query($verbindung, "select klasse from schiffe where typ='' and nachricht='".$pid.'/'.$fid."'");
        if (mysqli_num_rows($q) > 0) {
            echo '<span class="error">Diese Werft baut bereits!</span>';
        } else {

            if($builder == "planet") {

                echo '<form action="createship.php?pid='.$pid.'&fid='.$fid.'" method="post">';
            } 
            else {
                echo '<form action="createship.php?sid='.$sid.'" method="post">';
            }

            echo '<table class="invitetable" style="text-align:center;"><tr><th></th><th>Schiffsname</th><th>Bild</th><th>Baukosten</th><th>Energie</th><th>Warpkern</th><th>Schilde</th><th>H&uuml;lle</th><th>Phaserst&auml;rke</th><th>Torpedokapazität</th><th>Lagerraum</th><th>Gondeln</th><th>Bauzeit in Ticks</th><th>sonstiges</th><th></th></tr>';

            $query = '
                select klasse from bauplan where %s and skillbase = %d
            ';

            $query = sprintf($query, $_SESSION['Id'] < 10 && $builder == 'ship' ? '1=1' : 'siedler=1', $builder == 'ship' ? 1 : 0);

            $q = mysqli_query($verbindung, $query);
            while ($r = mysqli_fetch_array($q)) {
                $t = new Bauplan_Schiffe($r['klasse']);
                echo '<tr><td><input type="radio" name="pschiff" value="'.$t->klasse.'" /></td><td>'.$t->klasse.'</td><td><img src="'.$t->bild.'" border="0" /></td><td><table>';
                for ($i = 0; $i < sizeof($t->kosten->fracht); ++$i) {
                    if ($t->kosten->fracht[$i]->anzahl > 0) {
                        echo '<tr><td>'.$t->kosten->fracht[$i]->name.'</td><td><img src="images/misc/'.$t->kosten->fracht[$i]->bild.'" border="0" /></td><td>'.$t->kosten->fracht[$i]->anzahl.'</td></tr>';
                    }
                }
                echo '</table></td>';

                echo '<td>'.$t->maxenergie.' +'.$t->energieoutput.'</td><td>'.$t->maxwarpkern.'</td>';
                echo '<td>'.$t->maxschilde.'</td><td>'.$t->maxhull.'</td>';
                echo '<td>'.$t->laser.' ('.$t->maxphaser.')</td><td>'.($t->dummy_maxfracht[10] >= 0 ? $t->dummy_maxfracht[10] : 'u').'/'.($t->dummy_maxfracht[11] >= 0 ? $t->dummy_maxfracht[11] : 'u').' ('.$t->maxtorpedohitze.')</td><td>'.$t->lager.'</td><td>'.$t->maxgondeln.'</td><td>'.$t->bauzeit.'</td><td>';

                if ($t->skill->deuterium) {
                    echo '- Deuteriumtanker<br />';
                }
                if ($t->skill->erz) {
                    echo '- Erzkollektor <br />';
                }
                if ($t->skill->bauen) {
                    echo '- Bauschiff <br />';
                }

                echo '</td></tr>';
            }

            // echo '<tr><td>Kriegsschiff</td><td>400 Rohstoff A<br />20 Rohstoff B</td><td>100</td><td>300</td><td>8</td><td>2</td><td>1</td><td>0</td><td>250</td><td>3</td><td><a href="createship.php?pid=',$planet->id,'&job=3">Schiff bauen!</a></td></tr>';

            echo '</table><br />';

            $bu = new Button('', 'Schiff bauen');
            $bu->printme();
        }
        echo '<br /><br /><a href="repair.php?pid=', $pid, '"><span style="color:yellow;">Schiffe reparieren</span></a><br /><br />';

        if ($builder == 'planet') {
            $bu = new Button('planet.php?pid='.$pid, 'zurück zum Planet');
            $bu->printme();
        } else {
            $bu = new Button('schiffe.php?sid='.$sid, 'zurück zum Schiff');
            $bu->printme();
        }
    }
}
include 'foot.php';
