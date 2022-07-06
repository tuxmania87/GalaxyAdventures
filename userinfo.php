<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

$id = $_GET["id"];
//BETR
$betray = false;
if (!ctype_digit($id))
    die("Fehler: ID-ung&uuml;tig!");

$account = new Account($id);

echo '<h3>Kolonisten Info ', $account->nickname, ' - Level: ', $account->level, '</h3>';

echo '<table class="bordered2"><tr><td><img src="', $account->bild == '' ? 'siedler.gif' : $account->bild, '" border="0" /></td><td>', nl2br(pruefetext($account->beschreibung)), '</td></tr></table><br />';


if ($account->allianz->id > 0) {
    echo $account->nickname, ' ist in der Allianz: ', $account->allianz->name, '<br />';
} else
    echo 'Spieler ist in keiner Allianz.<br />';
echo '<br /><br />';


//W Punkkt

$shipsum = new Frachtraum("", "dummy");
$q = mysql_query("select id from schiffe where typ='s' and besitzer=" . $id);
while ($r = mysql_fetch_array($q)) {
    $t_ship = new Schiffe($r["id"]);
    for ($i = 0; $i < sizeof($t_ship->frachtraum->fracht); $i++) {
        $shipsum->fracht[$i + 1]->anzahl += $t_ship->frachtraum->fracht[$i]->anzahl;
    }
}

$planetsum = new Frachtraum("", "dummy");
$q = mysql_query("select id from planeten where besitzer=" . $id);
while ($r = mysql_fetch_array($q)) {
    $t_ship = new Planeten($r["id"]);
    for ($i = 0; $i < sizeof($t_ship->frachtraum->fracht); $i++) {
        $planetsum->fracht[$i + 1]->anzahl += $t_ship->frachtraum->fracht[$i]->anzahl;
    }
}

echo '<h3>Wirtschafts Punkte</h3>';

echo '<table class="invitetable" style="text-align:center;"><tr><th>Material</th><th>&nbsp;</th><th>auf Schiffen</th><th>auf Planeten</th><th>Summe</th><th>Wichtung</th><th>ZeilenSumme</th></tr>';

$gesamt = 0;
for ($i = 1; $i < sizeof($shipsum->fracht); $i++) {
    if ($id == $_SESSION["Id"] || $_SESSION["Id"] < 10)
        echo '<tr><td>', $shipsum->fracht[$i]->name, '</td><td><img src="images/misc/' . $shipsum->fracht[$i]->bild . '" border="0" /></td><td>', $shipsum->fracht[$i]->anzahl, '</td><td>', $planetsum->fracht[$i]->anzahl, '</td><td>', $shipsum->fracht[$i]->anzahl + $planetsum->fracht[$i]->anzahl, '</td><td><span style="color:yellow;">*', $shipsum->fracht[$i]->wichtung, '</span></td><td><span style="color:green;font-weight:bold;">', ($shipsum->fracht[$i]->anzahl + $planetsum->fracht[$i]->anzahl) * $shipsum->fracht[$i]->wichtung . '</span></td></tr>';
    $gesamt+=(($shipsum->fracht[$i]->anzahl + $planetsum->fracht[$i]->anzahl) * $shipsum->fracht[$i]->wichtung);
}
echo '<tr></tr><tr><td>Gesamt</td><td></td><td></td><td><span style="color:green;font-weight:bold;">', $gesamt, '</span></td><td><span style="color:yellow;font-weight:bold;">*0.35</span></td><td><span style="color:red;font-weight:bold;">', $gesamt * 0.35, '</span></td></tr>';


echo '</table><br />';

$pr = array();

echo '<table class="invitetable" style="text-align:center;"><tr><th>Gebäude</th><th>Anzahl</th><th>Wichtung</th><th>Summe</th></tr>';

$abfrage = mysql_query("SELECT id FROM planeten WHERE besitzer=" . $id);
while ($row = mysql_fetch_array($abfrage)) {
    $pl = new Planeten($row[0]);
    $limes = $pl->typ[1] == 'm' ? 24 : 50;
    for ($i = 0; $i <= $limes; $i++) {
        $c_feld = $pl->feld[$i];
        if ($c_feld->rest_bauzeit == 0 && $c_feld->aktiv == 1 && $c_feld->bau->id > 0) {
            $pr[$c_feld->bau->id - 1]++;
        }
    }
}

$list = Bauplan_Gebaude::getCompleteListe();

$gesamt2 = 0;
for ($i = 0; $i < sizeof($list); $i++) {
    if ($id == $_SESSION["Id"] || $_SESSION["Id"] < 10)
        echo '<tr><td>', $list[$i]->name, '</td><td>', $pr[$i], '</td><td><span style="color:yellow;">', $list[$i]->wichtung, '</td><td><span style="color:green;font-weight:bold;">', $pr[$i] * $list[$i]->wichtung, '</span></td></tr>';
    $gesamt2+=($pr[$i] * $list[$i]->wichtung);
}

echo '<tr></tr><tr><td>Gesamt</td><td><span style="color:green;font-weight:bold;">', $gesamt2, '</span></td><td><span style="color:yellow;font-weight:bold;">*8</span></td><td><span style="color:red;font-weight:bold;">', $gesamt2 * 8, '</span></td></tr>';

echo '</table><br />';

echo '<span style="font-weight:bold;text-decoration:underline">Gesamtpunktzahl: </span>&nbsp;<span style="font-weight:bold;color:green">', ($gesamt * 0.35) + ($gesamt2 * 8), '</span>';

include("foot.php");
?>
