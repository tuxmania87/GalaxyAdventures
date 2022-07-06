<?php

$stunde = date("H");

include("head.php");
include("navlogged.php");
include("klassen.php");
$verbindung = get_verbindung();
$pid = $_GET["pid"];
$photprod = 0;
$quantprod = 0;
$pid = $_GET["pid"];
if (ctype_digit($pid)) {
    $planet = new Planeten($pid);
}
//CHEATSCHUTZ ANFANG


$testid = $_GET["sid"];
if (!isset($testid))
    $testid = $_GET["pid"];
if (!ctype_digit($testid))
    die("Fehler: GET Konflikt");
if (!isset($_SESSION["Id"]))
    die("Fehler: Session abgelaufen");
if ($planet->typ != 'm' && $planet->typ != 'l' && $planet->typ != 'i' && $planet->typ != 'z' && $planet->typ != 'mm' && $planet->typ != 'lm' && $planet->typ != 'om')
    die("Fehler: TYP ist unbekannt!");
if ($planet->besitzer->id != $_SESSION["Id"])
    die();

//CHEATSCHUTZ ENDE

$ich = new Account($_SESSION["Id"]);
$oflaeche = $planet->typ == 'm' || $planet->typ == 'l' || $planet->typ == 'i' || $planet->typ == 'z' ? 50 : 24;

//alarstufe
if ($_GET["do"] == '13g')
    $planet->alarmstufe = 'green';
if ($_GET["do"] == '13y')
    $planet->alarmstufe = 'yellow';
if ($_GET["do"] == '13r')
    $planet->alarmstufe = 'red';
mysqli_query($verbindung, "UPDATE planeten SET alarmstufe='$planet->alarmstufe' WHERE id='$planet->id'");

//neue nachricht
if ($_POST["do"] == 1337) {
    $planet->nachricht = mysqli_real_escape_string($verbindung, $_POST["message2"]);
    mysqli_query($verbindung, "UPDATE planeten SET nachricht='$planet->nachricht' WHERE id='$planet->id'");
}

//pre tick berechnung
//allgemeine Graphberechung

$nodelist = array();
$liste = Bauplan_Gebaude::getCompleteListe();

//reverse map  Res -> Factory
$map = array();
for ($i = 0; $i < sizeof($liste); $i++) {
    for ($j = 0; $j < sizeof($liste[$i]->produziert->fracht); $j++) {
        if ($liste[$i]->produziert->fracht[$j]->anzahl > 0) {
            $map[$liste[$i]->produziert->fracht[$j]->id][] = $liste[$i]->id;
        }
    }
}

$t_fracht = array();
$glob_energy = 0;
//get energy producters
for ($i = 1; $i < sizeof($planet->feld); $i++) {
    if (
        $planet->feld[$i]->bau->produziert->fracht[0]->anzahl > 0 &&
        $planet->feld[$i]->aktiv == 1 && $planet->feld[$i]->rest_bauzeit == 0
    ) {
        $create = true;
        $t_array = array();
        for ($j = 0; $j < sizeof($planet->frachtraum->fracht); $j++) {
            if ($planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl > 0) {
                if ($planet->frachtraum->fracht[$j]->anzahl < $planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl) {
                    $create = false;
                    break;
                } else {
                    $t_array[$planet->feld[$i]->bau->braucht->fracht[$j + 1]->id] -= $planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl;
                }
            }
        }
        if (!$create) {
            $t_array = array();
        } else {
            foreach ($t_array as $key => $value) {
                $planet->frachtraum->fracht[$key - 1]->anzahl += $value;
                $t_fracht[$key] += $value;
            }
            $glob_energy += $planet->feld[$i]->bau->produziert->fracht[0]->anzahl;
        }
    }
}

//DEBUG echo "E: ".$glob_energy;

$cp_array = array_fill(0, sizeof($planet->frachtraum->fracht) + 1, 0);

foreach ($t_fracht as $key => $value) {
    $cp_array[$key] = $value;
}
$cp_array[0] = $glob_energy;

$dummy_fracht = implode("/", $cp_array);

$addfracht = new Frachtraum($dummy_fracht, "dummy");


//while(sizeof($already_leaf < $prod_count)) {
//for ($ssd = 0; $ssd < 2; $ssd++) {
for ($i = 0; $i < sizeof($liste); $i++) {


    //is_productive?
    $produktiv = false;
    for ($j = 1; $j < sizeof($liste[$i]->produziert->fracht); $j++) {
        if ($liste[$i]->produziert->fracht[$j]->anzahl > 0) {
            $produktiv = true;
        }
    }

    if ($produktiv) {
        //echo $liste[$i]->name." -- ".$liste[$i]->id." <br />";
        $n = new node();
        $n->data = $liste[$i]->id;
        $n->next = new Menge();

        for ($j = 1; $j < sizeof($liste[$i]->braucht->fracht); $j++) {
            if ($liste[$i]->braucht->fracht[$j]->anzahl > 0) {

                for ($h = 0; $h < sizeof($map[$liste[$i]->braucht->fracht[$j]->id]); $h++) {
                    $n->next->add($map[$liste[$i]->braucht->fracht[$j]->id][$h]);
                }
            }
        }
        $nodelist[] = $n;
    }
}

$leaf_nodes = array();
$round_nodes = array(0);


while (sizeof($round_nodes) > 0) {

    $round_nodes = array();

    for ($i = 0; $i < sizeof($nodelist); $i++) {
        if ($nodelist[$i]->next->is_Empty() && !in_array($nodelist[$i]->data, $leaf_nodes)) {
            $round_nodes[] = $nodelist[$i]->data;
            //$leaf_nodes[] = $nodelist[$i]->data;
        }
    }


    //TODO: round_nodes contains building ID that have to be handeled by
    // Iterating over planet field list in linear time times O(n)= n * log n
    for ($i = 1; $i < sizeof($planet->feld); $i++) {
        if (in_array($planet->feld[$i]->bau->id, $round_nodes) && $planet->feld[$i]->aktiv == 1 && $planet->feld[$i]->rest_bauzeit == 0) {
            //do we have all necessairy stuff?
            $stuff_control = true;
            for ($j = 0; $j < sizeof($planet->frachtraum->fracht); $j++) {
                if ($planet->frachtraum->fracht[$j]->anzahl < $planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl) {
                    $stuff_control = false;
                }
            }
            if ($planet->energie + $glob_energy < $planet->feld[$i]->bau->braucht->fracht[0]->anzahl)
                $stuff_control = false;


            if ($stuff_control) {
                //yep we can build it
                for ($j = 0; $j < sizeof($planet->frachtraum->fracht); $j++) {
                    $planet->frachtraum->fracht[$j]->anzahl += $planet->feld[$i]->bau->produziert->fracht[$j + 1]->anzahl;
                    $planet->frachtraum->fracht[$j]->anzahl -= $planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl;
                    $addfracht->fracht[$j + 1]->anzahl += $planet->feld[$i]->bau->produziert->fracht[$j + 1]->anzahl;
                    $addfracht->fracht[$j + 1]->anzahl -= $planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl;
                }
                $glob_energy -= $planet->feld[$i]->bau->braucht->fracht[0]->anzahl;
                $addfracht->fracht[0]->anzahl -= $planet->feld[$i]->bau->braucht->fracht[0]->anzahl;
            }
        }
    }



    //prepare for next round
    for ($i = 0; $i < sizeof($nodelist); $i++) {
        if (!$nodelist[$i]->next->is_Empty()) {
            for ($j = 0; $j < sizeof($round_nodes); $j++) {
                $nodelist[$i]->next->del($round_nodes[$j]);
            }
        }
    }
    $leaf_nodes = array_merge($leaf_nodes, $round_nodes);
}

/* TODEL for ($i = 0; $i < sizeof($nodelist); $i++) {
  if ($nodelist[$i]->next->is_Empty() && !in_array($nodelist[$i]->data, $leaf_nodes)) {
  $leaf_nodes[] = $nodelist[$i]->data;
  echo "Blatt2: " . $nodelist[$i]->data . " <br />";
  }
  } */


//}

$target = $_POST["target"];
$planet = new Planeten($pid);
$planet->energieoutput = $addfracht->fracht[0]->anzahl;

if (isset($_POST["newname"])) {
    $pass_tmp = $_POST["newname"];
    changeit($pass_tmp);
    $planet->name = $pass_tmp;
    mysqli_query($verbindung, "UPDATE planeten SET name='" . $planet->name . "' WHERE id='" . $planet->id . "'");
    $planet->nameklartext = $planet->name;
    $planet->name = pruefetext($pass_tmp);
}


if ($planet->besitzer->level <= 3)
    echo '<span style="font-weight:bold;color:yellow;">Hinweis: Dein Prim&auml;rziel sollte es sein, die <a href="quest.php?sid=0">Level-Quests</a> abzuschliessen. Achte auf neue Quests</span><br /><br />';

echo '<h3>Kolonie: ', $planet->name, ' (', $planet->id, ')</h3><br />';
echo '<h4>Status</h4><table class="invitetable"><tr><th>Energie</th><td><span style="font-weight:bold;">', $planet->energie, '/', $planet->maxenergie, $planet->energieoutput >= 0 ? '<span style="color:green;"> + ' . $planet->energieoutput . '</span>' : '<span style="color:red;">' . $planet->energieoutput . '</span>', '</span></td></tr>';
if ($planet->maxschilde > 0)
    echo '<tr><th>Schilde</th><td>', $planet->schildstatus == 0 ? '<span>' : '<span style="color:yellow;">', ' ', $planet->schilde, '/', $planet->maxschilde, '</span></td></tr>';
echo '<tr><th>Alarmstufe</th><td>';

if ($planet->alarmstufe == 'green')
    echo '<img src="images/misc/alarmgg.png" border="0" />';
else
    echo '<a href="planet.php?pid=', $planet->id, '&do=13g"><img src="images/misc/alarmgk.png" border="0" /></a>';
if ($planet->alarmstufe == 'yellow')
    echo '<img src="images/misc/alarmyg.png" border="0" />';
else
    echo '<a href="planet.php?pid=', $planet->id, '&do=13y"><img src="images/misc/alarmyk.png" border="0" /></a>';
if ($planet->alarmstufe == 'red')
    echo '<img src="images/misc/alarmrg.png" border="0" />';
else
    echo '<a href="planet.php?pid=', $planet->id, '&do=13r"><img src="images/misc/alarmrk.png" border="0" /></a>';
echo '</td></tr></table>';


$datum = date("Y-m-d H:i:s");
$betreff = "Aushilfe bei deinem Planeten: $planet->name ($planet->id)";
$inhalt = "Hallo\n\nDa du noch neu in dieser Galaxis bist und wir bemerkt haben, dass du dringend Energie ben&ouml;tigst haben wir mit einem unserer Helferschiffen deinen Planeten wieder mit Energie beladen!\nAdmiral Miller - von der <b><font color=\"white\">F&ouml;deration</font></b>";
if ($planet->besitzer->mitglied <= 1 && $planet->energie == 0) {
    echo '<br /><br /><span style="color:red;">Achtung!! Du hast keine Energie mehr. Achte darauf unbedingt <b>Solaranlangen</b> auf W&uuml;sten zu bauen um Energie zu produzieren!</span><br /><span style="color:yellow;font-weight:bold;">Hinweis:</span>Du hast neue <a href="mail.php">Nachrichten</a>';
    mysqli_query($verbindung, "INSERT INTO mail (datum,empfaenger,absender,neu,betreff,inhalt) VALUES ('$datum','" . $planet->besitzer->id . "','5','1','$betreff','$inhalt')");
    $planet->energie = $planet->maxenergie;
    mysqli_query($verbindung, "UPDATE planeten SET energie = " . $planet->energie . " WHERE id = " . $planet->id);
}
if ($planet->besitzer->level <= 2 && $planet->energieoutput < 0 && $planet->energie < $planet->maxenergie) {
    echo '<br /><br /><span style="color:red;">Achtung!! Du produzierst weniger Energie als du ben&ouml;tist!!. Achte darauf unbedingt <b>Solaranlangen</b> auf W&uuml;sten zu bauen um Energie zu produzieren!</span>';
}

echo '<br /><h4>Planetenoberfläche</h4><table class="bordered">';

for ($i = 1; $i <= $oflaeche; $i++) {
    if (($i == 1 || $i == 21 || $i == 31 || $i == 41 || $i == 11) && $oflaeche == 50)
        echo '<tr>';
    if (($i == 7 || $i == 13 || $i == 19) && $oflaeche == 24)
        echo '<tr>';

    //is building productive?
    $produktiv = false;

    if (!is_null($planet->feld[$i]->bau->produziert)) {
        for ($k = 0; $k < sizeof($planet->feld[$i]->bau->produziert->fracht) && $planet->feld[$i]->bau->id != null; $k++) {
            if ($planet->feld[$i]->bau->produziert->fracht[$k]->anzahl > 0) {
                $produktiv = true;
                break;
            }
        }
    }


    $tooltip = "<b>" . $planet->feld[$i]->name . "</b>";
    if ($planet->feld[$i]->rest_bauzeit > 0) {
        $tooltip .= "<br />fertig in <b>" . $planet->feld[$i]->rest_bauzeit . "</b> Ticks";
    } else if ($produktiv) {
        if ($planet->feld[$i]->aktiv == 1) {
            $tooltip .= " - <span style=\'color:green;font-weight:bold;\'>aktiviert</span>";
        } else {
            $tooltip .= " - <span style=\'color:red;font-weight:bold;\'>deaktiviert</span>";
        }
    }


    if ($planet->feld[$i]->bau->forschung) {
        $q = mysqli_query($verbindung, "select fid,status from mapforschung where status>1 and hash='" . $planet->id . "/" . $i . "'");
        while ($r = mysqli_fetch_array($q)) {
            $t_forschung = new Forschungen($r["fid"]);
            $tooltip .= "<br />erforscht: <b>" . $t_forschung->name . "</b> (" . $r["status"] . " Ticks)";
        }
    }

    $href = "";
    if ($planet->feld[$i]->bau->id != null) {
        // @TODO: schilde, kanone, forschung ...
        $href = "destroy.php";
    } else {
        $href = "build.php";
    }
    $href .= "?pid=" . $planet->id . "&fid=" . $i;

    echo '<td style="width:32px;height:32px;background-repeat:no-repeat;background-image:url(\'images/buildings/' . $planet->feld[$i]->bild . '\');"><a onmouseover="Tip(\'' . $tooltip . '\')" onmouseout="UnTip()" style="text-decoration:none;padding:8px 12px;" href="' . $href . '">&nbsp;</a></td>';


    if ($i % 10 == 0 && $oflaeche == 50) {
        echo '</tr>';
    }
    if ($i % 6 == 0 && $oflaeche == 24)
        echo '</tr>';
}
echo '</table>';
echo '<h4>Orbit</h4>';

echo '<table class="bordered">';


$limesorbit = 50 + ($oflaeche == 50 ? 20 : 12);
for ($i = 51; $i <= $limesorbit; $i++) {
    if (($i == 51 || $i == 61) && $oflaeche == 50)
        echo '<tr>';
    if (($i == 51 || $i == 67) && $oflaeche == 24)
        echo '<tr>';

    //is building productive?
    $produktiv = false;

    if (!is_null($planet->feld[$i]->bau->produziert)) {

        for ($k = 0; $k < sizeof($planet->feld[$i]->bau->produziert->fracht) && $planet->feld[$i]->bau->id != null; $k++) {
            if ($planet->feld[$i]->bau->produziert->fracht[$k]->anzahl > 0) {
                $produktiv = true;
                break;
            }
        }
    }
    
    $tooltip = "<b>" . $planet->feld[$i]->name . "</b>";
    if ($planet->feld[$i]->rest_bauzeit > 0) {
        $tooltip .= "<br />fertig in <b>" . $planet->feld[$i]->rest_bauzeit . "</b> Ticks";
    } else if ($produktiv) {
        if ($planet->feld[$i]->aktiv == 1) {
            $tooltip .= " - <span style=\'color:green;font-weight:bold;\'>aktiviert</span>";
        } else {
            $tooltip .= " - <span style=\'color:red;font-weight:bold;\'>deaktiviert</span>";
        }
    }

    if ($planet->feld[$i]->bau->forschung) {
        $q = mysqli_query($verbindung, "select fid,status from mapforschung where status>1 and hash='" . $planet->id . "/" . $i . "'");
        while ($r = mysqli_fetch_array($q)) {
            $t_forschung = new Forschungen($r["fid"]);
            $tooltip .= "<br />erforscht: <b>" . $t_forschung->name . "</b> (" . $r["status"] . " Ticks)";
        }
    }

    if ($planet->feld[$i]->bau->werft) {
        $q = mysqli_query($verbindung, "select klasse,frachtraum from schiffe where typ='' and nachricht='" . $planet->id . "/" . $i . "'");
        while ($r = mysqli_fetch_array($q)) {
            $tooltip .= "<br />baut gerade <b>" . $r["klasse"] . "</b> (" . $r["frachtraum"] . " Ticks)";
        }
    }

    $href = "";
    if ($planet->feld[$i]->bau->id != null) {
        // @TODO: schilde, kanone, forschung ...
        $href = "destroy.php";
    } else {
        $href = "build.php";
    }
    $href .= "?pid=" . $planet->id . "&fid=" . $i;

    echo '<td style="width:32px;height:32px;background-repeat:no-repeat;background-image:url(\'images/buildings/' . $planet->feld[$i]->bild . '\');"><a onmouseover="Tip(\'' . $tooltip . '\')" onmouseout="UnTip()" style="text-decoration:none;padding:8px 12px;" href="' . $href . '">&nbsp;</a></td>';


    if ($i % 10 == 0 && $oflaeche == 50) {
        echo '</tr>';
    }
    if ($i == 56 && $oflaeche == 24)
        echo '</tr>';
}
echo '</table>';

echo '<br />';
echo '<h4>Schiffe im Orbit</h4><form action="planet.php?pid=', $planet->id, '" method="post"><table><tr><td>';
$sio = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='s' AND orbit=1 AND x='" . $planet->position->x . "' AND y='" . $planet->position->y . "' AND system='" . $planet->position->system->id . "' AND tarnung=0");
if (mysqli_num_rows($sio) > 0) {
    echo '<select name="target">';
    while ($ship = mysqli_fetch_array($sio)) {
        $tship = new Schiffe($ship["id"]);
        echo '<option value="', $tship->id, '">', $tship->filtername(), ' (', $tship->id, ') Energie: ', $ship["energie"], '/', $ship["maxenergie"], '</option>';
    }
    echo '</select>';
} else {
    echo 'kein Schiff im Orbit';
}
echo '</td><td>';
$bu = new Button("", "Ziel einstellen");
$bu->printme();
echo '</td>';

if ($target != '') { // Ziel erfasst)
    $schiff = new Schiffe($target);

    echo '<td>', $schiff->besitzer->id == $_SESSION["Id"] ? '<a href="schiffe.php?sid=' . $schiff->id . '">' : '', '<img src="', $schiff->bild, '" border="0" />', $schiff->besitzer->id == $_SESSION["Id"] ? '</a>' : '', '</td><td>', $schiff->name, '(', $schiff->id, ')</td><td>S:', $schiff->schildstatus == 0 ? '<span>' : '<span style="color:yellow;">', $schiff->schilde, '/', $schiff->maxschilde, '</span></td>';
    echo '<td><a class="button energie" onmouseover="Tip(energ)" onmouseout="UnTip()" href="energie.php?fp=', $planet->id, '&ts=', $schiff->id, '"><span>E</span></a></td>';
    echo '<td><a class="button beamto" onmouseover="Tip(hbeamps)" onmouseout="UnTip()" href="beam.php?modus=1&from=P-', $planet->id, '&to=S-', $schiff->id, '"><span>Be</span></a></td>';
    echo '<td><a class="button beamfrom" onmouseover="Tip(wbeamps)" onmouseout="UnTip()" href="beam.php?modus=2&to=P-', $planet->id, '&from=S-', $schiff->id, '"><span>Be</span></td>';
}
echo '</tr></table></form><br />';

echo '<div><h4>Lagerraum</h4><br /><table class="invitetable" style="text-align:left;">';
echo '<tr><th>Lagerraum</th><th style="text-align:center;" colspan="3">' . $planet->frachtraum->gesamt() . '/' . $planet->frachtraum->max . ' Frei: ' . ($planet->frachtraum->max - $planet->frachtraum->gesamt());
echo '</th><th>+/-</th></tr>';
//lager anzeige
for ($i = 0; $i < sizeof($planet->frachtraum->fracht); $i++) {

    $balken = "";
    $tausend = floor($planet->frachtraum->fracht[$i]->anzahl / 1000);
    $hundert = floor(($planet->frachtraum->fracht[$i]->anzahl - $tausend * 1000) / 100);
    $rest = $planet->frachtraum->fracht[$i]->anzahl - $tausend * 1000 - $hundert * 100;
    //volle
    for ($o = 1; $o <= $tausend; $o++) {
        $balken .= "<img src=\"images/tausend.jpg\" style=\"border:1px solid white;margin-left:1px;\"  width=\"18\" height=\"15\"/>";
    }
    //volle hundert
    for ($o = 1; $o <= $hundert; $o++)
        $balken .= "<img src=\"images/hundert.jpg\" style=\"border:1px solid white;margin-left:1px;\"  width=\"15\" height=\"15\"/>";


    $add = "";
    $addbalken = "";
    if ($addfracht->fracht[$i + 1]->anzahl > 0) {
        $add = '<span style="color:green;font-weight:bold;">+' . $addfracht->fracht[$i + 1]->anzahl . '</span>';
        $addbalken = '<img src="images/add.jpg" height="15" width="' . (2 * $addfracht->fracht[$i + 1]->anzahl) . '" style="border:1px solid white;margin-left:1px;border-left:none;" />';
    }
    if ($addfracht->fracht[$i + 1]->anzahl < 0)
        $add = '<span style="color:red;font-weight:bold;">' . $addfracht->fracht[$i + 1]->anzahl . '</span>';
    if ($addfracht->fracht[$i + 1]->anzahl == 0)
        $add = '<span style="color:grey;font-weight:bold;">+' . $addfracht->fracht[$i + 1]->anzahl . '</span>';

    if ($rest > 0 && $addfracht->fracht[$i + 1]->anzahl > 0)
        $balken .= '<img src="images/balken.jpg" border="0" width="' . ($rest * 2) . '" height="15" style="border:1px solid white;margin-left:1px;border-right:none;" />';
    if ($rest > 0 && $addfracht->fracht[$i + 1]->anzahl <= 0)
        $balken .= '<img src="images/balken.jpg" border="0" width="' . ($rest * 2) . '" height="15" style="border:1px solid white;margin-left:1px;" />';


    echo $planet->frachtraum->fracht[$i]->anzahl != 0 || $addfracht->fracht[$i + 1]->anzahl != 0 ? "<tr><th>" . $planet->frachtraum->fracht[$i]->name . "</th><td>" . $planet->frachtraum->fracht[$i]->anzahl . "</td><td><img src=\"images/misc/" . $planet->frachtraum->fracht[$i]->bild . "\" border=\"0\" /></td><td width=\"300px\">" . $balken . $addbalken . "</td><td>" . $add . "</td></tr>" : "";
}
echo '</table></div>';

//notiz setzen
echo '<br /><h4>Planetennachricht</h4><form action="planet.php?pid=', $planet->id, '" method="post"><input type="hidden" name="do" value="1337"><textarea rows=6 cols=23 name="message2">', $planet->nachricht, '</textarea><br />';
$bu = new Button("", "Nachricht erstellen");
$bu->printme();
echo '</form>';

echo '<br /><form action="planet.php?pid=', $planet->id, '" method="post"><input type="text" name="newname" value="', $planet->nameklartext, '">&nbsp;&nbsp;&nbsp;';
$bu = new Button("", "Name ändern");
$bu->printme();
echo '</form>';
echo '<form action="planetchoice.php?pid=', $planet->id, '" method="post" onSubmit="return frage(3)"><input type="hidden" name="do" value="5"><br />';
$bu = new Button("", "Kolonie sprengen", "kolosprengen");
$bu->printme();
echo '</form>';

include("foot.php");
