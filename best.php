<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';
include_once 'connect.php';

$verbindung = get_verbindung();

function tausche(&$a, &$b)
{
    $h = $a;
    $a = $b;
    $b = $h;
}

$bu = new Button('best.php?kategorie=1', 'Wirtschaftsbestenliste');
$bu->printme();

echo '                 ';
$bu = new Button('best.php?kategorie=2', 'Militärische Bestenliste');
$bu->printme();
echo '<br />';

$idfeld = [];
$wirtschaft = [];
$army = [];
$kat = $_GET['kategorie'];
if ($kat == '' || $kat == 1) {
    echo '<br /><h3>Wirtschaftskraft</h3><table class="invitetable" style="text-align:center;"><tr><th>#</th><th>Spieler</th><th>Punkte</th></tr>';

    $i = 0;
    $abfrage = mysqli_query($verbindung, 'SELECT id,wpunkte FROM account WHERE id>9 ORDER BY wpunkte DESC');
    while ($row = mysqli_fetch_array($abfrage)) {
        ++$i;
        $tusr = new Account($row[0]);
        echo '<tr><td>',$i,'</td><td><a href="userinfo.php?id=',$tusr->id,'">',$tusr->nickname,'</td><td>',$row[1] / 1.0,'</a></td></tr>';
    }
    echo '</table>';
}

if ($kat == 2) {
    $playerresult = mysqli_query($verbindung, 'SELECT * FROM account WHERE id>9');
    while ($player = mysqli_fetch_array($playerresult)) {
        $id = $player['id'];
        $gesamt = 0;
        $sammelquery = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE besitzer='$id' AND typ='s'");
        while ($sammel = mysqli_fetch_array($sammelquery)) {
            if ($sammel['klasse'] == 'Oberth') {
                $gesamt += 2;
            }
            if ($sammel['klasse'] == 'Miranda') {
                $gesamt += 5;
            }
            if ($sammel['klasse'] == 'Constitution') {
                $gesamt += 10;
            }
        }
        if ($gesamt > 0) {
            $idfeld[] = $id;
            $army[] = $gesamt;
        }
    }

    $weiter = true;
    while ($weiter) {
        $weiter = false;
        for ($i = 0; $i < count($idfeld) - 1; ++$i) {
            if ($army[$i] < $army[$i + 1]) {
                tausche($army[$i], $army[$i + 1]);
                tausche($idfeld[$i], $idfeld[$i + 1]);
                $weiter = true;
            }
        }
    }
    echo '<br /><h3>Milit&auml;rische Kraft</h3><table class="invitetable" style="text-align:center;"><tr><th>#</th><th>Spieler</th><th>Punkte</th></tr>';
    for ($i = 0; $i < count($idfeld); ++$i) {
        $tusr = new Account($idfeld[$i]);
        echo '<tr><td>',$i + 1,'</td><td><a href="userinfo.php?id=',$idfeld[$i],'">',$tusr->nickname,'</a></td><td>', $army[$i],'</td></tr>';
    }
    echo '</table>';
}

include 'foot.php';
