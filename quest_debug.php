<?php
/**
 * quest_debug.php — temporäre Diagnosedatei, danach löschen!
 * Prüft ob die Quest Engine v2 korrekt eingerichtet ist.
 */
include 'head.php';
include 'klassen.php';

$verbindung = get_verbindung();
$uid = (int)$_SESSION['Id'];

echo '<pre style="font-family:monospace; color:#c0d8ff; background:#080f1a; padding:16px; font-size:13px;">';
echo "=== Quest Engine v2 — Diagnose (uid=$uid) ===\n\n";

// 1. Tabellen vorhanden?
$tables = ['quest_templates', 'quest_steps', 'player_quests', 'player_quest_steps'];
echo "--- Tabellen ---\n";
foreach ($tables as $t) {
    $q = mysqli_query($verbindung, "SHOW TABLES LIKE '$t'");
    $exists = mysqli_num_rows($q) > 0;
    echo ($exists ? "  ✓ " : "  ✗ FEHLT: ") . $t . "\n";
}

// 2. Quest-Templates
echo "\n--- Quest Templates ---\n";
$q = mysqli_query($verbindung, "SELECT id, titel, aktiv FROM quest_templates ORDER BY id");
if (!$q) {
    echo "  FEHLER: " . mysqli_error($verbindung) . "\n";
} else {
    $n = 0;
    while ($r = mysqli_fetch_assoc($q)) {
        echo "  #" . $r['id'] . " [" . ($r['aktiv'] ? 'aktiv' : 'inaktiv') . "] " . $r['titel'] . "\n";
        $n++;
    }
    if ($n === 0) echo "  (keine Einträge — Migration noch nicht eingespielt?)\n";
}

// 3. Aktive Player-Quests dieses Spielers
echo "\n--- Aktive Quests von uid=$uid ---\n";
$q = mysqli_query($verbindung,
    "SELECT pq.id, pq.quest_id, qt.titel, pq.status
       FROM player_quests pq
       JOIN quest_templates qt ON qt.id = pq.quest_id
      WHERE pq.uid = $uid ORDER BY pq.id"
);
if (!$q) {
    echo "  FEHLER: " . mysqli_error($verbindung) . "\n";
} else {
    $n = 0;
    while ($r = mysqli_fetch_assoc($q)) {
        echo "  pq_id=" . $r['id'] . " quest=" . $r['quest_id'] . " [" . $r['status'] . "] " . $r['titel'] . "\n";

        // Schritte
        $sq = mysqli_query($verbindung,
            "SELECT pqs.id, pqs.fortschritt, pqs.max, pqs.erledigt, qs.trigger_typ, qs.beschreibung
               FROM player_quest_steps pqs
               JOIN quest_steps qs ON qs.id = pqs.step_id
              WHERE pqs.player_quest_id = " . (int)$r['id']
        );
        while ($s = mysqli_fetch_assoc($sq)) {
            echo "    Schritt [" . $s['trigger_typ'] . "] " . $s['beschreibung']
               . " → " . $s['fortschritt'] . "/" . $s['max']
               . ($s['erledigt'] ? " ✓" : "") . "\n";
        }
        $n++;
    }
    if ($n === 0) echo "  (keine) — Quest erst via quest_v2.php annehmen!\n";
}

// 4. onScan() simulieren und Ergebnis zeigen
echo "\n--- Simulation: onScan(uid=$uid) ---\n";
$q = mysqli_query($verbindung,
    "SELECT pqs.id, pqs.fortschritt, pqs.max, pqs.erledigt
       FROM player_quest_steps pqs
       JOIN quest_steps qs ON qs.id = pqs.step_id
       JOIN player_quests pq ON pq.id = pqs.player_quest_id
      WHERE pq.uid = $uid
        AND pq.status = 'active'
        AND qs.trigger_typ = 'scan_systems'
        AND pqs.erledigt = 0"
);
if (!$q) {
    echo "  FEHLER: " . mysqli_error($verbindung) . "\n";
} else {
    $n = 0;
    while ($r = mysqli_fetch_assoc($q)) {
        $neu  = min((int)$r['fortschritt'] + 1, (int)$r['max']);
        $done = $neu >= (int)$r['max'] ? 1 : 0;
        echo "  → pqs_id=" . $r['id'] . " würde: fortschritt " . $r['fortschritt'] . " → $neu / " . $r['max'] . ($done ? " (ERLEDIGT)" : "") . "\n";
        $n++;
    }
    if ($n === 0) echo "  Keine aktiven scan_systems-Schritte gefunden.\n  → Entweder keine Quest angenommen, oder Schritt schon erledigt.\n";
}

echo "\n=== Ende Diagnose ===\n";
echo '</pre>';
echo '<p><a href="quest_v2.php" style="color:#7ab8ff;">→ Quest annehmen</a> &nbsp; <a href="quest_admin.php" style="color:#7ab8ff;">→ Admin-Editor</a></p>';

include 'foot.php';
