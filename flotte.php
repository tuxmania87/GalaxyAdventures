<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

die("Flottenverwaltung deaktiviert!");
include_once 'auth.php';
requireLogin();
$fid = requireIntParam('fid');
if (isset($_GET['do']) && !ctype_digit($_GET['do']) && $_GET['do'][0] != '6') {
    exit('Fehler: Ungültiger Parameter.');
}
$selfid = $_SESSION["Id"];
$ich = new Account($_SESSION["Id"]);
$fid = $_GET["fid"];



if ($fid == 0) {

    $testfrage = mysqli_query($verbindung, "SELECT * FROM flotte WHERE besitzer='$selfid'");
    while ($flott = mysqli_fetch_array($testfrage))
        echo 'Flotte: ', $flott["name"], ' --> <a href="flotte.php?fid=', $flott["id"], '">Flotte ausw&auml;hlen</a><br />';
} else {

    $schiffe = array();

    $abfrage = mysqli_query($verbindung, "SELECT id FROM schiffe WHERE flotte='$fid'");
    while ($t1 = mysqli_fetch_array($abfrage))
        array_push($schiffe, $t1["id"]);

    $prufbool = true;
    $deutbool = false;
    for ($i = 0; $i < count($schiffe); $i++) {
        $pruf = new Schiffe($schiffe[$i]);
        if ($pruf->besitzer->id != '2')
            $prufbool = false;
    }

    if ($prufbool) {
        echo 'Alle deine Schiffe wurden vernichtet!';
        mysqli_query($verbindung, "DELETE FROM flotte WHERE id=',$fid,'");
        mysqli_query($verbindung, "DELETE FROM flotte WHERE id='" . $fid . "'");
        mysqli_query($verbindung, "UPDATE schiffe SET flotte=0 WHERE flotte='$fid'");
    } else {




        $opferid = $_POST["opferid"];
        $opfertyp = $_POST["opfertyp"];
        if ($opferid != '') { // ballern!
            $enemy = $opfertyp == 'planet' ? new Planeten($opferid) : new Schiffe($opferid);
            $enemyid = $enemy->besitzer->id;
            if ($ich->level > 3 && $enemy->besitzer->level > 3) {
                /* logbuch eintrag

                  $xx=new schiff($schiffe[0]);
                  $text="Flotte von ".($xx->besitzer)." greift dich in Sektor ".$xx->x."|".$xx->y." an!";
                  $wer=$xx->besitzer; $wen=$enemy->besitzer; $wann=date("Y-m-d H:i:s");

                  mysqli_query($verbindung, "INSERT INTO logbuch (was,wann,wer,wen) VALUES ('$text','$wann','$wer','$wen')") or die(mysqli_error($verbindung));
                 */
//HINSCHIESSEN
                echo "<h3>Angriff</h3>";
                for ($i = 0; $i < count($schiffe); $i++) {
                    $schiff = new Schiffe($schiffe[$i]);
                    $schiff->feuern($enemy, 1);
                }
//zurückschiessen
                $enemyf = array();
                $enemyfp = array();
                $fabfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE system='" . $schiff->position->system->id . "' AND x='" . $schiff->position->x . "' AND y='" . $schiff->position->y . "' AND orbit='" . $schiff->position->orbit . "' AND besitzer!='" . $schiff->besitzer->id . "' AND energie>0 AND phaser<maxphaser AND laser>0");
                while ($frow = mysqli_fetch_array($fabfrage)) {
                    $tschiff = new Schiffe($frow["id"]);
                    if (!in_array($schiff->besitzer->id, $tschiff->besitzer->vertrag("nap")) && !in_array($schiff->besitzer->id, $tschiff->besitzer->vertrag("frieden")) && (($tschiff->besitzer->allianz->id > 0 && $tschiff->besitzer->allianz->id != $schiff->besitzer->allianz->id) || $tschiff->besitzer->allianz->id == 0) && ((in_array($enemy->besitzer->id, $tschiff->besitzer->vertrag("verteidigung"))) || ($enemy->besitzer->allianz->id == $tschiff->besitzer->allianz->id && $tschiff->besitzer->allianz->id > 0) || ($enemy->besitzer->id == $tschiff->besitzer->id)))
                        $enemyf[] = $frow["id"];
                }

                $fabfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE system='" . $schiff->position->system->id . "' AND x='" . $schiff->position->x . "' AND y='" . $schiff->position->y . "' AND besitzer!='" . $schiff->besitzer->id . "' AND energie>0  AND laser>0");
                while ($frow = mysqli_fetch_array($fabfrage)) {
                    $tschiff = new Planeten($frow["id"]);
                    if ($tschiff->position->orbit == 1 && !in_array($schiff->besitzer->id, $tschiff->besitzer->vertrag("nap")) && !in_array($schiff->besitzer->id, $tschiff->besitzer->vertrag("frieden")) && (($tschiff->besitzer->allianz->id > 0 && $tschiff->besitzer->allianz->id != $schiff->besitzer->allianz->id) || $tschiff->besitzer->allianz->id == 0) && ((in_array($enemy->besitzer->id, $tschiff->besitzer->vertrag("verteidigung"))) || ($enemy->besitzer->allianz->id == $tschiff->besitzer->allianz->id && $tschiff->besitzer->allianz->id > 0) || ($enemy->besitzer->id == $tschiff->besitzer->id)))
                        $enemyfp[] = $frow["id"];
                }

                echo "<br /><h3>Gegenangriff</h3>";
//PLANETN ballern ZURUECL
                for ($i = 0; $i < count($enemyfp); $i++) {
                    $x = 0;
                    $abort = false;
                    $planet = new Planeten($enemyfp[$i]);
                    $aua = new Schiffe($schiffe[$x]);
                    while ($aua->besitzer->id == 2 && !$abort) {
                        $x++;
                        if ($x >= count($schiffe))
                            $abort = true;
                        $aua = new Schiffe($schiffe[$x]);
                    }
                    if (!$abort)
                        $planet->feuern($aua, 1);
                }


//SCHIFFE BALLERN zuruecl
                for ($i = 0; $i < count($enemyf); $i++) {
                    $x = 0;
                    $abort = false;
                    $schiff = new Schiffe($enemyf[$i]);
                    $aua = new Schiffe($schiffe[$x]);
                    while ($aua->besitzer->id == 2 && !$abort) {
                        $x++;
                        if ($x >= count($schiffe))
                            $abort = true;
                        $aua = new Schiffe($schiffe[$x]);
                    }
                    if (!$abort)
                        $schiff->feuern($aua, 1);
                }

//ende zurück
            }
        }


//schiff hinzuefegen
        if ($_POST["do"] == 30) {
            $addid = $_POST["addid"];
            if (!ctype_digit($addid))
                die("Fehler ( 70 )");
            $testschiff = new Schiffe($addid);
            if ($testschiff->besitzer->id == $_SESSION["Id"]) {
                mysqli_query($verbindung, "UPDATE schiffe SET flotte='$fid' WHERE id='$addid'");
                $schiffe[] = $addid;
            }
        }
//form action="flotte.php?fid=2" method="post"><td><input type="hidden" name="do" value="30"><input type="hidden" name="addid" value="8"><input type="submit" value="hinzuf&uuml;gen"  ></td></form>
//ende schiffe hinzufügen
//schiff entfernen
        if ($_POST["do"] == 31) {
            $delid = $_POST["delid"];
            if (!ctype_digit($delid))
                die("Fehler ( 70 )");
            $testschiff = new Schiffe($delid);
            if ($testschiff->besitzer->id == $_SESSION["Id"]) {
                mysqli_query($verbindung, "UPDATE schiffe SET flotte='0' WHERE id='$delid'");
                $neufeld = array();
                for ($i = 0; $i < count($schiffe); $i++)
                    if ($schiffe[$i] != $delid)
                        $neufeld[] = $schiffe[$i];
                $schiffe = $neufeld;
            }
        }


//<form action="flotte.php?fid=2" method="post"><td><input type="hidden" name="do" value="31"><input type="hidden" name="delid" value="8"><input type="submit" value="entfernen"></td></form>

        if (count($schiffe) == 0) {
            mysqli_query($verbindung, "DELETE FROM flotte WHERE id='" . $fid . "'");
            mysqli_query($verbindung, "UPDATE schiffe SET flotte=0 WHERE flotte='$fid'");
            die("Flotte existiert nicht (mehr).");
        }
//ende schiff entfernen

        if ($_GET["do"] == 3)  // Schilde aktivieren
            for ($i = 0; $i < count($schiffe); $i++) {
                $schiff = new Schiffe($schiffe[$i]);
                if ($schiff->energie > 0 && $schiff->schilde > 0 && $schiff->schildstatus == 0) {
                    $schiff->energie--;
                    $schiff->schildstatus = 1;
                    mysqli_query($verbindung, "UPDATE schiffe SET energie='" . $schiff->energie . "',schildstatus='" . $schiff->schildstatus . "',schilde='" . $schiff->schilde . "' WHERE id='" . $schiff->id . "'");
                }
            }

        if ($_GET["do"] == 4)  // Schilde deaktivieren
            for ($i = 0; $i < count($schiffe); $i++) {
                $schiff = new Schiffe($schiffe[$i]);
                $schiff->schildstatus = 0;
                mysqli_query($verbindung, "UPDATE schiffe SET schildstatus='0' WHERE id='" . $schiff->id . "'");
            }



        if ($_GET["do"] == 2 || $_POST["do"] == 2) {//navigieren
            $direction = $_GET["dir"];
            $navbool = true;
            for ($i = 0; $i < count($schiffe); $i++) {
                //var_dump($schiffe[$i]); echo '<br />';
                $schiff = new Schiffe($schiffe[$i]);
                $foo = $schiff->navigieren($direction, true, 1);
                if ($foo != 0)
                    $navbool = false;
            }
            if ($navbool) {
                for ($i = 0; $i < count($schiffe); $i++) {
                    $schiff = new Schiffe($schiffe[$i]);
                    $foo = $schiff->navigieren($direction, true, 0);
                    //echo $schiff->fehler[$foo];
                }
                //zurueckfeuern
                $schiff = new Schiffe($schiffe[$i]);
                $schiff->kampftick("0", null);
            } else
                echo '<span style="color:red;font-weight:bold;">Ein Schiff in der Flotte hat keine Energie mehr / Gondeln &uuml;berhitzt!</span><br />';
        }

        if ($_GET["do"] == 5) { // energie aufteilen
            $gesamtE = 0;
            for ($i = 0; $i < count($schiffe); $i++) {
                $sch = new Schiffe($schiffe[$i]);
                $gesamtE+=$sch->energie;
            }
            echo "Gesamtenergie: " . $gesamtE;
            $mengeS = floor($gesamtE / count($schiffe));
            echo '. Teile jedem Schiff ', $mengeS, ' Energie zu. Restenergie: ', $gesamtE - (count($schiffe) * $mengeS), '<br />';
//ueberschreitung?
            $overflow = false;
            $neuMenge = 0;
            for ($i = 0; $i < count($schiffe); $i++) {
                $sch = new Schiffe($schiffe[$i]);
                if ($sch->maxenergie < $mengeS) {
                    $neuMenge = $sch->maxenergie;
                    $overflow = true;
                    echo '<span style="color:yellow;">Warnung: Das Schiff ', $sch->name, ' (', $sch->id, ') hat nur maximal ', $sch->maxenergie, ' Energie Speicher</span><br />';
                    $mengeS = $neuMenge;
                }
            }

//verteilen
            for ($i = 0; $i < count($schiffe); $i++) {
                mysqli_query($verbindung, "UPDATE schiffe SET energie='" . $mengeS . "' WHERE id='" . $schiffe[$i] . "'");
                $gesamtE-=$mengeS;
            }
            echo 'Restenergie: ', $gesamtE, '<br />';
            $i = 0;
            while ($gesamtE > 0 && $i < count($schiffe)) {
                $sch = new Schiffe($schiffe[$i]);
                $puffer = $sch->maxenergie - $sch->energie;
                if ($puffer > $gesamtE) {
                    echo $sch->name, ' (', $sch->id, ') erh&auml;lt ', $gesamtE, ' Energie<br />';
                    $puffer = 0;
                    mysqli_query($verbindung, "UPDATE schiffe SET energie=energie+" . $gesamtE . " WHERE id='" . $sch->id . "'");
                    $gesamtE = 0;
                }
                if ($puffer > 0) {
                    $gesamtE-=$puffer;
                    echo $sch->name, ' (', $sch->id, ') erh&auml;lt ', $puffer, ' Energie<br />';
                    mysqli_query($verbindung, "UPDATE schiffe SET energie=energie+" . $puffer . " WHERE id='" . $sch->id . "'");
                }
            }
        }  //ende e aufteile
//Alarmstufen
        if ($_GET["do"][0] == 6) {
            for ($i = 0; $i < count($schiffe); $i++) {
                $sch = new Schiffe($schiffe[$i]);
                if ($_GET["do"] == '6g')
                    $sch->alarmstufe = 'green';
                if ($_GET["do"] == '6y')
                    $sch->alarmstufe = 'yellow';
                if ($_GET["do"] == '6r')
                    $sch->alarmstufe = 'red';
                mysqli_query($verbindung, "UPDATE schiffe SET alarmstufe='$sch->alarmstufe' WHERE id='$sch->id'");
            }
        }
//ende alarmstufen	
//deut einsaugen 7 (8) 
        if ($_POST["do"] == 7) {
            for ($i = 0; $i < count($schiffe); $i++) {
                $sch = new Schiffe($schiffe[$i]);
                if ($sch->skill->deuterium == 1 && $sch->energie > 0) {
                    $deutanzahl = $_POST["deutanzahl"];
                    echo 'Schiff: ', $sch->name, ' (', $sch->id, '): ';
                    $sch->einsaugen('deuterium', $deutanzahl);
                }
            }
        }
//endedeut
//deut einsaugen (8) 
        if ($_POST["do"] == 8) {
            for ($i = 0; $i < count($schiffe); $i++) {
                $sch = new Schiffe($schiffe[$i]);
                if ($sch->skill->deuterium == 1 && $sch->energie > 0) {
                    $deutanzahl = $_POST["deutanzahl"];
                    echo 'Schiff: ', $sch->name, ' (', $sch->id, '): ';
                    $sch->einsaugen('deuteriumk', $deutanzahl);
                }
            }
        }
//endedeut
//deut einsaugen 9 (10) 
        if ($_POST["do"] == 9) {
            for ($i = 0; $i < count($schiffe); $i++) {
                $sch = new Schiffe($schiffe[$i]);
                if ($sch->skill->erz == 1 && $sch->energie > 0) {
                    $deutanzahl = $_POST["erzanzahl"];
                    echo 'Schiff: ', $sch->name, ' (', $sch->id, '): ';
                    $sch->einsaugen('erz', $deutanzahl);
                }
            }
        }
//endedeut
//deut einsaugen 910) 
        if ($_POST["do"] == 10) {
            for ($i = 0; $i < count($schiffe); $i++) {
                $sch = new Schiffe($schiffe[$i]);
                if ($sch->skill->erz == 1 && $sch->energie > 0) {
                    $deutanzahl = $_POST["erzanzahl"];
                    echo 'Schiff: ', $sch->name, ' (', $sch->id, '): ';
                    $sch->einsaugen('erzk', $deutanzahl);
                }
            }
        }
//endedeut


        $schiff = new Schiffe($schiffe[0]);

        if ($_GET["do"] == 20)   //flotte loeschen
            if ($schiff->besitzer->id == $_SESSION["Id"]) {
                mysqli_query($verbindung, "UPDATE schiffe SET flotte=0 WHERE flotte='" . $fid . "' AND besitzer='" . intval(\$_SESSION["Id"]) . "'");
                mysqli_query($verbindung, "DELETE FROM flotte WHERE id='" . $fid . "'");
                die("zur <a href=\"flotte.php?fid=0\">Flotten&uuml;bersicht</a>");
            }

        echo '<table class="bordered">';
        for ($i = -2 - $schiff->lrs - 2; $i <= 2 + $schiff->lrs + 2; $i++) {
            echo '<tr>';
            if ($i == 0)
                echo '<form action="flotte.php?do=2&dir=l&fid=', $fid, '" method="post"><td><input type="hidden" name="oldx" value="', $schiff->position->x, '" /><input type="hidden" name="do" value="2" /><input type="hidden" name="oldy" value="', $schiff->position->y, '" /><input type="image" src="images/misc/links.png" border="0" /></td></form>'; else
                echo '<td></td>';
            for ($j = -2 - $schiff->lrs; $j <= 2 + $schiff->lrs; $j++) {
//	if($j==2+$schiff->lrs+1 && $i==0) echo '<td></td><td>right</td>';
//	if($j==2+$schiff->lrs+1 && $i!=0) echo '<td></td>';
                $dir = false;

                if ($i == -2 - $schiff->lrs - 2 && $j == -2 - $schiff->lrs)
                    echo '<td></td>';
                if ($j == -2 - $schiff->lrs && $i >= -2 - $schiff->lrs && $i <= 2 + $schiff->lrs)
                    echo '<td>', $i + $schiff->position->y, '</td>';
                if ($j == -2 - $schiff->lrs && $i == -2 - $schiff->lrs - 1)
                    echo '<td>x/y</td>';
                if ($i == -2 - $schiff->lrs - 2) {
                    if ($j != 0)
                        echo '<td></td>'; else
                        echo '<form action="flotte.php?do=2&dir=o&fid=', $fid, '" method="post"><td><input type="hidden" name="oldx" value="', $schiff->position->x, '" /><input type="hidden" name="do" value="2" /><input type="hidden" name="oldy" value="', $schiff->position->y, '" /><input type="image" src="images/misc/oben.png" border="0" /></td></form>'; $dir = true;
                }
                if ($i == 2 + $schiff->lrs + 2) {
                    if ($j != 0)
                        echo '<td></td>'; else
                        echo '<td></td><form action="flotte.php?do=2&dir=u&fid=', $fid, '" method="post"><td><input type="hidden" name="oldx" value="', $schiff->position->x, '" /><input type="hidden" name="do" value="2" /><input type="hidden" name="oldy" value="', $schiff->position->y, '" /><input type="image" src="images/misc/unten.png" border="0" /></td></form>'; $dir = true;
                }
                if ($i == 2 + $schiff->lrs + 1 && $j == -2 - $schiff->lrs)
                    echo '<td></td>';
                if ($i == 2 + $schiff->lrs + 1) {
                    echo '<td>', $j + $schiff->position->x, '</td>';
                    $dir = true;
                }
                if ($i == -2 - $schiff->lrs - 1) {
                    echo '<td>', $j + $schiff->position->x, '</td>';
                    $dir = true;
                }

                if (!$dir) {
                    $bild = 'weltraum.jpg';
                    $counter = 0;
                    $tmpx = $schiff->position->x + $j;
                    $tmpy = $schiff->position->y + $i;
                    $ttip = "";
                    $abfrage = mysqli_query($verbindung, "SELECT * FROM weltraum WHERE x='$tmpx' AND y='$tmpy' AND system='" . $schiff->position->system->id . "'");
                    while ($tmp = mysqli_fetch_array($abfrage)) {

                        if ($tmp["typ"] == 'h')
                            $bild = 'hstation.jpg';
                        if ($tmp["typ"] == 't')
                            $bild = 'spalte.jpg';
                        if ($tmp["typ"] == 'd')
                            $bild = 'deut.jpg';
                        if ($tmp["typ"] == 'dk')
                            $bild = 'deutklein.jpg';
                        if ($tmp["typ"] == 'b')
                            $bild = 'nebel.jpg';
                        if ($tmp["typ"] == 'g')
                            $bild = 'green.jpg';
                        if ($tmp["typ"] == 'e')
                            $bild = 'erz.jpg';
                        if ($tmp["typ"] == 'ek')
                            $bild = 'erzklein.jpg';
                        if ($tmp["typ"] == 'p')
                            $bild = 'pulsar.jpg';
                        if ($tmp["typ"] == 'w')
                            $bild = 'wurmloch.jpg';
                        if ($tmp["typ"] == 'x')
                            $bild = 'black.jpg';
                        if ($tmp["typ"] == 'rot')
                            $bild = 'rot.jpg';
                        if ($tmp["typ"] == 'gelb')
                            $bild = 'gelb.jpg';
                        if ($tmp["typ"] == 'weiss')
                            $bild = 'weiss.jpg';
                        if ($tmp["typ"] == 'blau')
                            $bild = 'blau.jpg';
                        if ($tmp["typ"] == 'orange')
                            $bild = 'orange.jpg';
                        if ($tmp["typ"] == 'r1')
                            $bild = 'rot1.jpg';
                        if ($tmp["typ"] == 'r2')
                            $bild = 'rot2.jpg';
                        if ($tmp["typ"] == 'r3')
                            $bild = 'rot3.jpg';
                        if ($tmp["typ"] == 'r4')
                            $bild = 'rot4.jpg';
                        if ($tmp["typ"] == 'b1')
                            $bild = 'blau1.jpg';
                        if ($tmp["typ"] == 'b2')
                            $bild = 'blau2.jpg';
                        if ($tmp["typ"] == 'b3')
                            $bild = 'blau3.jpg';
                        if ($tmp["typ"] == 'b4')
                            $bild = 'blau4.jpg';
                        if ($tmp["typ"] == 'rs')
                            $bild = 'rotstell.jpg';
                        if ($tmp["typ"] == 'bs')
                            $bild = 'blaustell.jpg';
                        if ($tmp["typ"] == 'lim')
                            $bild = 'limes.jpg';
                        if ($tmp["typ"] == 'metrion')
                            $bild = 'metrion.jpg';
                        if ($tmp["typ"] == 'radio')
                            $bild = 'nebelgelb.jpg';


                        if ($tmp["typ"] == '')
                            $ttip = 'weltall';
                        if ($tmp["typ"] == 'x')
                            $ttip = 'black';
                        if ($tmp["typ"] == 'd')
                            $ttip = 'deut';
                        if ($tmp["typ"] == 'dk')
                            $ttip = 'deutklein';
                        if ($tmp["typ"] == 'e')
                            $ttip = 'erz';
                        if ($tmp["typ"] == 'ek')
                            $ttip = 'erzklein';
                        if ($tmp["typ"] == 'b')
                            $ttip = 'blau';
                        if ($tmp["typ"] == 'g')
                            $ttip = 'green';
                        if ($tmp["typ"] == 'p')
                            $ttip = 'pulsar';
                        if ($tmp["typ"] == 'gelb' || $tmp["typ"] == 'weiss' || $tmp["typ"] == 'rot')
                            $ttip = 'stern';
                        if ($tmp["typ"] == 'r1' || $tmp["typ"] == 'r2' || $tmp["typ"] == 'r3' || $tmp["typ"] == 'r4')
                            $ttip = "'<b>roter Riese</b><br />Einflug vernichtet Schiff!'";
                        if ($tmp["typ"] == 'rs' || $tmp["typ"] == 'bs')
                            $ttip = "'<b>Stellare Materie</b><br />Sensoren gestoert!";
                        if ($tmp["typ"] == 'b1' || $tmp["typ"] == 'b2' || $tmp["typ"] == 'b3' || $tmp["typ"] == 'b4')
                            $ttip = "'<b>blauer Riese</b><br />Einflug vernichtet Schiff!'";
                        if ($tmp["typ"] == 's') {
                            $newsys = new System($tmp["klasse"]);
                            $ttip = "'$newsys->name'";
                        }
                    }

                    $sysbild = "";

                    if ($schiff->position->system->id == 0) {
                        $abfragexy = mysqli_query($verbindung, "SELECT * FROM systeme WHERE x='$tmpx' AND y='$tmpy'");
                        while ($tmpxy = mysqli_fetch_array($abfragexy)) {
                            $gna = $tmpxy[4];
                            $sysbild = "" . $gna;
                            $ttip = "'" . $tmpxy["name"] . "'";
                        }
                    }

                    $abfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE x='$tmpx' AND y='$tmpy' AND system='" . $schiff->position->system->id . "'");
                    while ($tmp = mysqli_fetch_array($abfrage)) {
                        if ($tmp["typ"] == 'm')
                            if ($tmp["besitzer"] == 2)
                                $ttip = 'munbesiedelt'; else
                                $ttip = 'mbesiedelt';
                        if ($tmp["typ"] == 'l')
                            if ($tmp["besitzer"] == 2)
                                $ttip = 'lunbesiedelt'; else
                                $ttip = 'lbesiedelt';
                        if ($tmp["typ"] == 'i')
                            if ($tmp["besitzer"] == 2)
                                $ttip = 'iunbesiedelt'; else
                                $ttip = 'ibesiedelt';
                        if ($tmp["typ"] == 'z')
                            if ($tmp["besitzer"] == 2)
                                $ttip = 'zunbesiedelt'; else
                                $ttip = 'zbesiedelt';

                        $bild = $tmp["bild"];
                        if ($tmp["typ"] == 'om')
                            $bild = 'omond.jpg';
                        if ($tmp["typ"] == 'lm')
                            $bild = 'lmond.jpg';
                        if ($tmp["typ"] == 'mm')
                            $bild = 'mmond.jpg';
                        if ($tmp["typ"] == 'gasi')
                            $bild = 'gasi.jpg';
                        if ($tmp["typ"] == 'gasj')
                            $bild = 'gasj.jpg';
                        if ($tmp["typ"] == 'gass')
                            $bild = 'gass.jpg';
                    }
                    //Tooltip test
                    $abfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE x='$tmpx' AND y='$tmpy'  AND system='" . $schiff->position->system->id . "'");
                    while ($tmp = mysqli_fetch_array($abfrage))
                        $counter++;
                    if ($ttip == '')
                        $ttip = 'weltall';


                    //tooltip ende
                    if ($bild == 'nebel.jpg')
                        $counter = rand(0, 140);
                    echo '<td', $tmpx == $schiff->position->x && $tmpy == $schiff->position->y ? ' style="border:1pt solid #fa8f08;" ' : '', '>';
                    if ($schiff->position->system->id == 0)
                        echo '<a href="map.php?x=', $tmpx, '&y=', $tmpy, '" onmouseover="Tip(', $ttip, ')" onmouseout="UnTip()">';
                    if ($schiff->position->system->id > 0)
                        echo '<a href="map.php?system=', $schiff->position->system->id, '" onmouseover="Tip(', $ttip, ')" onmouseout="UnTip()">';
                    $neubild = $sysbild == '' ? 'images/misc/' . $bild : 'images/systems/' . $sysbild;
                    echo '<img src="', $counter == 0 ? $neubild : 'bilder.php?bild=' . $neubild . '&feld=' . $counter, '" border="0" /></a></td>';
                    if ($j == 2 + $schiff->lrs)
                        echo '<td>', $i + $schiff->position->y, '</td>';
                } // ende ifdir
            }
            if ($i == 0)
                echo '<form action="flotte.php?do=2&dir=r&fid=', $fid, '" method="post"><td><input type="hidden" name="oldx" value="', $schiff->position->x, '" /><input type="hidden" name="do" value="2" /><input type="hidden" name="oldy" value="', $schiff->position->y, '" /><input type="image" src="images/misc/rechts.png" border="0" /></td></form>'; else
                echo '<td></td>';
            echo '</tr>';
        }
        echo '</table><br /><table><tr><td style="vertical-align:top;"><table class="bordered"><tr><td>Display</td><td>Name</td><td>Hull</td><td>Schilde</td><td>Energie</td><td>Gondeln</td></tr>';

        $string1 = "";

        for ($i = 0; $i < count($schiffe); $i++) {
            $schiff = new Schiffe($schiffe[$i]);
            $string1.=" AND id!=" . $schiffe[$i];

            echo '<tr><td><a href="schiffe.php?sid=', $schiff->id, '"><img src="', $schiff->bild, '" border="0" /></a></td><td>', $schiff->name, '(', $schiff->id, ')</td><td>', $schiff->hull, '/', $schiff->maxhull, '</td><td>', $schiff->schildstatus == 1 ? '<span style="color:yellow;">' : '<span>', $schiff->schilde, '/', $schiff->maxschilde, '</span></td><td>', $schiff->energie, '/', $schiff->maxenergie, '</td><td>', $schiff->gondeln, '/', $schiff->maxgondeln, '</td><form action="flotte.php?fid=', $fid, '" method="post"><td><input type="hidden" name="do" value="31"><input type="hidden" name="delid" value="', $schiff->id, '"><input type="submit" value="entfernen"></td></form></tr>';
        }
        echo '</table>';

        echo 'Alarmstufe:<br />';
//alarmfeld
        $alarmfeld = array();
        for ($i = 0; $i < count($schiffe); $i++) {
            $sch = new Schiffe($schiffe[$i]);
            $alarmfeld[] = $sch->alarmstufe;
        }
        $alarmcheck = false;
        $alarmstufe = $alarmfeld[0];
        for ($i = 0; $i < count($alarmfeld); $i++)
            if ($alarmfeld[$i] != $alarmstufe)
                $alarmcheck = true;


        if ($schiff->alarmstufe == 'green' && $alarmcheck === false)
            echo '<img src="images/misc/alarmgg.png" border="0" />'; else
            echo '<a href="flotte.php?fid=', $fid, '&do=6g"><img src="images/misc/alarmgk.png" border="0" /></a>';
        if ($schiff->alarmstufe == 'yellow' && $alarmcheck === false)
            echo '<img src="images/misc/alarmyg.png" border="0" />'; else
            echo '<a href="flotte.php?fid=', $fid, '&do=6y"><img src="images/misc/alarmyk.png" border="0" /></a>';
        if ($schiff->alarmstufe == 'red' && $alarmcheck === false)
            echo '<img src="images/misc/alarmrg.png" border="0" />'; else
            echo '<a href="flotte.php?fid=', $fid, '&do=6r"><img src="images/misc/alarmrk.png" border="0" /></a>';
        echo '<br />';


        echo '<br />';
        $bu = new Button("flotte.php?do=3&fid=" . $fid, "Schilde aktivieren");
        $bu->printme();
        echo '<br />';
        $bu = new Button("flotte.php?do=4&fid=" . $fid, "Schilde deaktivieren");
        $bu->printme();
        echo '<br /><br />';
        $bu = new Button("flotte.php?do=5&fid=" . $fid, "Energie verteilen");
        $bu->printme();
        echo '</td><td style="vertical-align:top;"><table class="bordered">';

        $nebel = false;
        $gegnerab = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE x='" . $schiff->position->x . "' AND y='" . $schiff->position->y . "' AND orbit='" . $schiff->position->orbit . "' AND typ='b'");
        while ($gegner = mysqli_fetch_array($gegnerab))
            $nebel = true;

//deutbool
        for ($po = 0; $po < count($schiffe); $po++) {
            $pruf = new Schiffe($schiffe[$po]);
            if ($pruf->skill->deuterium == 1)
                $deutbool = true;
            if ($pruf->skill->erz == 1)
                $erzbool = true;
        }

//endedeut bool

        $gegnerab = mysqli_query($verbindung, "SELECT * FROM weltraum WHERE (typ='dk' OR typ='d') AND system='" . $schiff->position->system->id . "' AND x='" . $schiff->position->x . "' AND y='" . $schiff->position->y . "'");
        while ($gegner = mysqli_fetch_array($gegnerab)) {
            if ($gegner["typ"] == 'd' && $deutbool)
                echo '<tr><form action="flotte.php?fid=', $fid, '" method="post"><td><img src="images/misc/deut.jpg" border="0" /></td><td>dichtes<br />Deuteriumfeld</td><td><input type="text" name="deutanzahl" size="6" value="(energie)" /></td><td><input type="submit" value="einsaugen" /><input type="hidden" name="do" value="7" /></td></form></tr>';
            if ($gegner["typ"] == 'dk' && $deutbool)
                echo '<tr><form action="flotte.php?fid=', $fid, '" method="post"><td><img src="images/misc/deutklein.jpg" border="0" /></td><td>d&uuml;nnes<br />Deuteriumfeld</td><td><input type="text" name="deutanzahl" size="6" value="(energie)" /></td><td><input type="submit" value="einsaugen" /><input type="hidden" name="do" value="8" /></td></form></tr>';
            if ($gegner["typ"] == 'd' && !$deutbool)
                echo '<tr><td><img src="images/misc/deut.jpg" border="0" /></td><td>dichtes<br />Deuteriumfeld</td>-<td></td><td>-</td></tr>';
            if ($gegner["typ"] == 'dk' && !$deutbool)
                echo '<tr><td><img src="images/misc/deutklein.jpg" border="0" /></td><td>d&uuml;nnes<br />Deuteriumfeld</td><td>-</td><td>-</td></tr>';
            echo '</tr>';
        }

        $gegnerab = mysqli_query($verbindung, "SELECT * FROM weltraum WHERE (typ='ek' OR typ='e') AND system='" . $schiff->position->system->id . "' AND x='" . $schiff->position->x . "' AND y='" . $schiff->position->y . "'");
        while ($gegner = mysqli_fetch_array($gegnerab)) {
            if ($gegner["typ"] == 'e' && $erzbool)
                echo '<tr><form action="flotte.php?fid=', $fid, '" method="post"><td><img src="images/misc/erz.jpg" border="0" /></td><td>dichtes<br />Asteroidenfeld</td><td><input type="text" name="erzanzahl" size="6" value="(energie)" /></td><td><input type="submit" value="abbauen" /><input type="hidden" name="do" value="9" /></td></form></tr>';
            if ($gegner["typ"] == 'ek' && $erzbool)
                echo '<tr><form action="flotte.php?fid=', $fid, '" method="post"><td><img src="images/misc/erzklein.jpg" border="0" /></td><td>d&uuml;nnes<br />Asteroidenfeld</td><td><input type="text" name="erzanzahl" size="6" value="(energie)" /></td><td><input type="submit" value="abbauen" /><input type="hidden" name="do" value="10" /></td></form></tr>';
            if ($gegner["typ"] == 'e' && !$erzbool)
                echo '<tr><td><img src="images/misc/erz.jpg" border="0" /></td><td>dichtes<br />Asteroidenfeld</td>-<td></td><td>-</td></tr>';
            if ($gegner["typ"] == 'ek' && !$erzbool)
                echo '<tr><td><img src="images/misc/erzklein.jpg" border="0" /></td><td>d&uuml;nnes<br />Asteroidenfeld</td><td>-</td><td>-</td></tr>';
            echo '</tr>';
        }


        $gegnerab = mysqli_query($verbindung, "SELECT * FROM planeten WHERE system='" . $schiff->position->system->id . "' AND x='" . $schiff->position->x . "' AND y='" . $schiff->position->y . "' $string1");
        while ($gegner = mysqli_fetch_array($gegnerab)) {
            $usr = new Account($gegner["besitzer"]);
            if ($gegner["typ"] == 'm' && $schiff->position->orbit == 0)
                echo '<tr><td><img src="images/misc/planet.gif" border="0" /></td><td>', $gegner["name"], '(', $gegner["id"], ')</td><td>', ($usr->nickname), '</td><td><a href="flotte.php?fid=', $fid, '&do=2&dir=v">IN ORBIT EINTRETEN</a></td></tr>';
            if ($gegner["typ"] == 'm' && $schiff->position->orbit == 1)
                echo '<tr><td><img src="images/misc/planet.gif" border="0" /></td><td>', $gegner["name"], '(', $gegner["id"], ')</td><td>', ($usr->nickname), '</td><td>', $gegner["schildstatus"] == 1 ? '<span style="color:yellow;">' : '<span>', $gegner["schilde"], '/', $gegner["maxschilde"], '</span></td>', $gegner["schildstatus"] == 1 ? '<form action="flotte.php?fid=' . $fid . '" method="post"><input type="hidden" name="opfertyp" value="planet"><td><input type="hidden" name="opferid" value="' . $gegner["id"] . '"><input type="submit" value="F"></td></form>' : '', '<td><a href="flotte.php?fid=', $fid, '&do=2&dir=v">AUS ORBIT AUSTRETEN</a></td></tr>';
//LAVA
            if ($gegner["typ"] == 'l' && $schiff->position->orbit == 0)
                echo '<tr><td><img src="images/misc/lava.gif" border="0" /></td><td>', $gegner["name"], '(', $gegner["id"], ')</td><td>', ($usr->nickname), '</td><td><a href="flotte.php?fid=', $fid, '&do=2&dir=v">IN ORBIT EINTRETEN</a></td></tr>';
            if ($gegner["typ"] == 'l' && $schiff->position->orbit == 1)
                echo '<tr><td><img src="images/misc/lava.gif" border="0" /></td><td>', $gegner["name"], '(', $gegner["id"], ')</td><td>', ($usr->nickname), '</td><td>', $gegner["schildstatus"] == 1 ? '<span style="color:yellow;">' : '<span>', $gegner["schilde"], '/', $gegner["maxschilde"], '</span></td>', $gegner["schildstatus"] == 1 ? '<form action="flotte.php?fid=' . $fid . '" method="post"><td><input type="hidden" name="opfertyp" value="planet"><input type="hidden" name="opferid" value="' . $gegner["id"] . '"><input type="submit" value="F"></td></form>' : '', '<td><a href="flotte.php?fid=', $fid, '&do=2&dir=v">AUS ORBIT AUSTRETEN</a></td></tr>';
//Eisplanet
            if ($gegner["typ"] == 'i' && $schiff->position->orbit == 0)
                echo '<tr><td><img src="images/misc/eisplanet.gif" border="0" /></td><td>', $gegner["name"], '(', $gegner["id"], ')</td><td>', ($usr->nickname), '</td><td><a href="flotte.php?fid=', $fid, '&do=2&dir=v">IN ORBIT EINTRETEN</a></td></tr>';
            if ($gegner["typ"] == 'i' && $schiff->position->orbit == 1)
                echo '<tr><td><img src="images/misc/eisplanet.gif" border="0" /></td><td>', $gegner["name"], '(', $gegner["id"], ')</td><td>', ($usr->nickname), '</td><td>', $gegner["schildstatus"] == 1 ? '<span style="color:yellow;">' : '<span>', $gegner["schilde"], '/', $gegner["maxschilde"], '</span></td>', $gegner["schildstatus"] == 1 ? '<form action="flotte.php?fid=' . $fid . '" method="post"><input type="hidden" name="opfertyp" value="planet"><td><input type="hidden" name="opferid" value="' . $gegner["id"] . '"><input type="submit" value="F"></td></form>' : '', '<td><a href="flotte.php?fid=', $fid, '&do=2&dir=v">AUS ORBIT AUSTRETEN</a></td></tr>';
//Wuesteplanet
            if ($gegner["typ"] == 'z' && $schiff->position->orbit == 0)
                echo '<tr><td><img src="images/misc/wuste.jpg" border="0" /></td><td>', $gegner["name"], '(', $gegner["id"], ')</td><td>', ($usr->nickname), '</td><td><a href="flotte.php?fid=', $fid, '&do=2&dir=v">IN ORBIT EINTRETEN</a></td></tr>';
            if ($gegner["typ"] == 'z' && $schiff->position->orbit == 1)
                echo '<tr><td><img src="images/misc/wuste.jpg" border="0" /></td><td>', $gegner["name"], '(', $gegner["id"], ')</td><td>', ($usr->nickname), '</td><td>', $gegner["schildstatus"] == 1 ? '<span style="color:yellow;">' : '<span>', $gegner["schilde"], '/', $gegner["maxschilde"], '</span></td>', $gegner["schildstatus"] == 1 ? '<form action="flotte.php?fid=' . $fid . '" method="post"><td><input type="hidden" name="opfertyp" value="planet"><input type="hidden" name="opferid" value="' . $gegner["id"] . '"><input type="submit" value="F"></td></form>' : '', '<td><a href="flotte.php?fid=', $fid, '&do=2&dir=v">AUS ORBIT AUSTRETEN</a></td></tr>';
        }

        $gegnerab = mysqli_query($verbindung, "SELECT * FROM systeme WHERE x='" . $schiff->position->x . "' AND y='" . $schiff->position->y . "' $string1");
        while ($gegner = mysqli_fetch_array($gegnerab)) {
//System
            if ($schiff->position->system->id == 0) {  //Eintreten
                $sys = new System($gegner["id"]);
                echo '<tr><td><img src="images/systems/', $sys->bild, '" border="0" /></td><td>', $sys->name, '-System (', $gegner["id"], ')</td><td>Niemand (2)</td><td><a href="flotte.php?fid=', $fid, '&do=2&dir=s">einfliegen</a></td></tr>';
            }
        }
        if ($schiff->position->system->id > 0) { // Austreten
            $sys = $schiff->position->system;
            echo '<tr><td><img src="images/systems/', $sys->bild, '" border="0" /></td><td>', $sys->name, '-System (', $sys->id, ')</td><td>Niemand (2)</td><td><a href="flotte.php?fid=', $fid, '&do=2&dir=s">ausfliegen</a></td></tr>';
        }

        $gegnerab = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE tarnung=0 AND system='" . $schiff->position->system->id . "' AND orbit='" . $schiff->position->orbit . "' AND x='" . $schiff->position->x . "' AND y='" . $schiff->position->y . "' $string1");
        while ($gegner = mysqli_fetch_array($gegnerab)) {
            if (!$nebel) {
                $tgegner = new Schiffe($gegner["id"]);
                if ($gegner["typ"] == 's' && $gegner["orbit"] == $schiff->position->orbit) {
                    echo '<tr><td>', $gegner["besitzer"] == $_SESSION["Id"] ? '<a href="schiffe.php?sid=' . $gegner["id"] . '">' : '', '<img src="', $gegner["bild"], '" border="0" />', $gegner["besitzer"] == $_SESSION["Id"] ? '</a>' : '', '</td><td>', $tgegner->name, '(', $gegner["id"], ')</td><td>', ($gegner["besitzer"]), '</td><td>', $gegner["hull"], '/', $gegner["maxhull"], '</td><td>', $gegner["schildstatus"] == 1 ? '<span style="color:yellow;">' : '<span>', $gegner["schilde"], '/', $gegner["maxschilde"], '</span></td>';

                    if ($gegner["besitzer"] == $_SESSION["Id"])
                        echo '<form action="flotte.php?fid=', $fid, '" method="post"><td><input type="hidden" name="do" value="30"><input type="hidden" name="addid" value="', $gegner["id"], '"><input type="submit" value="hinzuf&uuml;gen" ', $gegner["skillbase"] == 1 ? 'disabled="true"' : '', ' ></td></form>'; else
                        echo '<form action="flotte.php?fid=', $fid, '" method="post"><td><input type="hidden" name="opferid" value="', $gegner["id"], '"><input type="submit" value="F"></td></form>';
                    echo '</tr>';
                }
            }
        }


        echo '</table></td></tr></table>';
    }
}

if ($fid > 0)
    echo '<br /><hr /><a href="flotte.php?fid=', $fid, '&do=20"><span style="color:red;">Flotte aufl&ouml;sen</span></a>';


include("foot.php");
?> 
