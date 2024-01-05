<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';
$pid = $_GET['pid'];
$fid = $_GET['fid'];
$do = $_GET['do'];

$verbindung = get_verbindung();

$selfid = $_SESSION['Id'];
$res1 = mysqli_query($verbindung, "SELECT mitglied FROM account WHERE id='$selfid'");
$row1 = mysqli_fetch_array($res1);
$mitglied = $row1['mitglied'];

$ich = new Account($_SESSION['Id']);

$myAccount = new Account($selfid);

// CHEATSCHUTZ ANFANG

$betray = false;
$testid = $_GET['sid'];
if (!isset($testid)) {
    $testid = $_GET['pid'];
}
if (!ctype_digit($_GET['pid']) || !ctype_digit($fid)) {
    exit('Fehler: ID ung&uuml;ltig');
}

// CHEATSCHUTZ ENDE

$planet = new Planeten($pid);

if ($planet->besitzer->id != $_SESSION['Id']) {
    exit('Fehler: Besitzer-ID ung&uuml;ltig');
}

if (isset($_GET['bauen']) && ctype_digit($_GET['bauen'])) {
    $error = '';
    $bauplan = new Bauplan_Gebaude($_GET['bauen']);
    for ($i = 0; $i < sizeof($planet->frachtraum->fracht); ++$i) {
        if ($planet->frachtraum->fracht[$i]->anzahl < $bauplan->baukosten->fracht[$i + 1]->anzahl) {
            $error .= '<span class="error">Du benötigst zum Bau '.$bauplan->baukosten->fracht[$i + 1]->anzahl.' '.$bauplan->baukosten->fracht[$i + 1]->name.'. ';
            $error .= 'Vorhanden sind aber nur '.$planet->frachtraum->fracht[$i]->anzahl.' '.$planet->frachtraum->fracht[$i]->name.'.</span><br />';
        }
    }

    if ($planet->energie < $bauplan->baukosten->fracht[0]->anzahl) {
        $error .= '<span class="error">Du benötigst zum Bau '.$bauplan->baukosten->fracht[0]->anzahl.' '.$bauplan->baukosten->fracht[0]->name.'. ';
        $error .= 'Vorhanden sind aber nur '.$planet->energie.' Energie.</span><br />';
    }

    if ($error != '') {
        echo $error.'<br />';
    } else {
        // bauen
        $planet->feld[$fid]->bau = $bauplan;
        $planet->feld[$fid]->aktiv = 1;
        $planet->feld[$fid]->rest_bauzeit = $myAccount->level <= 3 ? 0 : $bauplan->bauzeit;
        $planet->feld[$fid]->save();

        for ($i = 0; $i < sizeof($planet->frachtraum->fracht); ++$i) {
            $planet->frachtraum->fracht[$i]->anzahl -= $bauplan->baukosten->fracht[$i + 1]->anzahl;
        }

        $planet->energie -= $bauplan->baukosten->fracht[0]->anzahl;

        $planet->frachtraum->save();
        mysqli_query($verbindung, 'update planeten set energie='.$planet->energie.' where id = '.$planet->id);

        echo '<span class="success">'.$bauplan->name.' wird jetzt gebaut und wird in '.$bauplan->bauzeit.' Ticks fertig sein!</span>';
        echo '<meta http-equiv="refresh" content="2; URL=planet.php?pid='.$planet->id.'">';
    }
}

echo '<table class="liste"><tr><th>Bild</th><th>Name</th><th>Baukosten</th><th>Kosten pro Tick</th><th>Effekt pro Tick</th><th>Effekt</th><th>Bauzeit</th><th>Baubar auf</th></tr>';

$liste = Bauplan_Gebaude::getListe($planet->feld[$fid]->untergrund->id);

for ($k = 0; $k < sizeof($liste); ++$k) {
    // echo var_dump($liste[$k]).'<br><br>';
    if ($myAccount->level < $liste[$k]->prelevel) {
        continue;
    }

    // get correct imgae index
    $t_index = -1;
    for ($i = 0; $i < sizeof($liste[$k]->untergrund); ++$i) {
        if ($liste[$k]->untergrund[$i]->id == $planet->feld[$fid]->untergrund->id) {
            $t_index = $i;
        }
    }

    echo '<tr><td><a href="build.php?pid='.$planet->id.'&fid='.$fid.'&bauen='.$liste[$k]->id.'">';
    echo '<img src="images/buildings/'.$liste[$k]->bild[$t_index].'" border="0" />';
    echo '</a></td>';
    echo '<td>'.$liste[$k]->name.'</td>';
    echo '<td><table>';
    for ($i = 0; $i < sizeof($liste[$k]->baukosten->fracht); ++$i) {
        if ($liste[$k]->baukosten->fracht[$i]->anzahl > 0) {
            echo '<tr><td>'.$liste[$k]->baukosten->fracht[$i]->name.'</td>';
            echo '<td><img src="images/misc/'.$liste[$k]->baukosten->fracht[$i]->bild.'" border="0" /></td>';
            echo '<td>'.$liste[$k]->baukosten->fracht[$i]->anzahl.'</td></tr>';
        }
    }
    echo '</table></td>';
    echo '<td><table>';
    for ($i = 0; $i < sizeof($liste[$k]->braucht->fracht); ++$i) {
        if ($liste[$k]->braucht->fracht[$i]->anzahl > 0) {
            echo '<tr><td>'.$liste[$k]->braucht->fracht[$i]->name.'</td>';
            echo '<td><img src="images/misc/'.$liste[$k]->braucht->fracht[$i]->bild.'" border="0" /></td>';
            echo '<td>'.$liste[$k]->braucht->fracht[$i]->anzahl.'</td></tr>';
        }
    }
    echo '</table></td>';
    echo '<td><table>';
    for ($i = 0; $i < sizeof($liste[$k]->produziert->fracht); ++$i) {
        if ($liste[$k]->produziert->fracht[$i]->anzahl > 0) {
            echo '<tr><td>'.$liste[$k]->produziert->fracht[$i]->name.'</td>';
            echo '<td><img src="images/misc/'.$liste[$k]->produziert->fracht[$i]->bild.'" border="0" /></td>';
            echo '<td>'.$liste[$k]->produziert->fracht[$i]->anzahl.'</td></tr>';
        }
    }
    echo '</table></td>';

    echo '<td>';
    if ($liste[$k]->lager > 0) {
        echo '+'.$liste[$k]->lager.' Lager<br />';
    }
    if ($liste[$k]->epslager > 0) {
        echo '+'.$liste[$k]->epslager.' EPS-Lager<br />';
    }
    if ($liste[$k]->schilde > 0) {
        echo '+'.$liste[$k]->schilde.' Schilde<br />';
    }
    if ($liste[$k]->laser > 0) {
        echo '+'.$liste[$k]->laser.' Phaser<br />';
    }
    if ($liste[$k]->sonstiges) {
        echo $liste[$k]->sonstiges;
    }
    echo '</td>';

    echo '<td>';
    echo ($myAccount->level <= 3 ? 0 : $liste[$k]->bauzeit).' Ticks';
    echo '</td>';
    echo '<td>';
    for ($i = 0; $i < sizeof($liste[$k]->untergrund); ++$i) {
        echo '<img src="images/buildings/'.$liste[$k]->untergrund[$i]->bild.'" border="0" />';
    }
    echo '</td></tr>';
}

echo '</table>';

echo '<br />';
$bu = new Button('planet.php?pid='.$pid, 'zurück');
$bu->printme();

include 'foot.php';
