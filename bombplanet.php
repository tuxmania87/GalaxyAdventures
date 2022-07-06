<?php

include("head.php");
include("navlogged.php");
include("klassen.php");
$betray = false;

$sid = $_GET["sid"];
$pid = $_GET["pid"];
$fid = $_GET["fid"];

if (isset($_GET["fid"]) && !ctype_digit($fid))
    $betray = true;
if (!ctype_digit($sid))
    $betray = true;
if (!ctype_digit($pid))
    $betray = true;

if (!$betray) {

    $schiff = new Schiffe($sid);
    $planet = new Planeten($pid);

    $glimes = $planet->typ == 'lm' || $planet->typ == 'om' || $planet->typ == 'mm' ? 24 : 50;


    if ($schiff->position->system->id != $planet->position->system->id || $schiff->position->x != $planet->position->x || $schiff->position->y != $planet->position->y || $schiff->position->orbit == 0 || $planet->skill->basis == 1 || $schiff->tarnung == 1 || $planet->schildstatus == 1)
        $betray = true;
}

if ($betray) {
    echo 'Es ist ein Fehler aufgetreten....';
} else {

//******************************

    function feuern(&$angreifer, &$verteidiger, $fid) {
//Hinfeuern
        $fehler = "";
        if ($angreifer->phaser >= $angreifer->maxphaser)
            $fehler = "Phaser sind &uuml;berhitzt! Warte einen Tick bis sie sich abgek&uuml;hlt haben.<br />";
        if ($angreifer->energie < 1)
            $fehler = "Dein Schiff hat keine Energie mehr zum feuern!<br />";
        if ($angreifer->tarnung == 1)
            $fehler = "Dein Schiff ist getarnt. DU kannst nicht feuern!<br />";
        if ($angreifer->position->orbit == 0 || $angreifer->position->x != $verteidiger->position->x || $angreifer->position->y != $verteidiger->position->y || $angreifer->position->system->id != $verteidiger->position->system->id)
            $fehler = "Du bist nicht im Orbit eines Planeten!<br />";
        if ($angreifer->besitzer->level <= 3 || $verteidiger->besitzer->level <= 3)
            $fehler = "Du oder dein Opfer ist noch im Schutzzustand!<br />";
        if ($verteidiger->heimat == 1)
            $fehler = "Du kannst keine Heimatplaneten angreifen!<br />";

        if ($fehler != "")
            echo "<span style=\"color:red;font-weight:bold;\">$fehler</span>"; else {
            $text = "Schiff von " . $angreifer->besitzer->nickname . " greift deine Geb&auml;ude auf deinem Planeten " . $verteidiger->name . "(ID " . $verteidiger->id . ") in Sektor " . $angreifer->position->x . "|" . $angreifer->position->y . " an ( im " . $angreifer->position->system->name . "-System )!";
            $amount = $angreifer->laser;
            $angreifer->energie--;
            $angreifer->phaser++;
            mysql_query("UPDATE schiffe SET energie=energie-1,phaser=phaser+1 WHERE id='$angreifer->id'");
            if ($verteidiger->feld[$fid]->hull - $amount > 0) {
                $verteidiger->feld[$fid]->hull-=$amount;
                $verteidiger->feld[$fid]->save();
                echo 'Das Geb&auml;ude nimmt Schaden und besitzt noch ', $verteidiger->feld[$fid]->hull, ' Intigrit&auml;t!<br />';
            } else {

                //ABFRAGEN der gebäude und neue werte setzen
                if ($verteidiger->feld[$fid]->was == 2) {
                    $verteidiger->frachtraum->max-=500;
                    $verteidiger->maxenergie-=20;
                    mysql_query("UPDATE planeten SET lager=lager-500,maxenergie=maxenergie-20 WHERE id='$verteidiger->id'");
                }
                if ($verteidiger->feld[$fid]->was == 5 || $verteidiger->feld[$fid]->was == 17) {
                    $verteidiger->laser-=6;
                    mysql_query("UPDATE planeten SET laser=laser-6 WHERE id='$verteidiger->id'");
                }
                if ($verteidiger->feld[$fid]->was == 6 || $verteidiger->feld[$fid]->was == 16) {
                    $verteidiger->maxschilde-=80;
                    if ($verteidiger->schilde > $verteidiger->maxschilde)
                        $verteidiger->schilde = $verteidiger->maxschilde; mysql_query("UPDATE planeten SET schilde='$verteidiger->schilde',maxschilde='$verteidiger->maxschilde' WHERE id='$verteidiger->id'");
                }
                if ($verteidiger->feld[$fid]->was == 3 || $verteidiger->feld[$fid]->was == 7 || $verteidiger->feld[$fid]->was == 13) {
                    $verteidiger->maxenergie-=20;
                    if ($verteidiger->energie > $verteidiger->maxenergie)
                        $verteidiger->energie = $verteidiger->maxenergie; mysql_query("UPDATE planeten SET energie='$verteidiger->energie',maxenergie='$verteidiger->maxenergie' WHERE id='$verteidiger->id'");
                }
                if ($verteidiger->feld[$fid]->was == 12) {
                    $verteidiger->lager-=200;
                    mysql_query("UPDATE planeten SET lager=lager-200 WHERE id='$verteidiger->id'");
                }

                $verteidiger->feld[$fid]->was = 0;
                $verteidiger->feld[$fid]->bauzeit = 0;
                $verteidiger->feld[$fid]->hull = 60;
                $verteidiger->feld[$fid]->aktiv = 1;
                $verteidiger->feld[$fid]->save();

                echo 'Das Geb&auml;ude wurde vernichtet!<br />';
                $text.=" und vernichtete ein Geb&auml;ude!!";
            }
//Logbuch
            $wer = $angreifer->besitzer;
            $wen = $verteidiger->besitzer;
            $wann = date("Y-m-d H:i:s");
//mysql_query("INSERT INTO logbuch (was,wann,wer,wen) VALUES ('$text','$wann','$wer','$wen')") or die(mysql_error());

            $eintrag = new Logbuch("typ", "neu");
            $eintrag->x = $angreifer->position->x;
            $eintrag->y = $angreifer->position->y;
            $eintrag->zeit = date("Y-m-d H:i:s");
            $eintrag->system = $angreifer->position->system->id;
            $eintrag->typ = "Kampf Planet";
            $eintrag->klasse = "Eingang";
            $eintrag->text = $text;
            $eintrag->initiator = $angreifer->besitzer->id;
            $eintrag->betroffener = $verteidiger->besitzer->id;
            $eintrag->save();




            $angreifer->kampftick("3", $verteidiger);

//ende zurück
        }
    }

//*****************************



    $stunde = date("H");

    if ($schiff->besitzer->id == 2)
        die("Dein Schiff wurde zerst&ouml;rt");
//preufen auf reigabe
    $pcounter = 0;
    $pwas = 0;
    for ($i = 1; $i <= $glimes; $i++)
        if ($planet->feld[$i]->was > 0) {
            $pwas = $planet->feld[$i]->was;
            $pcounter++;
        }
    if ($pcounter == 0) {
        mysql_query("UPDATE planeten SET besitzer=13 WHERE id='" . $planet->id . "'");
        include("clear_lite.php");
    }

//ende pruefen
//feuern
    if ($fid > 0) {
        if ($planet->feld[$fid]->was == 30 && $pcounter > 1) {
            
        } else {
            feuern($schiff, $planet, $fid);
        }
    }

//preufen auf reigabe
    $pcounter = 0;
    $pwas = 0;
    for ($i = 1; $i <= $glimes; $i++)
        if ($planet->feld[$i]->was > 0) {
            $pwas = $planet->feld[$i]->was;
            $pcounter++;
        }
    if ($pcounter == 0) {
        mysql_query("UPDATE planeten SET besitzer=13 WHERE id='" . $planet->id . "'");
        include("clear_lite.php");
    }

//ende pruefen



    echo '<h3>Planetenangriff</h3><br />';

    echo '<table class="bordered">';

    for ($i = 1; $i <= $glimes; $i++) {
        if (($i == 1 || $i == 21 || $i == 31 || $i == 41 || $i == 11) && $glimes == 50)
            echo '<tr>';
        if (($i == 7 || $i == 13 || $i == 19) && $glimes == 24)
            echo '<tr>';
        if ($planet->feld[$i]->was == 0) {
            switch ($planet->feld[$i]->untergrund) {
                case "f": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "waldn.png" : "forest.png";
                        $mouse = "Wald";
                        break;
                    }
                case "g": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "wiesen.png" : "grass.png";
                        $mouse = "Wiese";
                        break;
                    }
                case "w": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "wassern.png" : "water.png";
                        $mouse = "Wasser";
                        break;
                    }
                case "m": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "gebirgen.png" : "mountain.png";
                        $mouse = "Gebirge";
                        break;
                    }
                case "d": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "wuesten.png" : "desert.png";
                        $mouse = "W&uuml;ste";
                        break;
                    }
                case "i": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "eisn.png" : "ice.png";
                        $mouse = "Eis";
                        break;
                    }
                case "l": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "lavan.png" : "lava.png";
                        $mouse = "Lava";
                        break;
                    }
                case "v": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "vulkann.png" : "vulkan.png";
                        $mouse = "Vulkan";
                        break;
                    }
                case "s": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "gesteinn.png" : "gestein.png";
                        $mouse = "Lavagestein";
                        break;
                    }
                case "im": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "eisbergn.png" : "icem.png";
                        $mouse = "Eisberg";
                        break;
                    }
                case "fl": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "packeisn.png" : "frozen.png";
                        $mouse = "gefrorener See";
                        break;
                    }
                case "dm": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "wuestengebirgen.png" : "dmountain.png";
                        $mouse = "W&uuml;stengebirge";
                        break;
                    }
                case "is": {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "spalten.png" : "ispalte.png";
                        $mouse = "Kernspalte";
                        break;
                    }
            }
            echo '<td style="background-repeat:no-repeat;height:34px;width:32px;background-image:url(\'images/buildings/' . $bild . '\');" onmouseover="Tip(\'<b>', $mouse, '</b>\')" onmouseout="UnTip()">&nbsp;</td>';
        }
        if ($planet->feld[$i]->was > 0) {
            $url = "destroy.php";
            switch ($planet->feld[$i]->was) {
                case 1: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "baustofffabrikn.png" : "bm.gif";
                        $mouse = "baustoff";
                        break;
                    }
                case 2: {
                        if ($planet->feld[$i]->untergrund == 'g')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "lagerwiesen.png" : "lager.gif";
                        if ($planet->feld[$i]->untergrund == 'd')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "lagerwuesten.png" : "lagerwueste.png";
                        if ($planet->feld[$i]->untergrund == 'i')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "lagereisn.png" : "lagereis.png";
                        $mouse = "lager";
                        break;
                    }
                case 3: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "solarkomplexn.png" : "solarkomplex.png";
                        $mouse = "solar";
                        break;
                    }
                case 4: {
                        $url = "createship.php";
                        $bild = "werft.gif";
                        $mouse = "werft";
                        break;
                    }
                case 5: {
                        if ($planet->feld[$i]->untergrund == 'i')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "kanoneeisn.png" : "plasma.gif";
                        if ($planet->feld[$i]->untergrund == 'd')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "kanonewuesten.png" : "kanonewueste.png";
                        $mouse = "plasma";
                        break;
                    }
                case 6: {
                        $url = "schilde.php";
                        if ($planet->feld[$i]->untergrund == 'i')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "schildturmeisn.png" : "schild.gif";
                        if ($planet->feld[$i]->untergrund == 'd')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "schildturmwuesten.png" : "schildturmwueste.png";
                        $mouse = "schilde";
                        break;
                    }
                case 7: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "turbinenn.png" : "turbinen.png";
                        $mouse = "wasser";
                        break;
                    }
                case 8: {
                        if ($planet->feld[$i]->untergrund == 'm')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "minegebirgen.png" : "mine.gif";
                        if ($planet->feld[$i]->untergrund == 'dm')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "minenwuestengebirgen.png" : "minewuestengebirge.png";
                        $mouse = "mine";
                        break;
                    }
                case 9: {
                        if ($planet->feld[$i]->untergrund == 'i')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "duraniumeisn.png" : "dura.gif";
                        if ($planet->feld[$i]->untergrund == 'd')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "duraniumwuesten.png" : "duraniumwueste.png";
                        $mouse = "duranium";
                        break;
                    }
                case 10: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "forschungskomplexn.png" : "forschungskomplex.png";
                        $mouse = "forschung";
                        break;
                    }
                case 12: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "lagersystemgebirgen.png" : "lagersystemgebirge.png";
                        $mouse = "lagerhoehle";
                        break;
                    }
                case 13: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "hitzekraftwerkn.png" : "hitzekraftwerk.png";
                        $mouse = "lavawerk";
                        break;
                    }
                case 14: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "soriumvulkann.png" : "soriumvulkan.png";
                        $mouse = "sorium";
                        break;
                    }
                case 16: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "schildturmgesteinn.png" : "schildturmgestein.png";
                        $url = "schilde.php";
                        $mouse = "schilde";
                        break;
                    }
                case 17: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "kanonelavan.png" : "kanonelava.png";
                        $mouse = "plasma";
                        break;
                    }
                case 21: {
                        if ($planet->feld[$i]->untergrund == 'g')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "beschleunigerwiesen.png" : "teilchen.png";
                        if ($planet->feld[$i]->untergrund == 'i')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "beschleunigereisn.png" : "beschleunigereis.png";
                        if ($planet->feld[$i]->untergrund == 'd')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "beschleunigerwuesten.png" : "beschleunigerwueste.png";
                        $mouse = "antimaterie";
                        break;
                    }
                case 22: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "tritaniumwuesten.png" : "tritanium.png";
                        $mouse = "tritanium";
                        break;
                    }
                case 23: {
                        if ($planet->feld[$i]->untergrund == 'm')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "diliminegebirgen.png" : "dilimine.png";
                        if ($planet->feld[$i]->untergrund == 'im')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "dilimineeisn.png" : "dilimineeis.png";
                        $mouse = "dilimine";
                        break;
                    }
                case 24: {
                        if ($planet->feld[$i]->untergrund == 'g')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "fusionn.png" : "fusion.png";
                        if ($planet->feld[$i]->untergrund == 'i')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "fusioneisn.png" : "fusioneis.png";
                        $mouse = "fusion";
                        break;
                    }
                case 25: {
                        $bild = $stunde >= 19 || $stunde <= 7 ? "geokraftwerkn.png" : "geokraftwerk.png";
                        $mouse = "geo";
                        break;
                    }
                case 26: {
                        if ($planet->feld[$i]->untergrund == 'g')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "doppeln.png" : "doppel.png";
                        if ($planet->feld[$i]->untergrund == 'i')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "doppeleisn.png" : "doppeleis.png";
                        if ($planet->feld[$i]->untergrund == 'd')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "doppelwuesten.png" : "doppelwueste.png";
                        $mouse = "doppel";
                        break;
                    }
                case 27: {
                        if ($planet->feld[$i]->untergrund == 'g')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "torpedowiesen.png" : "torpedofabrik.png";
                        if ($planet->feld[$i]->untergrund == 'i')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "torpedoeisn.png" : "torpedofabrikeis.png";
                        if ($planet->feld[$i]->untergrund == 'd')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "torpedowuesten.png" : "torpedofabrikwueste.png";
                        if ($planet->feld[$i]->untergrund == 's')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "torpedogesteinn.png" : "torpedofabrikgestein.png";
                        $mouse = "torpedo";
                        break;
                    }
                case 28: {
                        if ($planet->feld[$i]->untergrund == 'g')
                            $bild = $stunde >= 19 || $stunde <= 7 ? $npcbild . "n.png" : $npcbild . ".png";
                        $mouse = "npc";
                        break;
                    }
                case 30: {
                        if ($planet->feld[$i]->untergrund == 'g')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "zentralen.png" : "zentrale.png";
                        if ($planet->feld[$i]->untergrund != 'g')
                            $bild = $stunde >= 19 || $stunde <= 7 ? "basisn.png" : "basis.png";
                        $mouse = $planet->feld[$i]->untergrund == 'g' ? "zentrale" : "basis";
                        break;
                    }
            }
            if (($planet->feld[$i]->bauzeit == 0 && $planet->feld[$i]->was != 30) || ($planet->feld[$i]->was == 30 && $pcounter == 1))
                echo '<td style="background-repeat:no-repeat;background-image:url(\'images/buildings/' . $bild . '\');width:32px;height:34px;" onmouseover="Tip(', $planet->feld[$i]->aktiv > 0 ? $mouse . 'online' : $mouse . 'offline', ')" onmouseout="UnTip()"><a style="display:block;padding:8px 12px;text-decoration:none;" href="bombplanet.php?pid=', $pid, '&fid=', $i, '&sid=', $sid, '">&nbsp;</a></td>';
            if ($planet->feld[$i]->bauzeit > 0)
                echo '<td style="background-repeat:no-repeat;background-image:url(\'images/buildings/' . $bild . '\');width:32px;height:34px;" onmouseover="Tip(\'', $mouse, ' | Fertig in ', $planet->feld[$i]->bauzeit, ' Ticks \')" onmouseout="UnTip()">&nbsp;</td>';
            if ($planet->feld[$i]->was == 30 && $pcounter > 1)
                echo '<td style="background-repeat:no-repeat;background-image:url(\'images/buildings/' . $bild . '\');width:32px;height:34px;" onmouseover="Tip(', $planet->feld[$i]->aktiv > 0 ? $mouse . 'online' : $mouse . 'offline', ')" onmouseout="UnTip()">&nbsp;</td>';
        }

        if ($i % 10 == 0 && $glimes == 50)
            echo '</tr>';
        if ($i % 6 == 0 && $glimes == 24)
            echo '</tr>';
    }
    echo '</table>';



    echo '<br />';
    $bu = new Button("schiffe.php?sid=".$sid,"zurück zum Schiff");
    $bu->printme();
}
include("foot.php");
?>
