<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

$verbindung = get_verbindung();

$sid = $_GET["sid"];
if (ctype_digit($sid)) {
    $schiff = new Schiffe($sid);
}
//CHEATSCHUTZ ANFANG

if (!isset($_SESSION["Id"]))
    die("Fehler: Session ist nicht vorhanden / Bitte neu einloggen!<br />"); else
    $ich = new Account($_SESSION["Id"]);
if (!ctype_digit($_GET["sid"]))
    die("Fehler: ID ist ung&uuml;tig!<br />");
if ($schiff->typ != 's')
    die("Fehler: TYP ist ung&uuml;tig!<br />");
if ($ich->id != $schiff->besitzer->id)
    die("Fehler: Besitzer-ID ist ung&uuml;tig!<br />");


//CHEATSCHUTZ ENDE
//CODE abhandlung
//name andern do==-1

if ($_POST["do"] == '-1') {
    $texte = $_POST["newname"];
    changeit($texte);
    $schiff->name = $texte;
    mysqli_query($verbindung, "UPDATE schiffe SET name='$schiff->name' WHERE id='$schiff->id'");
    $schiff->name = pruefetext($texte);
}

if ($_POST["do"] == 1337 && $schiff->besitzer->id == $_SESSION["Id"]) {

    $savetext = $_POST["message2"];
    changeit($savetext);
    $schiff->nachricht = $savetext;
    mysqli_query($verbindung, "UPDATE schiffe SET nachricht='" . $savetext . "' WHERE id='" . $schiff->id . "'");
}

//navigieren d0==7
if ((isset($_GET["x"]) && isset($_GET["y"])
        && ctype_digit($_GET["x"]) && ctype_digit($_GET["y"])) || $_POST["do"] == 7 || $_GET["do"] == 7) {
    $getx = $_GET["x"];
    $gety = $_GET["y"];
    $diffx = $schiff->position->x - $getx;
    $diffy = $schiff->position->y - $gety;

    if ($diffx != 0 && $diffy != 0 && $_POST["do"] != 7 && $_GET["do"] != 7) {
        echo "<span style=\"color:red;font-weight:bold;\">Du kannst nur geradlinig navigieren!</span>";
    } else {
        if ($diffx > 0) {
            $richtung = 'l';
            $anzahl = $diffx;
        }
        if ($diffx < 0) {
            $richtung = 'r';
            $anzahl = $diffx * -1;
        }
        if ($diffy > 0) {
            $richtung = 'o';
            $anzahl = $diffy;
        }
        if ($diffy < 0) {
            $richtung = 'u';
            $anzahl = $diffy * -1;
        }


        if ($_POST["do"] == 7) {
            $richtung = "v";
            $anzahl = 1;
        }

        if ($_GET["do"] == 7) {
            $richtung = "s";
            $anzahl = 1;
        }

        for ($i = 0; $i < $anzahl; $i++) {
            $foo = $schiff->navigieren($richtung, false, 0);
            echo $schiff->fehler[$foo];
        }
    }
}

$cur_feld = new Weltraum($schiff->position->x, $schiff->position->y, $schiff->position->system->id, $schiff->position->system->id > 0);

//self destruct
if ($_POST["do"] == 100) {
    $schiff->zerstoerung();
    die("Schiff wurde erfolgreich zerst&ouml;rt!");
}

if ($_GET["do"] == 64) {
    if ($schiff->warpkern == 0)
        echo "Der Warpkern muss erst geladen werden!<br />";
    else {
        $schiff->warpkernstatus = 1 - $schiff->warpkernstatus;
        mysqli_query($verbindung, "UPDATE schiffe SET warpkernstatus=1-warpkernstatus WHERE id='$schiff->id'");
        if ($schiff->warpkernstatus == 1) {
            echo "<span class=\"success\">Warpkern wurde aktiviert!</span><br />";
        } else {
            echo "<span class=\"error\">Warpkern wurde deaktiviert!</span><br />";
        }
    }
}
//looten
if ($_GET["do"] == 15)
    if (ctype_digit($_GET["tid"])) {
        $shp = new Schiffe($_GET["tid"]);
        echo $schiff->fehler[($schiff->looten($shp))];
    }


// wurmloch 
if ($_POST["do"] == 913 && $cur_feld->feld->wurmloch) {
    $schiff->position->x = $cur_feld->ziel->x;
    $schiff->position->y = $cur_feld->ziel->y;
    $schiff->position->system = new System($cur_feld->ziel->system);
    $cur_feld = new Weltraum($schiff->position->x, $schiff->position->y, $schiff->position->system->id, $schiff->position->system->id > 0);
    mysqli_query($verbindung, "UPDATE schiffe SET x='" . $schiff->position->x . "',y='" . $schiff->position->y . "',`system`='" . $schiff->position->system->id . "' WHERE id='" . $schiff->id . "'");
}

//schilde anmachen
if ($_POST["do"] == 4) {
    echo $schiff->fehler[($schiff->schilde())];
}

//einsaugen deut
if ($_POST["do"] == 5) {
    $deutanzahl = $_POST["deutamount"];
    echo $schiff->fehler[($schiff->einsaugen('deuterium', $deutanzahl))];
}


//einsaugen erz
if ($_POST["do"] == 13) {
    $deutanzahl = $_POST["erzamount"];
    echo $schiff->fehler[($schiff->einsaugen('erz', $deutanzahl))];
}

//docken
if ($_GET["do"] == 14) {
    if (ctype_digit($_GET["tid"])) {
        $docktarget = new Schiffe($_GET["tid"]);
        echo $schiff->fehler[($schiff->docken($docktarget))];
    }
}

if (isset($_GET["defense"]) && ctype_digit($_GET["defense"])) {
    $schiff->defense = $_GET["defense"];
    mysqli_query($verbindung, "update schiffe set defend='" . $_GET["defense"] . "' where id=" . $schiff->id);
}

//feuern
if ($_GET["do"] == 3) {

    $ziel = explode("-", $_GET["opfer"]);
    $zieltyp = $ziel[0];
    $zielid = $ziel[1];
    if (!ctype_digit($zielid))
        die("Error 42");
    if ($zieltyp == 'P') {
        $ziel = new Planeten($zielid);
        $schiff->feuern($ziel, 0);
    }
    if ($zieltyp == 'S') {
        $ziel = new Schiffe($zielid);
        $schiff->feuern($ziel, 0);
    }
}

if ($_GET["do"] == 30) {

    $ziel = explode("-", $_GET["opfer"]);
    $zieltyp = $ziel[0];
    $zielid = $ziel[1];
    if (!ctype_digit($zielid))
        die("Error 42");
    if ($zieltyp == 'P') {
        $ziel = new Planeten($zielid);
        $schiff->feuern($ziel, 10);
    }
    if ($zieltyp == 'S') {
        $ziel = new Schiffe($zielid);
        $schiff->feuern($ziel, 10);
    }
}

if ($_GET["do"] == 31) {

    $ziel = explode("-", $_GET["opfer"]);
    $zieltyp = $ziel[0];
    $zielid = $ziel[1];
    if (!ctype_digit($zielid))
        die("Error 42");
    if ($zieltyp == 'P') {
        $ziel = new Planeten($zielid);
        $schiff->feuern($ziel, 20);
    }
    if ($zieltyp == 'S') {
        $ziel = new Schiffe($zielid);
        $schiff->feuern($ziel, 20);
    }
}

if ($_GET["do"] == "aufladen" /*&& $_SESSION["Id"] == 1*/) {
    mysqli_query($verbindung, "update schiffe s,bauplan p set energie=maxenergie, gondeln=0, phaser=0,torpedohitze=0,frachtraum='0/0/0/0/0/0/0/0/0/0/10/10' where p.klasse = s.klasse and s.id =" . $schiff->id);
    $schiff = new Schiffe($schiff->id);
}

//schilde aufladen
if ($_POST["do"] == 2) {
    $amount = $_POST["schilde"];
    echo $schiff->fehler[($schiff->schildaufladen($amount))];
}
//alarstufe
if ($_GET["do"] == '6g')
    $schiff->alarmstufe = 'green';
if ($_GET["do"] == '6y')
    $schiff->alarmstufe = 'yellow';
if ($_GET["do"] == '6r')
    $schiff->alarmstufe = 'red';
mysqli_query($verbindung, "UPDATE schiffe SET alarmstufe='$schiff->alarmstufe' WHERE id='$schiff->id'");
//ENDE CODE abahndkungen

if ($schiff->besitzer->id == 2)
    die("Dein Schiff wurde vermutlich zerst&ouml;rt");

$eoutput = 0;
if ($schiff->warpkernstatus == 1 && $schiff->warpkern > 0) {  //E aus warpkern
    if ($schiff->warpkern < $schiff->energieoutput) {
        $eoutput = $schiff->warpkern;
    } else {
        $eoutput = $schiff->energieoutput;
    }
}

//Anfang ausrichtungstabelle
echo '<table cellpadding="15"><tr><td width="70%" style="vertical-align:top;">';

if (/*$_SESSION["Id"] == 1*/true) {
    $bu = new Button("schiffe.php?sid=" . $schiff->id . "&do=aufladen", "aufladen");
    $bu->printme();
}

echo '<h3>Informationen des Schiffes: ', $schiff->name, ' <span style="color:silver;">(', $schiff->id, ')</span></h3>';
echo '<table class="liste"><tr><th>Display</th><th>Pos</th><th>Typ</th><th>Energie</th><th>Gondeln</th><th>Phaser</th><th>Torpedos</th><th>H&uuml;lle</th><th>Schilde</th></tr>';
echo '<tr><td><img src="', $schiff->bild, '" border="0" /></td><td>', $schiff->position->x, '|', $schiff->position->y, '</td><td>', $schiff->klasse, '</td><td>', $schiff->energie, '/', $schiff->maxenergie;

if ($eoutput > 0) {
    echo "<span style=\"color:green;\">+" . $eoutput . "</span>";
}

if ($eoutput < 0) {
    echo "<span style=\"color:red;\">-" . $eoutput . "</span>";
}

if ($eoutput == 0) {
    echo "<span style=\"color:grey;\">+" . $eoutput . "</span>";
}

echo '</td><td>', $schiff->gondeln, '/', $schiff->maxgondeln, '</td><td>', $schiff->laser, ' (', $schiff->phaser, '/', $schiff->maxphaser, ')</td><td>' . $schiff->torpedohitze . '/' . $schiff->maxtorpedohitze . '</td>';

$t_color = "";
$t_color2 = "";

if ($schiff->hull / $schiff->maxhull <= 0.5 && $schiff->hull / $schiff->maxhull > 0.2) {
    $t_color = '<span style="color:yellow;">';
    $t_color2 = "</span>";
}

if ($schiff->hull / $schiff->maxhull <= 0.2) {
    $t_color = '<span style="color:red;">';
    $t_color2 = "</span>";
}

echo '<td>'. $t_color.  $schiff->hull . $t_color2 . '/'. $schiff->maxhull. '</td><td>';
echo ($schiff->schildstatus == 1) ? '<span style="color:yellow;">' : '<span style="color:silver;">';
echo $schiff->schilde, '/', $schiff->maxschilde, '</span></td>';

if ($schiff->flotte > 0) {
    echo '<td><span style="width=50px;">';
    $bu = new Button("flotte.php?fid=" . $schiff->flotte, "zur Flotte");
    $bu->printme();
    echo '</span></td>';
}

echo '</tr></table>';
echo '<br /><h3>Schiffskontrolle</h3><table class="invitetable" style="text-align:center;"><tr><th>Schilde</th><form action="schiffe.php?sid=', $schiff->id, '" method="post"><input type="hidden" name="do" value="2" /><td><input type="input" size="6" name="schilde"><br />';
$bu = new Button("", "aufladen");
$bu->printme();
echo '</td></form><form action="schiffe.php?sid=', $schiff->id, '" method="post"><input type="hidden" name="do" value="4"><td>';
$bu = new Button("", ($schiff->schildstatus == 1) ? 'deaktivieren' : 'aktivieren');
$bu->printme();
echo '</td></form></tr>';
//Warpkern
if ($schiff->maxwarpkern > 0) {
    echo '<tr><th>Warpkern</th><td>', $schiff->warpkernstatus == 1 ? '<font color="yellow">' : '<font>', $schiff->warpkern, '</font></td><td>';
    $bu = new Button("schiffe.php?sid=" . $sid . "&do=64", $schiff->warpkernstatus == 1 ? 'aus' : 'ein');
    $bu->printme();
    echo '<br />';
    $bu = new Button("warpload.php?sid=" . $sid, "aufladen");
    $bu->printme();
    echo '</td></tr>';
}
//Tarnung
if ($schiff->skill->tarnung == 1) {
    echo '<form action="schiffe.php?sid=', $schiff->id, '" method="post"><tr><th>Tarnung</th><td>', $schiff->tarnung == 1 ? '<span style="color:yellow;">Tarnung aktiviert</span>' : 'Tarnung deaktiviert', '<td><input type="hidden" name="do" value="20">';
    $bu = new Button("", "ent/tarnen");
    $bu->printme();
    echo '</td></tr></form>';
}
//Transwarp
if ($schiff->skill->transwarp == 1)
    echo '<form action="schiffe.php?sid=', $schiff->id, '" method="post"><tr><th>Transwarp</th><td>X: <input type="text" name="transx" size="2"><br />Y: <input type="text" name="transy" size="2"></td><td><input type="hidden" name="do" value="30"><input type="submit" value="Energie!"></td></tr></form>';

echo '<tr><th>Alarmstufe</th><td>';
if ($schiff->alarmstufe == 'green')
    echo '<img src="images/misc/alarmgg.png" border="0" />'; else
    echo '<a href="schiffe.php?sid=', $schiff->id, '&do=6g"><img src="images/misc/alarmgk.png" border="0" /></a>';
if ($schiff->alarmstufe == 'yellow')
    echo '<img src="images/misc/alarmyg.png" border="0" />'; else
    echo '<a href="schiffe.php?sid=', $schiff->id, '&do=6y"><img src="images/misc/alarmyk.png" border="0" /></a>';
if ($schiff->alarmstufe == 'red')
    echo '<img src="images/misc/alarmrg.png" border="0" />'; else
    echo '<a href="schiffe.php?sid=', $schiff->id, '&do=6r"><img src="images/misc/alarmrk.png" border="0" /></a>';
echo '</td></tr>';


if ($schiff->skill->bauen == 1) {
    echo '<form action="createship.php?sid='.$schiff->id.'" method="post"><tr><th>Raumstation</th><td>';
    $bu = new Button("", "bauen");
    $bu->printme();
    echo '</td></tr></form>';
}

//steht schiff auf planet?
echo("DEBUG ". ("SELECT * FROM planeten WHERE x=" . $schiff->position->x . " AND y=" . $schiff->position->y . " AND `system`=" . $schiff->position->system->id));
$abfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE x=" . $schiff->position->x . " AND y=" . $schiff->position->y . " AND `system`=" . $schiff->position->system->id);
while ($tmp = mysqli_fetch_array($abfrage)) {
    $tempschiff = new Planeten($tmp["id"]);
    echo '<tr><th>Planet</th>';
    if ($schiff->besitzer->id == $tempschiff->besitzer->id)
        echo '<td><a href="planet.php?pid=', $tempschiff->id, '"><img src="images/misc/', $tempschiff->bild, '" border="0" /></a>'; else
        echo '<td><img src="images/misc/', $tempschiff->bild, '" border="0" />';
    echo '<br />', $tempschiff->name, ' (', $tempschiff->id, ') <br /><a href="userinfo.php?id=', $tempschiff->besitzer->id, '">', $tempschiff->besitzer->nickname, '</a></td>';
    if ($schiff->skill->erz && $tempschiff->besitzer->id == 2)
        echo '<td><a href="kolo.php?sid=', $schiff->id, '">kolonisieren</a></td>';
    if ($schiff->skill->basis == 0)
        echo '<form action="schiffe.php?sid=', $schiff->id, '" method="post"><input type="hidden" name="do" value="7"><td>';

    $bu = new Button("", $schiff->position->orbit == 1 ? 'Orbit verlassen' : 'Orbit betreten');
    $bu->printme();

    echo '</td></form>';

    if ($schiff->position->orbit == 1) {
        echo '<td>';
        echo '<center><table class="bordered3"><tr>';
//planetenbuttons
        echo '<td><a class="button scan" onmouseover="Tip(scanp)" onmouseout="UnTip()" href="scanplanet.php?sid=', $schiff->id, '&tid=', $tempschiff->id, '"><span>S</span></a></td>';
        echo '<td><a class="button energie" onmouseover="Tip(energ)" onmouseout="UnTip()" href="energie.php?fs=', $schiff->id, '&tp=', $tempschiff->id, '"><span>E</span></a></td>';
        if ($schiff->tarnung == 1 || $tempschiff->besitzer->id == 2 || $schiff->laser < 1 || $schiff->besitzer->level <= 1 || $tempschiff->besitzer->level <= 1) {
            
        } else
        if ($tempschiff->schildstatus == 1)
            echo '<td><a class="button phaser" onmouseover="Tip(phaserp)" onmouseout="UnTip()" href="schiffe.php?sid=', $schiff->id, '&do=3&opfer=P-', $tempschiff->id, '"><span>Ph</span></a></td>'; else
            echo '<td><a class="button phaser" onmouseover="Tip(phaserp)" onmouseout="UnTip()" href="bombplanet.php?sid=', $schiff->id, '&pid=', $tempschiff->id, '"><span>Ph</span></a></td>';
        echo '<td><a class="button beamto" onmouseover="Tip(hbeamsp)" onmouseout="UnTip()" href="beam.php?modus=', $tempschiff->besitzer->id == 3 ? 4 : 1, '&from=S-', $schiff->id, '&to=P-', $tempschiff->id, '"><span>Be</span></a></td>';
//noobschutz
        if (($tempschiff->besitzer->level >= 2 && $schiff->besitzer->level >= 2 ) || $tempschiff->besitzer->id == $_SESSION["Id"] || $tempschiff->besitzer->id == 3)
            echo '<td><a class="button beamfrom" onmouseover="Tip(wbeamsp)" onmouseout="UnTip()" href="beam.php?modus=', $tempschiff->besitzer->id == 3 ? 3 : 2, '&from=P-', $tempschiff->id, '&to=S-', $schiff->id, '"><span>Be</span></a></td>';
        if ($tempschiff->nachricht != '')
            echo '<td><a class="button botschaft thickbox" title="Botschaft" onmouseover="Tip(\'<b>Botschaft</b><br />Du rufst die Botschaft dieses Objekts ab</b>\')" onmouseout="UnTip()" href="m2.php?sid=', $schiff->id, '&tid=', $tmp["id"], '">B</a></td>';
//innertable end
        echo '</tr></table></center>';
        echo '</td></tr>';
    } else {

        if ($tmp["nachricht"] != '')
            echo '<td><a class="button botschaft thickbox" title="Botschaft" onmouseover="Tip(botschaft)" onmouseout="UnTip()" href="m2.php?sid=', $schiff->id, '&tid=', $tmp["id"], '"><span>B</span></a></td>';
        echo '</tr>';
    }
}


//Wurmloch Abfrage 
if ($cur_feld->feld->wurmloch) {
    echo '<tr><th>' . $cur_feld->name . '</th><td><img src="images/' . $cur_feld->bild . '" border="0" /></td>';
    echo '<form action="schiffe.php?sid=', $schiff->id, '" method="post"><td><input type="hidden" name="do" value="913"><input type="submit" value="einfliegen"></td></form></tr>';
}

//Horchposten
if ($schiff->klasse == 'Horchposten') {
    echo '<tr><th>Horchposten</th><td>';

    $bu = new Button("bericht.php?sid=" . $schiff->id, "Berichte einsehen");
    $bu->printme();
    echo '</td></tr>';
} //endifskill
//Deuterium Nebel
if ($schiff->skill->deuterium == 1 && $cur_feld->feld->deut > 0) {
    echo '<form action="schiffe.php?sid=', $schiff->id, '" method="post"><input type="hidden" name="do" value="5"><tr><th>' . $cur_feld->name . '</th><td>
       <img src="images/' . $cur_feld->bild . '" border="0"/></td><td>';
    echo '<input type="text" name="deutamount" size="7" value="(energie)" /></td><td>';
    $bu = new Button("", "einsaugen");
    $bu->printme();
    echo '</td></tr></form>';
} //endifskill
//Erz Nebel
if ($schiff->skill->erz == 1 && $cur_feld->feld->erz > 0) {
    echo '<form action="schiffe.php?sid=', $schiff->id, '" method="post"><input type="hidden" name="do" value="13"><tr><th>' . $cur_feld->name . '</th>
        <td><img src="images/' . $cur_feld->bild . '" border="0"/></td><td>';
    echo '<input type="text" name="erzamount" size="7" value="(energie)" /></td><td>';
    $bu = new Button("", "abbauen");
    $bu->printme();
    echo '</td></tr></form>';
}//endifskill

if ($schiff->frachtraum->fracht[10]->anzahl > 0 || $schiff->frachtraum->fracht[11]->anzahl > 0) {
    echo '<tr><th>Verteidigung</th><td style="text-align:left;">';
    if ($schiff->defense == 0) {
        echo '<img src="images/misc/phaserh.png" border="0" /> <span style="color:green;font-weight:bold;">Phaser</span><br />';
        echo '<a href="schiffe.php?sid=' . $schiff->id . '&defense=1"><img src="images/misc/photonh.png" border="0" /> Photonentorpedos</a><br />';
        echo '<a href="schiffe.php?sid=' . $schiff->id . '&defense=2"><img src="images/misc/quantenh.png" border="0" /> Quantentorpedos</a>';
    }
    if ($schiff->defense == 1) {
        echo '<a href="schiffe.php?sid=' . $schiff->id . '&defense=0"><img src="images/misc/phaserh.png" border="0" /> Phaser</a><br />';
        echo '<img src="images/misc/photonh.png" border="0" /> <span style="color:green;font-weight:bold;">Photonentorpedos</span><br />';
        echo '<a href="schiffe.php?sid=' . $schiff->id . '&defense=2"><img src="images/misc/quantenh.png" border="0" /> Quantentorpedos</a>';
    }
    if ($schiff->defense == 2) {
        echo '<a href="schiffe.php?sid=' . $schiff->id . '&defense=0"><img src="images/misc/phaserh.png" border="0" /> Phaser</a><br />';
        echo '<a href="schiffe.php?sid=' . $schiff->id . '&defense=1"><img src="images/misc/photonh.png" border="0" /> Photonentorpedos</a><br />';
        echo '<img src="images/misc/quantenh.png" border="0" /> <span style="color:green;font-weight:bold;">Quantentorpedos</span>';
    }
}

if ($cur_feld->feld->energieverlust > 0) {
    echo '<tr><th>' . $cur_feld->name . '</th><td><img src="images/' . $cur_feld->bild . '" border="0" /></td>';
    echo '<td>' . ($cur_feld->feld->energieverlust * 10) . '% Energieverlust pro Tick</td></tr>';
}

if ($cur_feld->feld->hide) {
    echo '<tr><th>' . $cur_feld->name . '</th><td><img src="images/' . $cur_feld->bild . '" border="0" /></td>';
    echo '<td>Waffensysteme sowie Schilde ausgefallen!</td></tr>';
}



//******************************
//Transwarp Knoten

$cur_ships = $cur_feld->getShips();
for ($i = 0; $i < sizeof($cur_ships); $i++) {
    if ($cur_ships[$i]->klasse == "Transwarphub") {
        echo '<tr><th>' . $cur_ships[$i]->klasse . '</th><td><img src="' . $cur_ships[$i]->bild . '" border="0" /></td>';
        echo "<td>";
        $bu = new Button("transwarp.php?sid=" . $schiff->id, "Hub System scannen");
        $bu->printme();
        echo "</td></tr>";
    }

    if ($cur_ships[$i]->skill->skillbase) {
        echo '<tr><td><img src="images/' . $cur_ships[$i]->bild . '" border="0" /></td><td>', $cur_ships[$i]->name, ' (', $cur_ships[$i]->id, ')<br />', $cur_ships[$i]->besitzer->nickname, '</td>';
        echo '<td><a href="schiffe.php?sid=', $schiff->id, '&tid=', $stat->id, '&do=14"><span style="color:yellow;">', $schiff->dock == $stat->id ? 'abdocken' : 'andocken', '</span></a></td></tr>';
    }
}

//Transwarp Spalte
$abfrage = mysqli_query($verbindung, "SELECT * FROM weltraum WHERE typ='t' AND x=" . $schiff->position->x . " AND y=" . $schiff->position->y . " AND `system`=" . $schiff->position->system->id);
while ($tmp = mysqli_fetch_array($abfrage)) {
    echo '<tr><th>Transwarp-Spalte</th><td><img src="images/misc/spalte.jpg" border="0" /></td>';
    echo '<td><a href="schiffe.php?sid=', $schiff->id, '&do=88">in die Spalte einfliegen ( 10% Verlustgefahr )</a></td></tr>';
}

//System
if ($schiff->position->system->id == 0) {
    $csys = new System(array($schiff->position->x, $schiff->position->y));
    if ($csys->feld != null) {
        echo '<tr><th>', $csys->name, '-System (', $csys->id, ') </th><td><img src="images/systems/', $csys->bild, '" border="0" /></td>';
        echo '<td>';
        $bu = new Button("schiffe.php?sid=" . $schiff->id . "&do=7&dir=s", "System betreten");
        $bu->printme();
        echo '<br />';
        $bu = new Button("scansystem.php?id=" . $csys->id . "&sid=" . $schiff->id, "System scannen");
        $bu->printme();
        echo '</td></tr>';
    }
}

if ($schiff->position->system->id > 0) {
    echo '<tr><th>', $schiff->position->system->name, '-System (', $schiff->position->system->id, ') </th><td><img src="images/systems/', $schiff->position->system->bild, '" border="0" /></td>';
    echo '<td>';
    $bu = new Button("schiffe.php?sid=" . $schiff->id . "&do=7&dir=s", "System verlassen");
    $bu->printme();
    echo '</td></tr>';
}
if ($notable != 1)
    echo '</table>';




//ausrichtungstabelle
echo '</td><td style="vertical-align:top;text-align:left;" align="left">';
$schiff->position->system->id = $schiff->position->system->id;
echo '<table class="bordered" style="margin:0px;padding:0px;">';
for ($i = -1 - $schiff->lrs - 2; $i <= 2 + $schiff->lrs + 1; $i++) {
    echo '<tr>';
    echo '<td></td>';
    for ($j = -2 - $schiff->lrs; $j <= 2 + $schiff->lrs; $j++) {
        $dir = false;
        if ($i == -2 - $schiff->lrs - 2 && $j == -2 - $schiff->lrs)
            echo '<td></td>';
        if ($j == -2 - $schiff->lrs && $i >= -2 - $schiff->lrs && $i <= 2 + $schiff->lrs)
            echo '<td>', $i + $schiff->position->y, '</td>';
        if ($j == -2 - $schiff->lrs && $i == -2 - $schiff->lrs - 1)
            echo '<td>x/y</td>';

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

            $counter = 0;
            $tmpx = $schiff->position->x + $j;
            $tmpy = $schiff->position->y + $i;

            $feld = new Weltraum($tmpx, $tmpy, $schiff->position->system->id, $schiff->position->system->id > 0);
            $ttip = "<b>" . $feld->name . "</b>";
            if ($feld->tooltip != '')
                $ttip .= "<br />" . $feld->tooltip;


            // <td> setzen
            echo '<td style="';
            if ($tmpx == $schiff->position->x && $tmpy == $schiff->position->y)
                echo 'border:1pt solid #fa8f08;';
            if ($schiff->position->system->id > 0 && ( $tmpx <= 0 || $tmpy <= 0 || $tmpx > 20 || $tmpy > 20 ))
                echo 'border:1pt solid #ce0009;';
            echo 'text-align:center;width:32px;height:32px;padding:0px;background-image:url(\'images/' . $feld->bild . '\');">';
            echo '<a style="display:block;padding:8px;text-decoration:none;" onmouseover="Tip(\'' . $ttip . '\')" onmouseout="UnTip()" href="schiffe.php?x=', $tmpx, '&y=', $tmpy, '&sid=' . $schiff->id . '" >';
            echo '<span style="font-weight:bold;">' . ($feld->getNumberofShips() == 0 ? '&nbsp;' : $feld->getNumberofShips()) . '</span></a></td>';
            if ($j == 2 + $schiff->lrs)
                echo '<td>', $i + $schiff->position->y, '</td>';
        }
    }
    echo '<td></td>';


    echo '</tr>';
}
echo '</table>';


//ausrichtungstabelle
echo '</td></tr></table>';



//Schiffsansicht
$display_test1 = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE `system`=" . $schiff->position->system->id . " AND orbit=" . $schiff->position->orbit . " AND x=" . $schiff->position->x . " AND y=" . $schiff->position->y . " AND id!='$schiff->id' AND typ='s' AND klasse!='Tr&uuml;mmer'");
$display_test2 = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE `system`=" . $schiff->position->system->id . " AND orbit=" . $schiff->position->orbit . " AND x=" . $schiff->position->x . " AND y=" . $schiff->position->y . " AND id!='$schiff->id' AND klasse='Tr&uuml;mmer'");
if (mysqli_num_rows($display_test1) > 0 || mysqli_num_rows($display_test2) > 0) {

    echo '<br /><h3>Schiffe im Sektor</h3><table class="liste">';
    if (!$cur_feld->feld->hide) {

        echo '<tr>
  <th>Schiff</th>
  <th>Typ</th>
  <th>Name</th>
  <th>Besitzer</th>
  <th>H&uuml;lle</th>

  <th>Schilde</th>
  <th>S</th>
  <th>E</th>
  <th>P</th>
  <th>T</th>
  <th>Q</th>
  <th>-&gt</th>
  <th>&lt;-</th>

  <th>F</th>
  <th>B</th>

</tr>';

        $cfeld = new Weltraum($schiff->position->x, $schiff->position->y, $schiff->position->system->id, $schiff->position->system->id > 0);
        $l = $cfeld->getShips();

        for ($i = 0; $i < sizeof($l); $i++) {
            $tempschiff = &$l[$i];
            if ($tempschiff->besitzer->id == $ich->besitzer->id ||
                    ( $tempschiff->tarnung == 0 ) && $tempschiff->id != $schiff->id) {

                if ($ich->id == $tempschiff->besitzer->id)
                    echo '<tr><td><a href="schiffe.php?sid=', $tempschiff->id, '"><img src="', $tempschiff->bild, '" border="0" /></a></td>'; else
                    echo '<tr><td><img src="', $tempschiff->bild, '"></td>';
                echo '<td>', $tempschiff->klasse;
//TARN UND DOCK
                if ($tempschiff->tarnung == 1)
                    echo '<br /><span style="color:yellow;">getarnt</span>';
                if ($tempschiff->dock > 0)
                    echo '<br /><span style="color:yellow;">angedockt</span>';
//ENDE TARN UND DOCK
                echo '</td>';
                echo '<td>', $tempschiff->name, ' <span style="color:silver;">(', $tempschiff->id, ')</span></td><td><a href="userinfo.php?id=', $tempschiff->besitzer->id, '">', $tempschiff->besitzer->nickname, '</a></td>';

                $t_color = "";
                $t_color2 = "";
                echo $tempschiff->id."<br>";
                if ($tempschiff->hull / $tempschiff->maxhull <= 0.5 && $tempschiff->hull / $tempschiff->maxhull > 0.2) {
                    $t_color = '<span style="color:yellow;">';
                    $t_color2 = "</span>";
                }

                if ($tempschiff->hull / $tempschiff->maxhull <= 0.2) {
                    $t_color = '<span style="color:red;">';
                    $t_color2 = "</span>";
                }

                echo '<td>' . $t_color . $tempschiff->hull . $t_color2 . '/' . $tempschiff->maxhull . '</td><td>';
                echo ($tempschiff->schildstatus == 1) ? '<span style="color:yellow;">' : '<span style="color:silver;">';
                echo $tempschiff->schilde, '/', $tempschiff->maxschilde, '</span></td>';
                echo '<td><a class="button scan" onmouseover="Tip(\'<b>Scannen...</b><br />Scannt das Schiff und zeigt seine Waren an.\')" onmouseout="UnTip()" href="schiffscan.php?sid=', $sid, '&tid=', $tempschiff->id, '"><span>S</span></a></td>';
                echo '<td><a class="button energie" onmouseover="Tip(\'energie\')" onmouseout="UnTip()" href="energie.php?fs=', $schiff->id, '&ts=', $tempschiff->id, '"><span>E</span></a></td>';

//echo $tempschiff->besitzer->id."-".$_SESSION["Id"] ."--". $tempschiff->besitzer->id."-2--". $schiff->tarnung ."-1--". $schiff->laser ."<1--". $schiff->besitzer->mitglied ."<45--". $tempschiff->besitzer->mitglied;
//echo $tempschiff->besitzer->id==$_SESSION["Id"],' - ',$tempschiff->besitzer->id==2,' - ',$schiff->tarnung==1,' - ',$schiff->laser<1,' - ',$schiff->besitzer->level<=1,' - ',$tempschiff->besitzer->level<=1;
                if ($tempschiff->besitzer->id == $_SESSION["Id"] || $tempschiff->besitzer->id == 2 || $schiff->tarnung == 1 || $schiff->laser < 1 || ( ($schiff->besitzer->level <= 1 || $tempschiff->besitzer->level <= 1) && $tempschiff->besitzer->id != 16))
                    echo '<td><div class="button"></div></td><td><div class="button"></div></td><td><div class="button"></div></td>'; else {
                    echo '<td><a class="button phaser" onmouseover="Tip(\'<b>Feuern</b><br />mit Phasern feuern.\')" onmouseout="UnTip()" href="schiffe.php?sid=', $schiff->id, '&do=3&opfer=S-', $tempschiff->id, '"><span>P</span></a></td>';

//photonen
                    if($schiff->frachtraum->fracht[10]->anzahl > 0)
                        echo '<td><a class="button photon" onmouseover="Tip(\'<b>Feuern</b><br />mit Photonentorpedos feuern.\')" onmouseout="UnTip()" href="schiffe.php?sid=', $schiff->id, '&do=30&opfer=S-', $tempschiff->id, '"><span>P</span></a></td>';
                    else
                      echo '<td><div class="button"></div></td>';
//quanten                    
                    if($schiff->frachtraum->fracht[11]->anzahl > 0)
                        echo '<td><a class="button quanten" onmouseover="Tip(\'<b>Feuern</b><br />mit Quantentorpedos feuern.\')" onmouseout="UnTip()" href="schiffe.php?sid=', $schiff->id, '&do=31&opfer=S-', $tempschiff->id, '"><span>P</span></a></td>';
                    else
                      echo '<td><div class="button"></div></td>';
                }
//not ferg 
                echo '<td><a class="button beamto" onmouseover="Tip(\' ', $tempschiff->besitzer->id != 3 ? '<b>Beamen</b><br />Du beamst Waren von deinem Schiff auf das Zielschiff.' : '<b>Konto-Beamen</b><br />Du beamst Waren von deinem Schiff auf dein Konto.', ' \')" onmouseout="UnTip()" href="beam.php?modus=', $tempschiff->besitzer->id == 3 ? 4 : 1, '&from=S-', $schiff->id, '&to=S-', $tempschiff->id, '"><span>-&gt;</span></a></td>';
//noob schutz
                echo '<td><a class="button beamfrom" onmouseover="Tip(\'', $tempschiff->besitzer->id != 3 ? '<b>Beamen</b><br />Du beamst Waren von dem Zielschiff auf dein Schiff.' : '<b>Konto-Beamen</b><br />Du beamst Waren von deinem Konto auf dein Schiff.', ' \')" onmouseout="UnTip()" href="beam.php?modus=', $tempschiff->besitzer->id == 3 ? 3 : 2, '&to=S-', $schiff->id, '&from=S-', $tempschiff->id, '"><span>&lt;-</span></a></td>';
//FER
                if ($schiff->besitzer->id == 3) {
                    echo '<td><a class="button beamto" onmouseover="Tip(\'<b>Beamen</b><br />Du beamst Waren von deinem Schiff auf das Zielschiff.\')" onmouseout="UnTip()" href="beam.php?modus=1&from=S-', $schiff->id, '&to=S-', $tempschiff->id, '"><span>-&gt;</span></a></td>';
//noob schutz
                    echo '<td><a class="button beamfrom" onmouseover="Tip(\'<b>Beamen</b><br />Du neamst Waren von dem Zielschiff auf dein Schiff.\')" onmouseout="UnTip()" href="beam.php?modus=2&to=S-', $schiff->id, '&from=S-', $tempschiff->id, '"><span>&lt;-</span></a></td>';
                }
                echo '<td><a class="button kontakt" onmouseover="Tip(\'<b>Nachricht</b><br />Du sendest dem Besitzer dieses Schiffes eine Nachricht.\')" onmouseout="UnTip()" href="newmail.php?to=', $tempschiff->besitzer->id, '"><span>F</span></a></td>';
                if ($tempschiff->nachricht != '')
                    echo '<td><a class="button botschaft thickbox" title="Botschaft" onmouseover="Tip(\'<b>Botschaft</b><br />Du rufst die Botschaft dieses Objekts ab</b>\')" onmouseout="UnTip()" href="m2.php?sid=', $schiff->id, '&tid=', $tempschiff->id, '"><span>B</span></a></td>';
//questabfrage
                $qbool = false;
                $qabfrage = mysqli_query($verbindung, "SELECT * FROM quests WHERE geber='" . $tempschiff->id . "' OR abgeber='" . $tempschiff->id . "'");
                while ($qrot = mysqli_fetch_array($qabfrage))
                    $qbool = true;
                if ($qbool)
                    echo '<td><a href="quest.php?sid=', $tempschiff->id, '&uid=', $schiff->id, '"><img src="quest.png" border="0" /></a></td>';


                echo '</tr>';
//tarntest --ende
            }
            if ($tempschiff->tarnung == 1 && $tempschiff->besitzer->id != $_SESSION["Id"]) { // chiffrierte Anzeige
                echo '<tr><td>unbekannt</td>';
                echo '<td>???<br /><span style="color:yellow;">getarnt</span></td><td>???</td><td>???</td><td>???</td><td>';
                echo '???</td>';
                echo '<td>???</td>';
                echo '<td>-</td></form>';
                echo '</tr>';
            }
//--->
        }
    }
    echo '</table>';
}
echo '<br />';

echo '<div><h4>Lagerraum</h4><br /><table class="invitetable" style=\"text-align:left;\">';
echo '<tr><th>Lagerraum</th><th style="text-align:center;" colspan="3">' . $schiff->frachtraum->gesamt() . '/' . $schiff->frachtraum->max . ' Frei: ' . ($schiff->frachtraum->max - $schiff->frachtraum->gesamt());
echo '</th></tr>';
//lager anzeige
for ($i = 0; $i < sizeof($schiff->frachtraum->fracht); $i++) {

    $balken = "";
    $tausend = floor($schiff->frachtraum->fracht[$i]->anzahl / 1000);
    $hundert = floor(($schiff->frachtraum->fracht[$i]->anzahl - $tausend * 1000) / 100);
    $rest = $schiff->frachtraum->fracht[$i]->anzahl - $tausend * 1000 - $hundert * 100;
//volle
    for ($o = 1; $o <= $tausend; $o++) {
        $balken.="<img src=\"images/tausend.jpg\" style=\"border:1px solid white;margin-left:1px;\"  width=\"18\" height=\"15\"/>";
    }
//volle hundert
    for ($o = 1; $o <= $hundert; $o++)
        $balken.="<img src=\"images/hundert.jpg\" style=\"border:1px solid white;margin-left:1px;\"  width=\"15\" height=\"15\"/>";


    if ($rest > 0)
        $balken.='<img src="images/balken.jpg" border="0" width="' . ($rest * 2) . '" height="15" style="border:1px solid white;margin-left:1px;" />';

    echo $schiff->frachtraum->fracht[$i]->anzahl != 0 ? "<tr><th>" . $schiff->frachtraum->fracht[$i]->name . "</th><td>" . $schiff->frachtraum->fracht[$i]->anzahl . ($schiff->frachtraum->fracht[$i]->max >= 0 ? '/' . $schiff->frachtraum->fracht[$i]->max : '') . "</td><td><img src=\"images/misc/" . $schiff->frachtraum->fracht[$i]->bild . "\" border=\"0\" /></td><td width=\"300px\">" . $balken . "</td></tr>" : "";
}
echo '</table></div>';


echo '<form action="schiffe.php?sid=', $schiff->id, '" method="POST" ><input name="newname" type="text" value="', $schiff->nameklartext, '" /><input type="hidden" name="do" value="-1" />&nbsp;';
$bu = new Button("", "Namen &auml;ndern");
$bu->printme();
echo '</form>';
//notiz setzen
echo 'Schiffsnachricht: <br /><form action="schiffe.php?sid=', $schiff->id, '" method="post"><input type="hidden" name="do" value="1337"><textarea rows=6 cols=23 name="message2">', $schiff->nachricht, '</textarea><br />';
$bu = new Button("", "Nachricht erstellen");
$bu->printme();
echo '</form>';

echo '<br /><br /><form action="schiffe.php?sid=', $schiff->id, '" method="post" onSubmit="return frage(4)"><input type="hidden" name="do" value="100">';
$bu = new Button("", "Selbstzerst&ouml;rung");
$bu->printme();
echo '</form>';

include("foot.php");
?>
