<?php

include("head.php");
include("navlogged.php");
include("klassen.php");
$id = $_SESSION["Id"];

echo '<h2>Vertragsverwaltung</h2><br />';

if ($_GET["kategorie"] == 'nap')
    echo '<h3>Nicht-Angriffspakte</h3>';
if ($_GET["kategorie"] == 'defend')
    echo '<h3>Verteidigungspakte</h3>';
if ($_GET["kategorie"] == 'handel')
    echo '<h3>Handelsvertrag</h3>';
if ($_GET["kategorie"] == 'quest')
    echo '<h3>Questb&uuml;ndniss</h3>';

echo '<br /><table class="invitetable" style="text-align:center;"><tr><th>Typ</th><th>Initiator</th><th>Adressat</th></tr>';
if ($_GET["kategorie"] == 'nap')
    $abfragevar = mysqli_query($verbindung, "SELECT * FROM vertrag WHERE (initiator='$id' OR partner='$id') AND valid=1 AND nap=1");
if ($_GET["kategorie"] == 'defend')
    $abfragevar = mysqli_query($verbindung, "SELECT * FROM vertrag WHERE (initiator='$id' OR partner='$id') AND valid=1 AND verteidigung=1");
if ($_GET["kategorie"] == 'handel')
    $abfragevar = mysqli_query($verbindung, "SELECT * FROM vertrag WHERE (initiator='$id' OR partner='$id') AND valid=1 AND handel=1");
if ($_GET["kategorie"] == 'quest')
    $abfragevar = mysqli_query($verbindung, "SELECT * FROM vertrag WHERE (initiator='$id' OR partner='$id') AND valid=1 AND quest=1");
while ($row = mysqli_fetch_array($abfragevar)) {
    $init = new Account($row["initiator"]);
    $part = new Account($row["partner"]);
    echo '<tr><td>', $_GET["kategorie"], '</td><td>', $init->nickname, '</td><td>', $part->nickname, '</td><td><a href="vertrag.php?do=4&vid=', $row["id"], '">Vertrag aufl&ouml;sen</a></tr>';
}
echo '</table><br /><table class="invitetable" style="text-align:center;"><tr><th>Typ</th><th>Initiator</th><th>Adressat</th><th>Status</th></tr>';
if ($_GET["kategorie"] == 'nap')
    $abfragevar = mysqli_query($verbindung, "SELECT * FROM vertrag WHERE (initiator='$id' OR partner='$id') AND valid=0 AND nap=1");
if ($_GET["kategorie"] == 'defend')
    $abfragevar = mysqli_query($verbindung, "SELECT * FROM vertrag WHERE (initiator='$id' OR partner='$id') AND valid=0 AND verteidigung=1");
if ($_GET["kategorie"] == 'handel')
    $abfragevar = mysqli_query($verbindung, "SELECT * FROM vertrag WHERE (initiator='$id' OR partner='$id') AND valid=0 AND handel=1");
if ($_GET["kategorie"] == 'quest')
    $abfragevar = mysqli_query($verbindung, "SELECT * FROM vertrag WHERE (initiator='$id' OR partner='$id') AND valid=0 AND quest=1");
while ($row = mysqli_fetch_array($abfragevar)) {
    $init = new Account($row["initiator"]);
    $part = new Account($row["partner"]);
    echo '<tr><td>', $_GET["kategorie"], '</td><td>', $init->nickname, '</td><td>', $part->nickname, '</td>';
    if ($row["initiator"] == $id)
        echo '<td><a href="vertrag.php?do=1&vid=', $row["id"], '">zur&uuml;cknehmen</a></td></tr>';
    else
        echo '<td><a href="vertrag.php?do=2&vid=', $row["id"], '">annehmen</a>&nbsp;|&nbsp;<a href="vertrag.php?do=3&vid=', $row["id"], '">ablehnen</a></td></tr>';
}
echo '</table><br />';
if ($_GET["kategorie"] == 'nap') {  //anbieten
    echo '<form action="vertrag.php" method="post"><input type="hidden" name="nap" value="1">Spieler <input type="text" name="vpartner" size="2" /> einen Nicht Angriffspakt anbieten.<br />'; $bu=new Button("","anbieten"); $bu->printme(); echo '</form><br />';
}
if ($_GET["kategorie"] == 'defend') {  //anbieten
    echo '<form action="vertrag.php" method="post"><input type="hidden" name="def" value="1">Spieler <input type="text" name="vpartner" size="2" /> einen Verteidigungspakt anbieten.<br />'; $bu=new Button("","anbieten"); $bu->printme(); echo '</form>';
}
if ($_GET["kategorie"] == 'handel') {  //anbieten
    echo '<form action="vertrag.php" method="post"><input type="hidden" name="handel" value="1">Spieler <input type="text" name="vpartner" size="2" /> einen Handelspakt anbieten.<br />'; $bu=new Button("","anbieten"); $bu->printme(); echo '</form>';
}
if ($_GET["kategorie"] == 'quest') {  //anbieten
    echo '<form action="vertrag.php" method="post"><input type="hidden" name="quest" value="1">Spieler <input type="text" name="vpartner" size="2" /> ein Questb&uuml;ndniss anbieten.<br />'; $bu=new Button("","anbieten"); $bu->printme(); echo '</form>';
}
include("foot.php");
?>

