<?php
/**
 * quest_admin.php — Admin-Quest-Editor (Quest Engine v2)
 *
 * Nur für Admins/Mods (moderator > 0)
 *
 * Funktionen:
 *  - Quest-Templates anlegen / bearbeiten / löschen / aktivieren
 *  - Schritte (Trigger) hinzufügen / entfernen / sortieren
 *  - Übersicht aller Quests und Spielerfortschritt
 */

include 'head.php';
include 'navlogged.php';
include 'klassen.php';

$verbindung = get_verbindung();
$ich        = new Account($_SESSION['Id']);

// Nur Moderatoren/Admins
if (!$ich->moderator) {
    exit('<div style="color:red;padding:20px;">Zugriff verweigert.</div>');
}

$uid = (int)$_SESSION['Id'];

// ------------------------------------------------------------
//  Trigger-Definitionen: welche Parameter gibt es je Typ?
// ------------------------------------------------------------
$triggerDefs = [
    'kill_ships' => [
        'label'  => '⚔️ Schiffe vernichten',
        'params' => [
            'anzahl' => ['type' => 'number', 'label' => 'Anzahl', 'default' => 5],
            'rasse'  => ['type' => 'text',   'label' => 'Rasse (leer = alle)', 'default' => ''],
        ],
    ],
    'collect_resource' => [
        'label'  => '📦 Rohstoff sammeln',
        'params' => [
            'rohstoff' => ['type' => 'select', 'label' => 'Rohstoff',
                           'options' => ['erz','deuterium','titan','silizium','antimaterie','nahrung','wasser','stahl','kristall'],
                           'default' => 'erz'],
            'menge'    => ['type' => 'number', 'label' => 'Menge', 'default' => 500],
        ],
    ],
    'build_building' => [
        'label'  => '🏗️ Gebäude bauen',
        'params' => [
            'gebaeude_id' => ['type' => 'number', 'label' => 'Gebäude-ID', 'default' => 1],
            'anzahl'      => ['type' => 'number', 'label' => 'Anzahl Planeten', 'default' => 1],
        ],
    ],
    'scan_systems' => [
        'label'  => '🔭 Systeme scannen',
        'params' => [
            'anzahl' => ['type' => 'number', 'label' => 'Anzahl Systeme', 'default' => 3],
        ],
    ],
    'fly_to' => [
        'label'  => '🚀 Position anfliegen',
        'params' => [
            'x'         => ['type' => 'number', 'label' => 'X', 'default' => 0],
            'y'         => ['type' => 'number', 'label' => 'Y', 'default' => 0],
            'system_id' => ['type' => 'number', 'label' => 'System-ID (0=Weltraum)', 'default' => 0],
        ],
    ],
    'deliver_item' => [
        'label'  => '📬 Item abliefern',
        'params' => [
            'item_id' => ['type' => 'number', 'label' => 'Item-ID', 'default' => 1],
            'menge'   => ['type' => 'number', 'label' => 'Menge', 'default' => 1],
        ],
    ],
];

// ------------------------------------------------------------
//  POST-Handler
// ------------------------------------------------------------
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ---- Quest anlegen / bearbeiten ----
    if ($action === 'save_quest') {
        $id          = (int)($_POST['id'] ?? 0);
        $titel       = mysqli_real_escape_string($verbindung, trim($_POST['titel'] ?? ''));
        $beschr      = mysqli_real_escape_string($verbindung, trim($_POST['beschreibung'] ?? ''));
        $abgabetext  = mysqli_real_escape_string($verbindung, trim($_POST['abgabetext'] ?? ''));
        $vorquest_id = (int)($_POST['vorquest_id'] ?? 0);
        $min_level   = (int)($_POST['min_level'] ?? 0);
        $repeatable  = isset($_POST['repeatable']) ? 1 : 0;
        $aktiv       = isset($_POST['aktiv']) ? 1 : 0;

        // Belohnung JSON zusammenbauen
        $b_rohstoffe = [];
        foreach (['erz','deuterium','titan','silizium','antimaterie','nahrung','wasser','stahl','kristall'] as $roh) {
            $val = (int)($_POST['belohnung_roh_' . $roh] ?? 0);
            if ($val > 0) {
                $b_rohstoffe[$roh] = $val;
            }
        }
        $b_schiffe = array_filter(array_map('trim', explode(',', $_POST['belohnung_schiffe'] ?? '')));
        $belohnung = json_encode(['rohstoffe' => $b_rohstoffe, 'schiffe' => array_values($b_schiffe)]);

        if ($id > 0) {
            mysqli_query($verbindung,
                "UPDATE quest_templates SET
                    titel='$titel', beschreibung='$beschr', abgabetext='$abgabetext',
                    vorquest_id=$vorquest_id, min_level=$min_level,
                    belohnung='$belohnung', repeatable=$repeatable, aktiv=$aktiv
                  WHERE id=$id"
            ) or ($msg = '<span style="color:red;">Fehler: ' . mysqli_error($verbindung) . '</span>');
            if (!$msg) {
                $msg = '<span style="color:#00cc77;">Quest aktualisiert (ID: ' . $id . ')</span>';
            }
        } else {
            mysqli_query($verbindung,
                "INSERT INTO quest_templates
                    (titel, beschreibung, abgabetext, vorquest_id, min_level, belohnung, repeatable, aktiv, erstellt_von)
                 VALUES
                    ('$titel','$beschr','$abgabetext',$vorquest_id,$min_level,'$belohnung',$repeatable,$aktiv,$uid)"
            ) or ($msg = '<span style="color:red;">Fehler: ' . mysqli_error($verbindung) . '</span>');
            if (!$msg) {
                $id  = (int)mysqli_insert_id($verbindung);
                $msg = '<span style="color:#00cc77;">Quest angelegt (ID: ' . $id . ')</span>';
            }
        }
        if (!$msg) {
            header('Location: quest_admin.php?view=edit&id=' . $id . '&saved=1');
            exit;
        }
    }

    // ---- Quest löschen ----
    if ($action === 'delete_quest') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            mysqli_query($verbindung, "DELETE FROM quest_templates WHERE id=$id");
            $msg = '<span style="color:#ffaa44;">Quest #' . $id . ' gelöscht.</span>';
        }
    }

    // ---- Schritt hinzufügen ----
    if ($action === 'add_step') {
        $quest_id    = (int)($_POST['quest_id'] ?? 0);
        $trigger_typ = $_POST['trigger_typ'] ?? '';
        $beschr      = mysqli_real_escape_string($verbindung, trim($_POST['step_beschreibung'] ?? ''));

        if (!isset($triggerDefs[$trigger_typ])) {
            $msg = '<span style="color:red;">Unbekannter Trigger-Typ.</span>';
        } else {
            // Parameter aus POST lesen
            $params = [];
            foreach ($triggerDefs[$trigger_typ]['params'] as $key => $def) {
                $raw = $_POST['param_' . $key] ?? $def['default'];
                $params[$key] = $def['type'] === 'number' ? (int)$raw : $raw;
            }
            $paramJson = mysqli_real_escape_string($verbindung, json_encode($params));

            // Reihenfolge: nächste freie
            $maxQ = mysqli_query($verbindung,
                "SELECT COALESCE(MAX(reihenfolge), -1) + 1 AS next FROM quest_steps WHERE quest_id=$quest_id"
            );
            $next = (int)mysqli_fetch_assoc($maxQ)['next'];

            mysqli_query($verbindung,
                "INSERT INTO quest_steps (quest_id, reihenfolge, trigger_typ, beschreibung, parameter)
                 VALUES ($quest_id, $next, '$trigger_typ', '$beschr', '$paramJson')"
            ) or ($msg = '<span style="color:red;">Fehler: ' . mysqli_error($verbindung) . '</span>');
            if (!$msg) {
                $msg = '<span style="color:#00cc77;">Schritt hinzugefügt.</span>';
            }
        }
    }

    // ---- Schritt löschen ----
    if ($action === 'delete_step') {
        $step_id = (int)($_POST['step_id'] ?? 0);
        if ($step_id > 0) {
            mysqli_query($verbindung, "DELETE FROM quest_steps WHERE id=$step_id");
            $msg = '<span style="color:#ffaa44;">Schritt gelöscht.</span>';
        }
    }

    // ---- Quest aktivieren/deaktivieren ----
    if ($action === 'toggle_aktiv') {
        $id  = (int)($_POST['id'] ?? 0);
        $val = (int)($_POST['val'] ?? 0);
        if ($id > 0) {
            mysqli_query($verbindung, "UPDATE quest_templates SET aktiv=$val WHERE id=$id");
            $msg = '<span style="color:#00cc77;">Status geändert.</span>';
        }
    }
}

// ------------------------------------------------------------
//  View-Routing
// ------------------------------------------------------------
$view     = $_GET['view'] ?? 'list';
$edit_id  = (int)($_GET['id'] ?? 0);

// Alle Quest-Templates laden (für Auswahllisten)
$allQuests = [];
$q = mysqli_query($verbindung, "SELECT id, titel, aktiv, min_level, vorquest_id FROM quest_templates ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($q)) {
    $allQuests[] = $row;
}

// CSS inline (LCARS-Stil passend zum Rest des Spiels)
$css = '
<style>
.qa-wrap { padding:16px; max-width:960px; margin:0 auto; font-family:"Lucida Console",monospace; color:#c0d8ff; }
.qa-table { width:100%; border-collapse:collapse; font-size:0.88em; }
.qa-table th { background:#0a1a33; color:#7ab8ff; padding:8px 10px; text-align:left; border-bottom:1px solid #2244aa; }
.qa-table td { padding:7px 10px; border-bottom:1px solid #0d1e36; vertical-align:top; }
.qa-table tr:hover td { background:#081525; }
.qa-btn { padding:5px 12px; border:1px solid #2244aa; background:#0a1730; color:#7ab8ff;
          border-radius:3px; cursor:pointer; font-size:0.82em; text-decoration:none; display:inline-block; }
.qa-btn:hover { background:#112244; }
.qa-btn-green { border-color:#006633; background:#002211; color:#00cc77; }
.qa-btn-red   { border-color:#661100; background:#220800; color:#ff6644; }
.qa-btn-orange{ border-color:#664400; background:#221100; color:#ffaa44; }
.qa-form-row  { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:10px; align-items:flex-start; }
.qa-form-col  { display:flex; flex-direction:column; gap:4px; min-width:180px; flex:1; }
.qa-label     { font-size:0.8em; color:#7ab8ff; margin-bottom:2px; }
.qa-input     { background:#0a1525; border:1px solid #1a3366; color:#c0d8ff; padding:6px 8px;
                border-radius:3px; font-family:inherit; font-size:0.9em; width:100%; box-sizing:border-box; }
.qa-input:focus { outline:none; border-color:#4477cc; }
.qa-card      { background:#0a1525; border:1px solid #1a3366; border-radius:5px; padding:14px; margin-bottom:12px; }
.qa-pill      { display:inline-block; padding:2px 8px; border-radius:10px; font-size:0.78em; }
.qa-pill-green{ background:#003322; color:#00aa55; border:1px solid #005533; }
.qa-pill-grey { background:#111a22; color:#445566; border:1px solid #223344; }
.qa-section   { color:#7ab8ff; font-size:1.05em; font-weight:bold; border-bottom:1px solid #1a3366;
                padding-bottom:6px; margin:20px 0 12px 0; }
.qa-step      { background:#080f1a; border:1px solid #0f1e33; border-radius:4px; padding:10px 12px; margin-bottom:8px; }
</style>
';

echo $css;
?>

<div class="qa-wrap">
  <h2 style="color:#7ab8ff; border-bottom:1px solid #2244aa; padding-bottom:8px;">
    🛠️ Quest-Editor <span style="color:#445566; font-size:0.7em;">Admin-Bereich</span>
  </h2>

  <!-- Navigation -->
  <div style="margin-bottom:16px;">
    <a href="quest_admin.php?view=list"  class="qa-btn <?= $view==='list'  ? 'qa-btn-green' : '' ?>">📋 Alle Quests</a>
    <a href="quest_admin.php?view=new"   class="qa-btn <?= $view==='new'   ? 'qa-btn-green' : '' ?>">➕ Neue Quest</a>
    <a href="quest_admin.php?view=stats" class="qa-btn <?= $view==='stats' ? 'qa-btn-green' : '' ?>">📊 Spielerfortschritt</a>
    <a href="quest_v2.php" class="qa-btn" style="margin-left:16px;">👤 Spieler-Ansicht</a>
  </div>

  <?php if ($msg): ?>
    <div style="margin:12px 0; padding:10px 14px; background:#0d1a2e; border:1px solid #2244aa; border-radius:4px;">
      <?= $msg ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['saved'])): ?>
    <div style="margin:12px 0; padding:10px 14px; background:#002211; border:1px solid #005533; border-radius:4px; color:#00cc77;">
      ✅ Gespeichert.
    </div>
  <?php endif; ?>


  <!-- =====================================================
       VIEW: LISTE aller Quests
       ===================================================== -->
  <?php if ($view === 'list'): ?>

    <table class="qa-table">
      <thead>
        <tr>
          <th style="width:40px;">ID</th>
          <th>Titel</th>
          <th style="width:80px;">Level</th>
          <th style="width:100px;">Vorquest</th>
          <th style="width:70px;">Status</th>
          <th style="width:130px;">Aktionen</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($allQuests as $qt): ?>
          <?php
            $vorq = '';
            foreach ($allQuests as $q2) {
                if ((int)$q2['id'] === (int)$qt['vorquest_id']) {
                    $vorq = '#' . $q2['id'] . ' ' . mb_substr($q2['titel'], 0, 20);
                }
            }
          ?>
          <tr>
            <td style="color:#446688;"><?= (int)$qt['id'] ?></td>
            <td>
              <a href="quest_admin.php?view=edit&id=<?= (int)$qt['id'] ?>" style="color:#7ab8ff; text-decoration:none;">
                <?= htmlspecialchars($qt['titel']) ?>
              </a>
            </td>
            <td><?= (int)$qt['min_level'] > 0 ? '≥ ' . (int)$qt['min_level'] : '–' ?></td>
            <td style="font-size:0.85em; color:#446688;"><?= $vorq ?: '–' ?></td>
            <td>
              <?php if ($qt['aktiv']): ?>
                <span class="qa-pill qa-pill-green">aktiv</span>
              <?php else: ?>
                <span class="qa-pill qa-pill-grey">inaktiv</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="quest_admin.php?view=edit&id=<?= (int)$qt['id'] ?>" class="qa-btn">✏️</a>
              <form method="post" style="display:inline;" onsubmit="return confirm('Quest #<?= (int)$qt['id'] ?> wirklich löschen?')">
                <input type="hidden" name="action" value="delete_quest">
                <input type="hidden" name="id" value="<?= (int)$qt['id'] ?>">
                <button type="submit" class="qa-btn qa-btn-red">🗑</button>
              </form>
              <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="toggle_aktiv">
                <input type="hidden" name="id" value="<?= (int)$qt['id'] ?>">
                <input type="hidden" name="val" value="<?= $qt['aktiv'] ? 0 : 1 ?>">
                <button type="submit" class="qa-btn qa-btn-orange">
                  <?= $qt['aktiv'] ? '⏸' : '▶️' ?>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($allQuests)): ?>
          <tr><td colspan="6" style="color:#446688; text-align:center; padding:20px;">Noch keine Quests vorhanden.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div style="margin-top:12px;">
      <a href="quest_admin.php?view=new" class="qa-btn qa-btn-green">➕ Neue Quest anlegen</a>
    </div>


  <!-- =====================================================
       VIEW: Neue Quest / Bearbeiten
       ===================================================== -->
  <?php elseif ($view === 'new' || $view === 'edit'): ?>

    <?php
      // Bestehende Daten laden wenn Edit
      $qt = ['id'=>0,'titel'=>'','beschreibung'=>'','abgabetext'=>'','vorquest_id'=>0,
             'min_level'=>0,'belohnung'=>'{}','repeatable'=>0,'aktiv'=>1];
      $steps = [];
      if ($edit_id > 0) {
          $q = mysqli_query($verbindung, "SELECT * FROM quest_templates WHERE id=$edit_id");
          $qt = mysqli_fetch_assoc($q) ?: $qt;
          $sq = mysqli_query($verbindung,
              "SELECT * FROM quest_steps WHERE quest_id=$edit_id ORDER BY reihenfolge ASC");
          while ($s = mysqli_fetch_assoc($sq)) {
              $steps[] = $s;
          }
      }
      $belohnung = json_decode($qt['belohnung'], true) ?? ['rohstoffe'=>[],'schiffe'=>[]];
      $rohstoffe_rewards = $belohnung['rohstoffe'] ?? [];
      $schiffe_rewards   = implode(', ', $belohnung['schiffe'] ?? []);
      $rohstoff_list = ['erz','deuterium','titan','silizium','antimaterie','nahrung','wasser','stahl','kristall'];
    ?>

    <h3 style="color:#7ab8ff;"><?= $edit_id > 0 ? 'Quest bearbeiten (ID: ' . $edit_id . ')' : 'Neue Quest anlegen' ?></h3>

    <form method="post">
      <input type="hidden" name="action" value="save_quest">
      <input type="hidden" name="id" value="<?= (int)$qt['id'] ?>">

      <div class="qa-card">
        <div class="qa-section">Grunddaten</div>

        <div class="qa-form-row">
          <div class="qa-form-col" style="flex:2;">
            <label class="qa-label">Titel *</label>
            <input class="qa-input" type="text" name="titel" value="<?= htmlspecialchars($qt['titel']) ?>" required placeholder="Quest-Titel...">
          </div>
          <div class="qa-form-col" style="flex:0 0 100px;">
            <label class="qa-label">Mindestlevel</label>
            <input class="qa-input" type="number" name="min_level" value="<?= (int)$qt['min_level'] ?>" min="0">
          </div>
          <div class="qa-form-col" style="flex:0 0 150px;">
            <label class="qa-label">Vorquest (Quest-ID)</label>
            <select class="qa-input" name="vorquest_id">
              <option value="0">– keine –</option>
              <?php foreach ($allQuests as $q2): ?>
                <?php if ((int)$q2['id'] !== $edit_id): ?>
                  <option value="<?= (int)$q2['id'] ?>"
                    <?= (int)$qt['vorquest_id'] === (int)$q2['id'] ? 'selected' : '' ?>>
                    #<?= (int)$q2['id'] ?> <?= htmlspecialchars(mb_substr($q2['titel'], 0, 30)) ?>
                  </option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="qa-form-row">
          <div class="qa-form-col">
            <label class="qa-label">Quest-Text (wird beim Annehmen angezeigt)</label>
            <textarea class="qa-input" name="beschreibung" rows="4" placeholder="Beschreibungstext der Quest..."><?= htmlspecialchars($qt['beschreibung']) ?></textarea>
          </div>
          <div class="qa-form-col">
            <label class="qa-label">Abgabe-Text (wird beim Abschließen angezeigt)</label>
            <textarea class="qa-input" name="abgabetext" rows="4" placeholder="Text wenn die Quest erledigt ist..."><?= htmlspecialchars($qt['abgabetext']) ?></textarea>
          </div>
        </div>

        <div class="qa-form-row">
          <div class="qa-form-col" style="flex:0 0 auto; flex-direction:row; gap:16px; align-items:center;">
            <label style="color:#c0d8ff; font-size:0.9em; cursor:pointer;">
              <input type="checkbox" name="aktiv" <?= $qt['aktiv'] ? 'checked' : '' ?>>
              Aktiv (für Spieler sichtbar)
            </label>
            <label style="color:#c0d8ff; font-size:0.9em; cursor:pointer;">
              <input type="checkbox" name="repeatable" <?= $qt['repeatable'] ? 'checked' : '' ?>>
              Wiederholbar
            </label>
          </div>
        </div>
      </div>

      <!-- Belohnungen -->
      <div class="qa-card">
        <div class="qa-section">Belohnungen</div>

        <div class="qa-form-row" style="flex-wrap:wrap;">
          <?php foreach ($rohstoff_list as $roh): ?>
            <div class="qa-form-col" style="flex:0 0 120px;">
              <label class="qa-label"><?= ucfirst($roh) ?></label>
              <input class="qa-input" type="number" name="belohnung_roh_<?= $roh ?>"
                     value="<?= (int)($rohstoffe_rewards[$roh] ?? 0) ?>" min="0">
            </div>
          <?php endforeach; ?>
        </div>

        <div class="qa-form-row" style="margin-top:8px;">
          <div class="qa-form-col">
            <label class="qa-label">Schiffe (Bauplan-Klassen, kommagetrennt — z.B. <code>miranda, hauler</code>)</label>
            <input class="qa-input" type="text" name="belohnung_schiffe"
                   value="<?= htmlspecialchars($schiffe_rewards) ?>"
                   placeholder="miranda, hauler, ...">
          </div>
        </div>
      </div>

      <div style="margin-bottom:20px;">
        <button type="submit" class="qa-btn qa-btn-green" style="font-size:1em; padding:8px 20px;">
          💾 Quest speichern
        </button>
        <a href="quest_admin.php?view=list" class="qa-btn" style="margin-left:8px;">Abbrechen</a>
      </div>
    </form>


    <!-- ---- Schritte (nur bei bestehender Quest) ---- -->
    <?php if ($edit_id > 0): ?>
      <div class="qa-card">
        <div class="qa-section">Quest-Schritte / Trigger</div>

        <?php if (empty($steps)): ?>
          <p style="color:#446688; font-style:italic;">Noch keine Schritte — füge unten welche hinzu.</p>
        <?php else: ?>
          <?php foreach ($steps as $i => $step): ?>
            <?php $params = json_decode($step['parameter'], true) ?? []; ?>
            <div class="qa-step">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                <div>
                  <span style="color:#446688; font-size:0.82em; margin-right:8px;">Schritt <?= $i + 1 ?></span>
                  <span style="color:#7ab8ff;">
                    <?= $triggerDefs[$step['trigger_typ']]['label'] ?? $step['trigger_typ'] ?>
                  </span>
                </div>
                <form method="post" style="display:inline;" onsubmit="return confirm('Schritt löschen?')">
                  <input type="hidden" name="action" value="delete_step">
                  <input type="hidden" name="step_id" value="<?= (int)$step['id'] ?>">
                  <button type="submit" class="qa-btn qa-btn-red">🗑 löschen</button>
                </form>
              </div>

              <div style="color:#8899bb; font-size:0.88em; margin-bottom:4px;">
                <?= htmlspecialchars($step['beschreibung']) ?>
              </div>

              <div style="font-size:0.8em; color:#334455;">
                Parameter:
                <?php foreach ($params as $k => $v): ?>
                  <span style="background:#080f1a; border:1px solid #0f1e33; border-radius:3px; padding:1px 6px; margin:0 3px; color:#446688;">
                    <?= htmlspecialchars($k) ?>: <span style="color:#7ab8ff;"><?= htmlspecialchars((string)$v) ?></span>
                  </span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <!-- Neuen Schritt hinzufügen -->
        <div style="margin-top:16px; background:#060d18; border:1px dashed #1a3366; border-radius:5px; padding:14px;">
          <div style="color:#7ab8ff; font-size:0.95em; margin-bottom:10px;">➕ Neuen Schritt hinzufügen</div>

          <form method="post" id="add-step-form">
            <input type="hidden" name="action" value="add_step">
            <input type="hidden" name="quest_id" value="<?= $edit_id ?>">

            <div class="qa-form-row">
              <div class="qa-form-col" style="flex:0 0 220px;">
                <label class="qa-label">Trigger-Typ</label>
                <select class="qa-input" name="trigger_typ" id="trigger-typ-select" onchange="updateParamFields()">
                  <?php foreach ($triggerDefs as $typ => $def): ?>
                    <option value="<?= $typ ?>"><?= htmlspecialchars($def['label']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="qa-form-col" style="flex:2;">
                <label class="qa-label">Beschreibung (für den Spieler)</label>
                <input class="qa-input" type="text" name="step_beschreibung" placeholder="z.B. 5 klingonische Schiffe vernichten">
              </div>
            </div>

            <!-- Dynamische Parameter-Felder -->
            <div id="param-fields" class="qa-form-row" style="margin-top:8px;">
              <!-- wird per JS befüllt -->
            </div>

            <button type="submit" class="qa-btn qa-btn-green" style="margin-top:8px;">Schritt hinzufügen</button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <div style="color:#446688; font-style:italic; font-size:0.9em;">
        Speichere die Quest zuerst, um Schritte hinzuzufügen.
      </div>
    <?php endif; ?>


  <!-- =====================================================
       VIEW: Spielerfortschritt / Statistik
       ===================================================== -->
  <?php elseif ($view === 'stats'): ?>

    <h3 style="color:#7ab8ff;">Spieler-Fortschritt</h3>

    <table class="qa-table">
      <thead>
        <tr>
          <th>Spieler</th>
          <th>Quest</th>
          <th>Status</th>
          <th>Fortschritt (Schritte)</th>
          <th>Angenommen</th>
          <th>Abgeschlossen</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $q = mysqli_query($verbindung,
            "SELECT pq.id, pq.uid, pq.status, pq.angenommen_am, pq.abgeschlossen_am,
                    qt.titel,
                    a.nickname,
                    (SELECT COUNT(*) FROM player_quest_steps WHERE player_quest_id=pq.id) AS total_steps,
                    (SELECT COUNT(*) FROM player_quest_steps WHERE player_quest_id=pq.id AND erledigt=1) AS done_steps
               FROM player_quests pq
               JOIN quest_templates qt ON qt.id = pq.quest_id
               JOIN account a ON a.id = pq.uid
              ORDER BY pq.id DESC
              LIMIT 200"
        );
        $found = false;
        while ($row = mysqli_fetch_assoc($q)):
            $found = true;
            $statusColor = match($row['status']) {
                'done'   => '#00aa55',
                'failed' => '#cc3300',
                default  => '#ccaa00',
            };
        ?>
          <tr>
            <td style="color:#7ab8ff;"><?= htmlspecialchars(strip_tags($row['nickname'])) ?> <span style="color:#334455;">(#<?= (int)$row['uid'] ?>)</span></td>
            <td><?= htmlspecialchars($row['titel']) ?></td>
            <td><span style="color:<?= $statusColor ?>;"><?= $row['status'] ?></span></td>
            <td>
              <span style="color:#7ab8ff;"><?= (int)$row['done_steps'] ?></span>
              <span style="color:#334455;">/<?= (int)$row['total_steps'] ?></span>
            </td>
            <td style="color:#446688; font-size:0.85em;"><?= htmlspecialchars(substr($row['angenommen_am'], 0, 10)) ?></td>
            <td style="color:#446688; font-size:0.85em;"><?= $row['abgeschlossen_am'] ? htmlspecialchars(substr($row['abgeschlossen_am'], 0, 10)) : '–' ?></td>
          </tr>
        <?php endwhile; ?>
        <?php if (!$found): ?>
          <tr><td colspan="6" style="color:#446688; text-align:center; padding:20px;">Noch keine Spieler-Quest-Daten.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

  <?php endif; ?>

</div>

<!-- =====================================================
     JavaScript: Dynamische Parameter-Felder
     ===================================================== -->
<script>
// Trigger-Definitionen aus PHP
const triggerDefs = <?= json_encode(array_map(function($def) {
    return ['label' => $def['label'], 'params' => $def['params']];
}, $triggerDefs)) ?>;

function updateParamFields() {
    const typ    = document.getElementById('trigger-typ-select').value;
    const def    = triggerDefs[typ];
    const wrap   = document.getElementById('param-fields');
    wrap.innerHTML = '';

    if (!def || !def.params) return;

    for (const [key, p] of Object.entries(def.params)) {
        const col = document.createElement('div');
        col.className = 'qa-form-col';
        col.style.flex = '0 0 160px';

        const label = document.createElement('label');
        label.className = 'qa-label';
        label.textContent = p.label;
        col.appendChild(label);

        let input;
        if (p.type === 'select') {
            input = document.createElement('select');
            for (const opt of p.options) {
                const o = document.createElement('option');
                o.value = opt;
                o.textContent = opt;
                if (opt === p.default) o.selected = true;
                input.appendChild(o);
            }
        } else {
            input = document.createElement('input');
            input.type  = p.type === 'number' ? 'number' : 'text';
            input.value = p.default ?? '';
            if (p.type === 'number') input.min = '0';
        }
        input.name      = 'param_' + key;
        input.className = 'qa-input';
        col.appendChild(input);
        wrap.appendChild(col);
    }
}

// Initial befüllen
document.addEventListener('DOMContentLoaded', updateParamFields);
</script>

<?php include 'foot.php'; ?>
