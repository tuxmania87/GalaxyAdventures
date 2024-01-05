<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';

include_once 'connect.php';

$verbindung = get_verbindung();

$schiffid = $_GET['sid'];
$systemid = $_GET['id'];
if (!ctype_digit($systemid)) {
    exit('Fehler: ID Konflikt!');
}

$system = new System($systemid);

$check = false;
$abfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE `system`=0 AND x='".$system->x."' AND y='".$system->y."' AND besitzer='".$_SESSION['Id']."'");
while ($row = mysqli_fetch_array($abfrage)) {
    $check = true;
}

if (!$check) {
    exit('FEHLER');
}

// QUestabfrage

$abfrage = mysqli_query($verbindung, "SELECT erfolge.id FROM erfolge,quests WHERE erfolge.qid=quests.id AND erfolge.uid='".$_SESSION['Id']."' AND erfolge.erledigt=0 AND quests.zusatz='$systemid'");
while ($row = mysqli_fetch_assoc($abfrage)) {
    mysqli_query($verbindung, "UPDATE erfolge SET erledigt=1 WHERE erledigt=0 AND id='".$row['id']."'") or exit($verbindung->error);
    echo '<a href="showquest.php">Quest erledigt!</a><br />';
}

// SCAN..
$count = 0;
$planet = [];
$abfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE `system`='$systemid' AND besitzer!=2 AND besitzer!=''");
while ($row = mysqli_fetch_array($abfrage)) {
    ++$count;
    $planet[] = $row['besitzer'];
}
echo '<h3>Scan vom ',$system->name,'-System</h3>';
echo '<br />besiedelte Planeten: ',$count,'<br />Gefundene Spezies: ';
for ($i = 0; $i < sizeof($planet); ++$i) {
    $x = new Account($planet[$i]);
    echo $x->nickname.',';
}

$count = 0;
$schiff = [];
$abfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE `system`='$systemid' AND besitzer!=2 AND besitzer!=''");
while ($row = mysqli_fetch_array($abfrage)) {
    ++$count;
    $schiff[] = $row['besitzer'];
}

echo '<br /><br />gefunden Schiffe: ',$count,'<br />Schiffssignaturen: ';
for ($i = 0; $i < sizeof($schiff); ++$i) {
    $x = new Account($schiff[$i]);
    echo $x->nickname.',';
}
echo '<br /><br /><a href="schiffe.php?sid=',$schiffid,'">zur&uuml;ck</a>';
