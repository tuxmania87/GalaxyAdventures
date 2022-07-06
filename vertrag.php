<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

$id = $_SESSION["Id"];
$ich = new Account($id);

//mitglied einladen
if ($_POST["sent"] == 2) {
    //pruefen auf mitglieder Anzahl
    $user = new Account($_POST["userid"]);
    $weiter = true;
    if ($user->gruppe > 0) {
        $weiter = false;
        echo "Fehler: der User ist bereits in einer Gruppe<br />";
    }
    if (mysql_num_rows(mysql_query("SELECT * FROM account WHERE gruppe='" . $ich->gruppe . "'")) > 5) {
        $weiter = false;
        echo "Fehler: die Gruppe ist voll<br />";
    }
    if ($weiter) {
        //einladung setzen
        mysql_query("UPDATE account SET gruppe='" . $ich->gruppe . "',gruppeinvite=1 WHERE id='" . $user->id . "'");
        echo "Der User wurde eingeladen!<br />";
    }
}

//ja / nein
if ($_GET["no"] == 1) {
    if ($ich->gruppe == $ich->id)
        mysql_query("UPDATE account SET gruppe=0,gruppeinvite=0 WHERE gruppe='" . $ich->id . "'");
    mysql_query("UPDATE account SET gruppe=0,gruppeinvite=0 WHERE id='" . $ich->id . "'");
    $ich->gruppeinvite = 0;
    $ich->gruppe = 0;
}
if ($_GET["yes"] == 1) {
    mysql_query("UPDATE account SET gruppeinvite=0 WHERE id='" . $ich->id . "'");
    $ich->gruppeinvite = 0;
}

//gruppe er�ffnen
if ($_GET["open"] == 1) {
    if ($ich->gruppe > 0)
        echo "Fehler du bist bereits in einer Gruppe (ID:$ich->gruppe)";
    else {
        $ich->gruppe = $ich->id;
        mysql_query("UPDATE account SET gruppe=id,gruppeinvite=0 WHERE id='$id'");
        echo "Du hast erfolgreich eine Gruppe gegr&uuml;ndet";
    }
}

//arbeiten
if ($_GET["do"] == 1) {//zuruecknehmen
    $vid = $_GET["vid"];
    if (ctype_digit($vid)) {
        $abfrage1 = mysql_query("SELECT * FROM vertrag WHERE id='$vid'");
        while ($row1 = mysql_fetch_array($abfrage1)) {
            if ($row1["initiator"] == $_SESSION["Id"])
                mysql_query("DELETE FROM vertrag WHERE id='$vid'");
        }
    }
}

if ($_GET["do"] == 2) { //annehmen
    $vid = $_GET["vid"];
    if (ctype_digit($vid)) {
        $abfrage1 = mysql_query("SELECT * FROM vertrag WHERE id='$vid'");
        while ($row1 = mysql_fetch_array($abfrage1)) {
            if ($row1["partner"] == $_SESSION["Id"]) {
                if ($row1["handel"] == 1)
                    mysql_query("UPDATE vertrag SET nap=1,valid=1,verteidigung=1 WHERE id='$vid'");
                if ($row1["verteidigung"] == 1)
                    mysql_query("UPDATE vertrag SET nap=1,valid=1 WHERE id='$vid'");
                if ($row1["nap"] == 1)
                    mysql_query("UPDATE vertrag SET valid=1 WHERE id='$vid'");
                if ($row1["quest"] == 1)
                    mysql_query("UPDATE vertrag SET valid=1 WHERE id='$vid'");
            }
        }
    }
}

if ($_GET["do"] == 3) {//ablehnen
    $vid = $_GET["vid"];
    if (ctype_digit($vid)) {
        $abfrage1 = mysql_query("SELECT * FROM vertrag WHERE id='$vid'");
        while ($row1 = mysql_fetch_array($abfrage1)) {
            if ($row1["partner"] == $_SESSION["Id"] || $row1["initiator"] == $_SESSION["Id"])
                mysql_query("DELETE FROM vertrag WHERE id='$vid'");
        }
    }
}

if ($_GET["do"] == 4) {//aufloesen
    $vid = $_GET["vid"];
    if (ctype_digit($vid)) {
        $abfrage1 = mysql_query("SELECT * FROM vertrag WHERE id='$vid'");
        while ($row1 = mysql_fetch_array($abfrage1)) {
            if ($row1["partner"] == $_SESSION["Id"] || $row1["initiator"] == $_SESSION["Id"]) {
                mysql_query("DELETE FROM vertrag WHERE id='$vid'");
                //richtigen vertrage l�schen
            }
        }
    }
}

if ($_GET["open"] == 1) {
    if ($ich->gruppe == 0) {
        $ich->gruppe = $ich->id;
        mysql_query("UPDATE account SET gruppe=id WHERE id='" . $ich->id . "'");
    }
}

if ($_POST["nap"] == 1) { //nap anbieten
    $vpartner = $_POST["vpartner"];
    if (ctype_digit($vpartner)) {  //anbieten
        mysql_query("INSERT INTO vertrag (initiator,partner,nap) VALUES ('$id','$vpartner','1')");
        echo 'Vertrag wurde angeboten';
    }
}

if ($_POST["def"] == 1) { //verteidigung anbieten
    $vpartner = $_POST["vpartner"];
    if (ctype_digit($vpartner)) {  //anbieten
        mysql_query("INSERT INTO vertrag (initiator,partner,verteidigung) VALUES ('$id','$vpartner','1')");
        echo 'Vertrag wurde angeboten';
    }
}

if ($_POST["handel"] == 1) { //handel
    $vpartner = $_POST["vpartner"];
    if (ctype_digit($vpartner)) {  //anbieten
        mysql_query("INSERT INTO vertrag (initiator,partner,handel) VALUES ('$id','$vpartner','1')");
        echo 'Vertrag wurde angeboten';
    }
}

if ($_POST["quest"] == 1) { //quest
    $vpartner = $_POST["vpartner"];
    if (ctype_digit($vpartner)) {  //anbieten
        mysql_query("INSERT INTO vertrag (initiator,partner,quest) VALUES ('$id','$vpartner','1')");
        echo 'Vertrag wurde angeboten';
    }
}

//datenerfassung
$nap = array();
$defend = array();
$handel = array();
$quest = array();
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE (initiator='$id' OR partner='$id') AND nap=1 AND valid=1");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$nap[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE initiator='$id' AND nap=1 AND valid=0");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$nap[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE partner='$id' AND nap=1 AND valid=0");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$nap[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE (initiator='$id' OR partner='$id') AND verteidigung=1 AND valid=1");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$defend[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE initiator='$id' AND verteidigung=1 AND valid=0");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$defend[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE partner='$id' AND verteidigung=1 AND valid=0");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$defend[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE (initiator='$id' OR partner='$id') AND handel=1 AND valid=1");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$handel[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE initiator='$id' AND handel=1 AND valid=0");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$handel[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE partner='$id' AND handel=1 AND valid=0");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$handel[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE (initiator='$id' OR partner='$id') AND quest=1 AND valid=1");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$quest[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE initiator='$id' AND quest=1 AND valid=0");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$quest[] = $abfrage;
$abfrage = mysql_query("SELECT COUNT(*) FROM vertrag WHERE partner='$id' AND quest=1 AND valid=0");
$abfrage = mysql_fetch_array($abfrage);
$abfrage = $abfrage[0];
$quest[] = $abfrage;

echo '<h2>Vertragsverwaltung</h2>';
echo '<table class="invitetable" style="text-align:center;"><tr><th>Vertr&auml;ge</th><th>g&uuml;ltig</th><th>angeboten</th><th>angeboten bekommen</th></tr>';
echo '<tr><td>Nicht-Angriffspakt</td><td>(', $nap[0], ')</td><td>(', $nap[1], ')</td><td>(', $nap[2], ')</td><td><a href="vertraginfo.php?kategorie=nap">anbieten</a></tr>';
echo '<tr><td>Verteidigungspakt</td><td>(', $defend[0], ')</td><td>(', $defend[1], ')</td><td>(', $defend[2], ')</td><td><a href="vertraginfo.php?kategorie=defend">anbieten</a></td></tr>';
echo '<tr><td>Handelsvertrag</td><td>(', $handel[0], ')</td><td>(', $handel[1], ')</td><td>(', $handel[2], ')</td><td><a href="vertraginfo.php?kategorie=defend">anbieten</a></td></tr>';
echo '</table>';

echo '<br />Um Vertr&auml;ge abzuschliessen, klicke auf das entsprechende Symbol.<br /><br />Nicht-Angriffspakt: Schiffe Auf ALARM Rot greifen deinen Vertragspartner nicht an.<br />Verteidigungspakt: Wenn du in einem Sektor angegriffen wirst, greift dein Vertragspartner mit an. Beinhaltet NAP';

echo '<br /><hr /><br /><h3>Questgruppe</h3>';
if ($ich->gruppe == 0 && !$ich->gruppeinvite) {
    echo '<i>Du bist in keiner Gruppe</i><br /><br />';
    echo '<a href="vertrag.php?open=1">Gruppe er&ouml;ffnen</a>';
}
if ($ich->gruppe > 0 && !$ich->gruppeinvite) {
    echo '<h4>Gruppen-ID ', $ich->gruppe, '</h4>Mitglieder: <br /><br />';
    $gabfrage = mysql_query("SELECT id FROM account WHERE gruppe = '" . $ich->gruppe . "'");
    while ($row = mysql_fetch_array($gabfrage)) {
        $usr = new Account($row[0]);
        echo $usr->nickname, ' ', $usr->id == $ich->gruppe ? '<span style="color:red;">( Leiter )</span>' : '', '<br />';
    }
    if ($ich->gruppe == $ich->id)
        echo '<br /><form action="vertrag.php" method="post">Spieler mit der ID <input type="text" size="2" name="userid" /> in Gruppe einladen!<input type="hidden" name="sent" value="2" /><br /><input type="submit" value="einladen!" /></form>';
    echo '<br /><a href="vertrag.php?no=1">', $ich->gruppe == $ich->id ? 'Gruppe aufl&ouml;sen' : 'Aus Gruppe austreten', '</a><br />';
}
if ($ich->gruppe > 0 && $ich->gruppeinvite) {
    $inviter = new Account($ich->gruppe);
    echo '<i>Du bist in keiner Gruppe</i><br /><br />';
    echo 'Der Spieler: ', $inviter->nickname, ' hat dich in seine Gruppe eingeladen! <a href="vertrag.php?yes=1"><span style="color:green;font-weight:bold;">annehmen</span></a> | <a href="vertrag.php?no=1"><span style="color:red;font-weight:bold;">ablehnen</span></a><br /><br />';
    echo '<a href="vertrag.php?open=1">Gruppe er&ouml;ffnen</a>';
}
echo '<br />';
include("foot.php");
?>
