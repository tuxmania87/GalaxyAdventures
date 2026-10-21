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
$abfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE `system`=0 AND x='".$system->x."' AND y='".$system->y."' AND besitzer='".intval($_SESSION['Id'])."'");
while ($row = mysqli_fetch_array($abfrage)) {
    $check = true;
}

if (!$check) {
    exit('FEHLER');
}

// Quest Engine v1 (alt) — Einzelsystem-Quests
$abfrage = mysqli_query($verbindung, "SELECT erfolge.id FROM erfolge,quests WHERE erfolge.qid=quests.id AND erfolge.uid='".intval($_SESSION['Id'])."' AND erfolge.erledigt=0 AND quests.zusatz='$systemid'");
while ($row = mysqli_fetch_assoc($abfrage)) {
    mysqli_query($verbindung, "UPDATE erfolge SET erledigt=1 WHERE erledigt=0 AND id='".$row['id']."'") or exit($verbindung->error);
    echo '<a href="showquest.php">Quest erledigt!</a><br />';
}

// Quest Engine v2 — scan_systems Trigger
$uid_for_scan = (int)$_SESSION['Id'];
$verbindung_debug = get_verbindung();
$q_before = mysqli_query($verbindung_debug, "SELECT id, fortschritt, max, erledigt FROM player_quest_steps WHERE id IN (SELECT pqs.id FROM player_quest_steps pqs JOIN quest_steps qs ON qs.id=pqs.step_id JOIN player_quests pq ON pq.id=pqs.player_quest_id WHERE pq.uid=$uid_for_scan AND pq.status='active' AND qs.trigger_typ='scan_systems' AND pqs.erledigt=0)");
$before_rows = [];
while ($r = mysqli_fetch_assoc($q_before)) $before_rows[] = $r;

QuestEngine::onScan($uid_for_scan);

$q_after = mysqli_query($verbindung_debug, "SELECT id, fortschritt, max, erledigt FROM player_quest_steps WHERE id IN (SELECT id FROM player_quest_steps WHERE id IN (SELECT pqs.id FROM player_quest_steps pqs JOIN quest_steps qs ON qs.id=pqs.step_id JOIN player_quests pq ON pq.id=pqs.player_quest_id WHERE pq.uid=$uid_for_scan AND qs.trigger_typ='scan_systems'))");
$after_rows = [];
while ($r = mysqli_fetch_assoc($q_after)) $after_rows[] = $r;

echo '<div style="background:#1a0a00;border:1px solid #ff6600;padding:8px;font-family:monospace;font-size:12px;color:#ffcc88;margin:4px 0;">';
echo '<b>[DEBUG onScan] uid=' . $uid_for_scan . '</b><br>';
echo 'Vorher: ';
foreach ($before_rows as $br) echo 'pqs_id='.$br['id'].' fort='.$br['fortschritt'].'/'.$br['max'].' erledigt='.$br['erledigt'].' | ';
echo (empty($before_rows) ? '(keine aktiven scan_systems-Schritte gefunden!)' : '') . '<br>';
echo 'Nachher: ';
foreach ($after_rows as $ar) echo 'pqs_id='.$ar['id'].' fort='.$ar['fortschritt'].'/'.$ar['max'].' erledigt='.$ar['erledigt'].' | ';
echo (empty($after_rows) ? '(keine Zeilen)' : '') . '<br>';
echo 'mysqli_error nach onScan: ' . (mysqli_error($verbindung_debug) ?: 'kein Fehler');
echo '</div>';

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
for ($i = 0; $i < count($planet); ++$i) {
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
for ($i = 0; $i < count($schiff); ++$i) {
    $x = new Account($schiff[$i]);
    echo $x->nickname.',';
}
echo '<br /><br /><a href="schiffe.php?sid=',$schiffid,'">zur&uuml;ck</a>';
