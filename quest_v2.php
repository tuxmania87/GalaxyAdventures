<?php
/**
 * quest_v2.php — Spieler-Quest-UI (Quest Engine v2)
 *
 * Zeigt:
 *  - Aktive Quests mit Fortschritt
 *  - Verfügbare Quests (annehmen)
 *  - Abgeschlossene Quests (Log)
 *
 * Actions via GET:
 *  ?action=accept&quest_id=X   — Quest annehmen
 *  ?action=complete&pq_id=X    — Quest abschließen (wenn bereit)
 */

include 'head.php';
include 'navlogged.php';
include 'klassen.php';

$verbindung = get_verbindung();
$ich        = new Account($_SESSION['Id']);
$uid        = (int)$_SESSION['Id'];

// ------------------------------------------------------------
//  Action-Handler
// ------------------------------------------------------------
$msg = '';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'accept' && isset($_GET['quest_id'])) {
        $quest_id = (int)$_GET['quest_id'];
        if ($quest_id > 0) {
            if (QuestEngine::accept($uid, $quest_id)) {
                $msg = '<span style="color:#00ff88;">Quest angenommen!</span>';
            } else {
                $msg = '<span style="color:#ff6644;">Quest konnte nicht angenommen werden (Voraussetzungen nicht erfüllt oder bereits aktiv).</span>';
            }
        }
    }

    if ($action === 'complete' && isset($_GET['pq_id'])) {
        $pq_id = (int)$_GET['pq_id'];
        if ($pq_id > 0) {
            // Erst aktuellen Fortschritt prüfen (für scan-basierte Trigger etc.)
            QuestEngine::checkAll($uid);
            if (QuestEngine::complete($uid, $pq_id)) {
                $msg = '<span style="color:#00ff88;">Quest abgeschlossen! Belohnung wurde gutgeschrieben.</span>';
            } else {
                $msg = '<span style="color:#ff6644;">Quest ist noch nicht abgeschlossen.</span>';
            }
        }
    }
}

// Fortschritt nach jeder Seitenladung aktuell halten
QuestEngine::checkAll($uid);

// Daten laden
$activeQuests    = QuestEngine::getActiveQuests($uid);
$availableQuests = QuestEngine::getAvailableQuests($uid, $ich->level);
$doneQuests      = QuestEngine::getDoneQuests($uid);

// Trigger-Typ → lesbarer Name
$triggerLabels = [
    'kill_ships'       => '⚔️ Schiffe vernichten',
    'collect_resource' => '📦 Rohstoff sammeln',
    'build_building'   => '🏗️ Gebäude bauen',
    'scan_systems'     => '🔭 Systeme scannen',
    'fly_to'           => '🚀 Position anfliegen',
    'deliver_item'     => '📬 Item abliefern',
];
?>

<div style="padding: 16px; max-width: 900px; margin: 0 auto; font-family: 'Lucida Console', monospace; color: #c0d8ff;">

  <h2 style="color:#7ab8ff; border-bottom:1px solid #2244aa; padding-bottom:8px;">
    📋 Questlog
  </h2>

  <?php if ($msg): ?>
    <div style="margin:12px 0; padding:10px 14px; background:#111a2a; border:1px solid #2244aa; border-radius:4px;">
      <?= $msg ?>
    </div>
  <?php endif; ?>

  <!-- ===================================================== -->
  <!--  AKTIVE QUESTS                                         -->
  <!-- ===================================================== -->
  <h3 style="color:#ffcc44; margin-top:24px;">Aktive Quests (<?= count($activeQuests) ?>)</h3>

  <?php if (empty($activeQuests)): ?>
    <p style="color:#6688aa; font-style:italic;">Keine aktiven Quests. Nimm unten eine Quest an!</p>
  <?php else: ?>
    <?php foreach ($activeQuests as $aq): ?>
      <div style="background:#0d1a2e; border:1px solid <?= $aq['bereit'] ? '#00cc66' : '#1a3366' ?>; border-radius:6px; padding:14px; margin-bottom:12px;">

        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
          <div>
            <span style="font-size:1.05em; color:#ffcc44; font-weight:bold;">
              <?= htmlspecialchars($aq['titel']) ?>
            </span>
            <span style="color:#446688; font-size:0.85em; margin-left:10px;">
              (angenommen: <?= htmlspecialchars(substr($aq['angenommen_am'], 0, 10)) ?>)
            </span>
          </div>
          <?php if ($aq['bereit']): ?>
            <a href="quest_v2.php?action=complete&pq_id=<?= $aq['pq_id'] ?>"
               style="background:#006633; color:#00ff88; padding:6px 14px; border:1px solid #00aa55; border-radius:4px; text-decoration:none; font-size:0.9em; white-space:nowrap;">
              ✅ Quest abschließen
            </a>
          <?php endif; ?>
        </div>

        <!-- Beschreibung -->
        <div style="color:#8899bb; margin:8px 0 10px 0; font-size:0.9em; line-height:1.5;">
          <?= nl2br(htmlspecialchars($aq['bereit'] ? $aq['abgabetext'] : $aq['beschreibung'])) ?>
        </div>

        <!-- Schritte / Fortschritt -->
        <div>
          <?php foreach ($aq['steps'] as $step): ?>
            <?php
              $fort = (int)$step['fortschritt'];
              $max  = (int)$step['max'];
              $done = (int)$step['erledigt'];
              $pct  = $max > 0 ? round($fort / $max * 100) : 0;
              $barcolor = $done ? '#00aa55' : '#1a5599';
            ?>
            <div style="margin:6px 0;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3px;">
                <span style="font-size:0.85em; color:<?= $done ? '#00cc77' : '#99aacc' ?>;">
                  <?= $done ? '✓' : '○' ?>
                  <?= htmlspecialchars($step['beschreibung']) ?>
                </span>
                <span style="font-size:0.8em; color:#446688; margin-left:8px; white-space:nowrap;">
                  <?= $fort ?>/<?= $max ?>
                </span>
              </div>
              <!-- Fortschrittsbalken -->
              <div style="height:6px; background:#0a1020; border-radius:3px; overflow:hidden;">
                <div style="width:<?= $pct ?>%; height:100%; background:<?= $barcolor ?>; border-radius:3px; transition:width 0.3s;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      </div>
    <?php endforeach; ?>
  <?php endif; ?>


  <!-- ===================================================== -->
  <!--  VERFÜGBARE QUESTS                                     -->
  <!-- ===================================================== -->
  <h3 style="color:#7ab8ff; margin-top:28px;">Verfügbare Quests (<?= count($availableQuests) ?>)</h3>

  <?php if (empty($availableQuests)): ?>
    <p style="color:#6688aa; font-style:italic;">Keine weiteren Quests verfügbar — schau später wieder vorbei.</p>
  <?php else: ?>
    <?php foreach ($availableQuests as $aq): ?>
      <div style="background:#0d1a2e; border:1px solid #1a3366; border-radius:6px; padding:14px; margin-bottom:10px;">

        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
          <span style="color:#7ab8ff; font-size:1.05em; font-weight:bold;">
            <?= htmlspecialchars($aq['titel']) ?>
          </span>
          <a href="quest_v2.php?action=accept&quest_id=<?= (int)$aq['id'] ?>"
             style="background:#001a44; color:#7ab8ff; padding:6px 14px; border:1px solid #2255aa; border-radius:4px; text-decoration:none; font-size:0.9em; white-space:nowrap;">
            + Annehmen
          </a>
        </div>

        <div style="color:#6688aa; margin:8px 0 10px 0; font-size:0.9em; line-height:1.5;">
          <?= nl2br(htmlspecialchars($aq['beschreibung'])) ?>
        </div>

        <!-- Schritte anzeigen (Vorschau) -->
        <?php if (!empty($aq['steps'])): ?>
          <div style="font-size:0.82em; color:#445566; border-top:1px solid #0a1a33; padding-top:8px; margin-top:8px;">
            Aufgaben:
            <?php foreach ($aq['steps'] as $i => $step): ?>
              <span style="color:#335577;">
                <?= $i > 0 ? ' · ' : ' ' ?>
                <?= htmlspecialchars($triggerLabels[$step['trigger_typ']] ?? $step['trigger_typ']) ?>
                — <?= htmlspecialchars($step['beschreibung']) ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ($aq['min_level'] > 0): ?>
          <div style="font-size:0.8em; color:#334455; margin-top:6px;">
            Mindestlevel: <?= (int)$aq['min_level'] ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>


  <!-- ===================================================== -->
  <!--  ABGESCHLOSSENE QUESTS                                 -->
  <!-- ===================================================== -->
  <?php if (!empty($doneQuests)): ?>
    <h3 style="color:#446688; margin-top:28px;">Abgeschlossen (<?= count($doneQuests) ?>)</h3>
    <div style="background:#080f1a; border:1px solid #0f1e33; border-radius:6px; padding:12px;">
      <?php foreach ($doneQuests as $dq): ?>
        <div style="color:#2a4466; font-size:0.88em; padding:3px 0; border-bottom:1px solid #0a1525;">
          <span style="color:#1a6633;">✓</span>
          <?= htmlspecialchars($dq['titel']) ?>
          <span style="color:#1a3344; margin-left:8px;"><?= htmlspecialchars(substr($dq['abgeschlossen_am'], 0, 10)) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php include 'foot.php'; ?>
