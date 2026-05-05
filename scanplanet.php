<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';
include_once 'auth.php';
$userId = requireLogin();
$sid = requireIntParam('sid');
$tid = requireIntParam('tid');

$schiff = new Schiffe($sid);
$target = new Planeten($tid);

$verbindung = get_verbindung();

$oflaeche = $target->typ[1] == 'm' && strlen($target->typ) == 2 ? 24 : 50;

$stunde = date('H');
    // QUESTABFRAGE

    $abfrage = mysqli_query($verbindung, "SELECT E.id FROM erfolge E,quests Q WHERE E.qid=Q.id AND Q.typ=9 AND E.erledigt=0 AND Q.zusatz='".$target->typ."'");
    while ($row = mysqli_fetch_array($abfrage)) {
        $qst = new Quest($row[0]);
        $qst->plus();
        $qst->done();
        echo 'Du hast einen (Teil)-Erfolg fuer eine Quest erreicht!<br />';
    }

    // ENDE QUESTS

    if ($target->besitzer->id == 3) {
        $npcware = 'Latinum';
        $npcname = 'Handelsturm';
        $npcbild = 'npcferg';
    }
    // if($target->besitzer->id==4) { $npcware='Vinkulum'; $npcname='Handelsturm'; $npcbild='npcferg'; }
    if ($target->besitzer->id == 5) {
        $npcware = 'Chateau Picard';
        $npcname = 'F&ouml;derationsrat';
        $npcbild = 'npcfod';
    }
    if ($target->besitzer->id == 6) {
        $npcware = 'Ale';
        $npcname = 'Imperialer Senat';
        $npcbild = 'npcrom';
    }
    if ($target->besitzer->id == 7) {
        $npcware = 'Blutwein';
        $npcname = 'Grosse Halle';
        $npcbild = 'npckling';
    }
    if ($target->besitzer->id == 8) {
        $npcware = 'Taspar Eier';
        $npcname = 'Detapa-Rat';
        $npcbild = 'npccard';
    }

    if ($schiff->position->x == $target->position->x && $schiff->position->y == $target->position->y && $schiff->position->orbit == 1 && $schiff->position->system->id == $target->position->system->id) {
        echo '<table class="bordered">';

        for ($i = 1; $i <= $oflaeche; ++$i) {
            if (($i == 1 || $i == 21 || $i == 31 || $i == 41 || $i == 11) && $oflaeche == 50) {
                echo '<tr>';
            }
            if (($i == 1 || $i == 7 || $i == 13 || $i == 19) && $oflaeche == 24) {
                echo '<tr>';
            }
            
                $bild = $target->feld[$i]->bild;
				$mouse = $target->feld[$i]->name;
                
                echo '<td><img src="images/buildings/',$bild,'" border="0" onmouseover="Tip(\'<b>',$mouse,'</b>\')" onmouseout="UnTip()" /></td>';
            
           
            

            if ($i % 10 == 0 && $oflaeche == 50) {
                echo '</tr>';
            }
            if ($i % 6 == 0 && $oflaeche == 24) {
                echo '</tr>';
            }
        }
        echo '</table><hr />';
        echo '<br /><table class="bordered2">';
        $inhalt = ['rohstoffa', 'rohstoffb', 'rohstoffc', 'rohstoffd', 'isochips', 'tritanium', 'dili', 'antimaterie', 'deuterium', 'npcborg', 'npcrom', 'npcfer', 'npcfod', 'npckling', 'npccard'];
        $inhaltcap = ['Baustoff', 'Duranium', 'Erz', 'Sorium', 'Isochips', 'Tritanium', 'Dilithium', 'Antimaterie', 'Deuterium', 'Vinkulum', 'Ale', 'Latinum', 'Ch�teau Picard', 'Blutwein', 'Taspar Eier'];
        $inhaltimg = ['baustoff.png', 'duranium.png', 'erz.png', 'sorium.png', 'isochips.png', 'tritanium.png', 'dili.png', 'antimaterie.png', 'deuterium.png', 'vinkulum.png', 'ale.png', 'latinum.png', 'chateau.png', 'blutwein.png', 'eier.png'];
        for ($i = 0; $i < count($inhalt); ++$i) {
            if ($target->$inhalt[$i] > 0) {
                echo '<tr><td>',$inhaltcap[$i],'</td><td><img src="images/misc/',$inhaltimg[$i],'" border="0" /></td><td>',$target->$inhalt[$i],'</td></tr>';
            }
        }
        echo '</table>';
        echo '<br /><br />';
        $but = new Button('schiffe.php?sid='.$sid, 'zur&uuml;ck zum Schiff');
        $but->printme();
    }
}
include 'foot.php';
