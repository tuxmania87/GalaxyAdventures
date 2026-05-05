<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';

include_once 'auth.php';
requireLogin();
$pid = requireIntParam('pid');
$fid = requireIntParam('fid');
$planet = new Planeten($pid);
$feld = new Gebaude($pid, $fid);
if (!$feld->bau->forschung || $feld->rest_bauzeit > 0) {
    exit('Fehler: Forschung nicht möglich.');
}
requireOwnership($planet->besitzer->id, 'Planet');
// aktiveren

if (isset($_GET['dorid']) && ctype_digit($_GET['dorid'])) {
    $dorid = $_GET['dorid'];

    $todo = new Forschungen($dorid);

    $cando = true;
    for ($i = 0; $i < count($todo->pre); ++$i) {
        $map = new MappedForschungen($_SESSION['Id'], $todo->pre[$i]->id);
        if ($map->status != 1) {
            $cando = false;
            break;
        }
    }
    $q = mysqli_query($verbindung, "select * from mapforschung where status>1 and hash='".$pid.'/'.$fid."'");
    if ($cando && ($planet->frachtraum->fracht[4]->anzahl >= $todo->kosten) && mysqli_num_rows($q) == 0) {
        $planet->frachtraum->fracht[4]->anzahl -= $todo->kosten;
        $planet->frachtraum->save();
        mysqli_query($verbindung, "insert into mapforschung (uid,fid,hash,status) values ('".intval(\$_SESSION['Id'])."','".$todo->id."','".$pid.'/'.$fid."','".($todo->dauer + 1)."')");
        echo '<span class="success">'.$todo->name.' wird erforscht</span>';
        echo '<meta http-equiv="refresh" content="1; URL=forschung.php?pid='.$pid.'&fid='.$fid.'">';
        exit;
    } else {
        echo '<span class="error">Es sind nicht alle Vorraussetzungen erfüllt um zu forschen!</span><br />';
        $_GET['rid'] = $todo->id;
    }
}

if (isset($_GET['rid']) && ctype_digit($_GET['rid'])) {
    echo '<h3>Forschungsdetails</h3><br /><div style="width:600px;"><table class="invitetable" style="text-align:center;">';

    $f = new Forschungen($_GET['rid']);

    echo '<tr><th>Forschungstitel</th><td><span style="font-weight:bold;font-size:medium;">'.$f->name.'</span></td></tr>';
    echo '<tr><th>Beschreibung</th><td>'.$f->beschreibung.'</td></tr>';
    echo '<tr><th>Kosten</th><td>'.$f->kosten.' Isochips</td></tr>';
    echo '<tr><th>Benötigt</th><td><ul>';

    if (!is_null($f->pre)) {
        for ($i = 0; $i < count($f->pre); ++$i) {
            $color = 'grey';

            $mapped = new MappedForschungen($_SESSION['Id'], $f->pre[$i]->id);
            if ($mapped->status == 1) {
                $color = 'green';
            } elseif ($mapped->status > 1) {
                $color = 'red';
            } else {
                $ready = true;
                // var_dump($f->pre[$i]->pre);

                for ($j = 0; $j < count($f->pre[$i]->pre); ++$j) {
                    $t_mapped = new MappedForschungen($_SESSION['Id'], $f->pre[$i]->pre[$j]->id);

                    if ($t_mapped->status != 1) {
                        $ready = false;
                    }
                }
                if ($ready) {
                    $color = 'yellow';
                } else {
                    $color = 'grey';
                }
            }

            echo '<li><a href="forschung.php?fid='.$fid.'&pid='.$pid.'&rid='.$f->pre[$i]->id.'"><span style="color:'.$color.';">'.$f->pre[$i]->name.'</span></a></li>';
        }
        if (count($f->pre) == 0) {
            echo '<li>-</li>';
        }

        echo '</ul></td></tr>';

        echo '<tr><th>Ermöglicht</th><td><ul>';
        $l = Forschungen::getList();
        $liste = [];
        for ($i = 0; $i < count($l); ++$i) {
            if (is_null($l[$i]->pre)) {
                continue;
            }
            for ($j = 0; $j < count($l[$i]->pre); ++$j) {
                if ($l[$i]->pre[$j]->id == $f->id) {
                    $liste[] = $l[$i];
                }
            }
        }

        for ($i = 0; $i < count($liste); ++$i) {
            $color = 'grey';

            $mapped = new MappedForschungen($_SESSION['Id'], $liste[$i]->id);
            if ($mapped->status == 1) {
                $color = 'green';
            } elseif ($mapped->status > 1) {
                $color = 'red';
            } else {
                $ready = true;
                // var_dump($f->pre[$i]->pre);

                for ($j = 0; $j < count($liste[$i]->pre); ++$j) {
                    $t_mapped = new MappedForschungen($_SESSION['Id'], $liste[$i]->pre[$j]->id);

                    if ($t_mapped->status != 1) {
                        $ready = false;
                    }
                }
                if ($ready) {
                    $color = 'yellow';
                } else {
                    $color = 'grey';
                }
            }

            echo '<li><a href="forschung.php?fid='.$fid.'&pid='.$pid.'&rid='.$liste[$i]->id.'"><span style="color:'.$color.';">'.$liste[$i]->name.'</span></a></li>';
        }
        if (count($liste) == 0) {
            echo '<li>-</li>';
        }

        echo '</ul>';

        // can we build a new building?

        $q = mysqli_query($verbindung, 'select id from gebaude where preforschung='.$f->id);
        while ($r = mysqli_fetch_array($q)) {
            $t_build = new Bauplan_Gebaude($r['id']);

            echo '<table class="liste">';

            echo '<tr><th>Name</th><td>'.$t_build->name.'</td></tr>';
            echo '<tr><th>Bild</th><td><img src="images/buildings/'.$t_build->bild[0].'" border="0" /></td></tr>';
            echo '<tr><th>Untergrund</th><td>';

            for ($i = 0; $i < count($t_build->untergrund); ++$i) {
                echo '<img src="images/buildings/'.$t_build->untergrund[$i]->bild.'" border="0" />';
            }

            echo '</td></tr>';
            echo '<tr><th>Kosten je Tick</th><td>';
            echo '<table>';
            for ($i = 0; $i < count($t_build->braucht->fracht); ++$i) {
                if ($t_build->braucht->fracht[$i]->anzahl > 0) {
                    echo '<tr><td>'.$t_build->braucht->fracht[$i]->name.'</td><td><img src="images/misc/'.
                    $t_build->braucht->fracht[$i]->bild.'" border="0" /></td><td>'.$t_build->braucht->fracht[$i]->anzahl.'</td></tr>';
                }
            }
            echo '</table>';
            echo '</td></tr>';
            echo '<tr><th>Produktion je Tick</th><td>';
            echo '<table>';
            for ($i = 0; $i < count($t_build->produziert->fracht); ++$i) {
                if ($t_build->produziert->fracht[$i]->anzahl > 0) {
                    echo '<tr><td>'.$t_build->produziert->fracht[$i]->name.'</td><td><img src="images/misc/'.
                    $t_build->produziert->fracht[$i]->bild.'" border="0" /></td><td>'.$t_build->produziert->fracht[$i]->anzahl.'</td></tr>';
                }
            }
            echo '</table>';
            echo '</td></tr>';

            echo '<tr><th>Effekt</th><td>';
            if ($t_build->lager > 0) {
                echo '+'.$t_build->lager.' Lager<br />';
            }
            if ($t_build->epslager > 0) {
                echo '+'.$t_build->epslager.' EPS-Lager<br />';
            }
            if ($t_build->schilde > 0) {
                echo '+'.$t_build->schilde.' Schilde<br />';
            }
            if ($t_build->laser > 0) {
                echo '+'.$t_build->laser.' Phaser<br />';
            }
            if ($t_build->sonstiges != '') {
                echo $t_build->sonstiges;
            }

            echo '</td></tr>';

            if ($t_build->forschung) {
                echo '<tr><th>Forschung</th><td>';
                $bu = new Button('forschung.php?pid='.$pid.'&fid='.$fid, 'Forschungsliste');
                $bu->printme();
                echo '</td></tr>';
            }

            echo '</table>';
        }
    }
    // --"-- ships?

    echo '</td></tr>';
    echo '<tr><th>Forschungsdauer</th><td>'.$f->dauer.' Ticks</td>';

    echo '</table><br />';

    $bu = new Button('forschung.php?pid='.$pid.'&fid='.$fid.'&dorid='.$f->id, $f->name.' erforschen');
    $bu->printme();
    echo '<br />';
    $bu = new Button('forschung.php?pid='.$pid.'&fid='.$fid, 'zurück zur Forschungsliste');
    $bu->printme();

    echo '</div>';
} else {
    echo '<h2>Forschungsliste</h2>
    
Mit Hilfe der Forschung ist es dir möglich verschiedene neue Gebäude und Schiffe freizuschalten. Neue Technologien und Kolonisationstechniken sind ebenfalls erforschbar. Die Forschung benötigt Isochips.<br /><br />';

    echo 'Verfügbar: '.$planet->frachtraum->fracht[4]->anzahl.' <img src="images/misc/'.$planet->frachtraum->fracht[4]->bild.'" border="0" /> '.$planet->frachtraum->fracht[4]->name;

    echo '<br /><br />';

    $bu = new Button('forschung.png', 'Forschungsbaum ansehen');
    $bu->printme();

    echo '<br /><br /><table class="invitetable" style="text-align:center;">
    <tr><th>Name</th><th>Kosten</th><th>Details</th></tr>';

    $l = Forschungen::getList();

    for ($i = 0; $i < count($l); ++$i) {
        $mapped = new MappedForschungen($_SESSION['Id'], $l[$i]->id);
        if ($mapped->status == 1) {
            echo '<tr><td><span style="color:green;">'.$l[$i]->name.'</span></td>';
        } elseif ($mapped->status > 1) {
            echo '<tr><td><span style="color:red;">'.$l[$i]->name.'</span></td>';
        } else {
            $ready = true;
            for ($j = 0; $j < count($l[$i]->pre); ++$j) {
                $t_mapped = new MappedForschungen($_SESSION['Id'], $l[$i]->pre[$j]->id);
                if ($t_mapped->status != 1) {
                    $ready = false;
                }
            }
            if ($ready) {
                echo '<tr><td><span style="color:yellow;">'.$l[$i]->name.'</span></td>';
            } else {
                echo '<tr><td><span style="color:grey;">'.$l[$i]->name.'</span></td>';
            }
        }
        echo '<td>'.$l[$i]->kosten.'</td><td><a href="forschung.php?pid='.$pid.'&fid='.$fid.'&rid='.$l[$i]->id.'">ansehen</a></td></tr>';
    }

    echo '</table>';
}

include 'foot.php';
