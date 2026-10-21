<?php
include 'head.php';
include 'navlogged.php';
include 'klassen.php';

$id  = (int)$_SESSION['Id'];
$verbindung = get_verbindung();

// Konto anlegen falls noch nicht vorhanden
$ii = mysqli_query($verbindung, "SELECT id FROM konto WHERE besitzer='$id' LIMIT 1");
if (!$ii || !mysqli_fetch_array($ii)) {
    mysqli_query($verbindung, "INSERT INTO konto (besitzer) VALUES ('$id')");
}

$account = new Account($id);

// Notifikationen
$checkmail    = (bool)mysqli_num_rows(mysqli_query($verbindung, "SELECT id FROM mail WHERE empfaenger='$id' AND neu=1 AND del=0 LIMIT 1"));
$checkvertrag = (bool)mysqli_num_rows(mysqli_query($verbindung, "SELECT id FROM vertrag WHERE partner='$id' AND valid=0 LIMIT 1"));

// Heimatplanet vorhanden?
$hasHomePlanet = (bool)mysqli_num_rows(mysqli_query($verbindung, "SELECT id FROM planeten WHERE besitzer='$id' AND typ='m' LIMIT 1"));

// Tick-Info + Online-Zähler
$tickRow      = null;
$onlineCount  = 0;
$tm = mysqli_query($verbindung, "SELECT `start`, `end`, (`end`-`start`) AS dauer, status FROM `ticklog` WHERE id=(SELECT max(id) FROM `ticklog`)");
if ($tm && $r = mysqli_fetch_assoc($tm)) {
    $tickRow = $r;
}
$isonlinevar = mysqli_query($verbindung, "SELECT aktion FROM account");
while ($checkonline = mysqli_fetch_array($isonlinevar)) {
    if (isonline($checkonline['aktion'])) $onlineCount++;
}

// Zufallstipp
$tips = [];
$tipvar = mysqli_query($verbindung, "SELECT text FROM tip");
while ($tipp = mysqli_fetch_array($tipvar)) { $tips[] = $tipp['text']; }
$randomTip = count($tips) > 0 ? $tips[array_rand($tips)] : '';
?>

<h1 class="page-title">◈ Übersicht</h1>

<!-- ── Notifikationen ───────────────────────────────────────────── -->
<?php if ($checkmail || $checkvertrag): ?>
<div style="display:flex; flex-wrap:wrap; gap:var(--sp-sm); margin-bottom:var(--sp-lg);">
  <?php if ($checkmail): ?>
  <a href="mail.php" class="btn btn-primary" style="gap:var(--sp-sm);">
    ✉️ Neue Nachrichten
  </a>
  <?php endif; ?>
  <?php if ($checkvertrag): ?>
  <a href="vertrag.php" class="btn btn-gold">
    📜 Neue Vertragsvorschläge
  </a>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- ── Warnungen ────────────────────────────────────────────────── -->
<?php if (strpos($account->nickname, 'Kolonist') !== false): ?>
<div class="alert alert-warning">
  ⚠️ Du heißt noch <strong>Kolonist</strong>. Bitte <a href="option.php">umbenennen</a> — das ist der Standardname.
</div>
<?php endif; ?>

<?php if ($account->level <= 3): ?>
<div class="alert alert-info">
  🛡️ Du bist in den ersten 3 Leveln geschützt — kein Angriff, kein Diebstahl möglich.
  <?php if ($account->level <= 2): ?>&nbsp; Gebäude haben noch <strong>keine Bauzeit</strong>.<?php endif; ?>
  &nbsp;<a href="wiki/index.php/Tutorial">Tutorial</a> · <a href="wiki/">Wiki</a>
</div>
<?php endif; ?>

<!-- ── Chat ─────────────────────────────────────────────────────── -->
<?php if ($account->chat == 1): ?>
<div class="card mb-md">
  <div class="card-header">
    <span class="card-title">💬 Chat</span>
    <a href="option.php" class="btn btn-sm text-muted">ausblenden</a>
  </div>
  <div id="chatid"></div>
  <form name="form1" onsubmit="sendchat(); return false;" style="margin-top:var(--sp-sm);">
    <div class="chat-input-row">
      <input type="text" id="sendchatform" name="sendchatform"
             placeholder="Nachricht eingeben …" onkeydown="kdown(event);">
      <a href="chat_history.php" class="btn btn-sm">Verlauf</a>
    </div>
  </form>
</div>
<?php else: ?>
<p><a href="option.php">Chat einblenden</a></p>
<?php endif; ?>

<!-- ── Zwei Spalten: Status + Links ─────────────────────────────── -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--sp-md); margin-bottom:var(--sp-lg);">

  <!-- Status-Card -->
  <div class="card">
    <div class="card-title" style="margin-bottom:var(--sp-sm);">📡 Spielstatus</div>
    <table style="width:auto; font-size:var(--fs-sm);">
      <tr>
        <td class="text-muted" style="padding-right:var(--sp-md);">Stufe</td>
        <td class="text-primary text-bold"><?= $account->level ?></td>
      </tr>
      <tr>
        <td class="text-muted">Dabei seit</td>
        <td><?= $account->mitglied ?> Ticks</td>
      </tr>
      <tr>
        <td class="text-muted">Online</td>
        <td>
          <a href="player.php" class="text-success"><?= $onlineCount ?> Spieler</a>
        </td>
      </tr>
      <?php if ($tickRow): ?>
      <tr>
        <td class="text-muted">Letzter Tick</td>
        <td class="text-mono" style="font-size:var(--fs-xs);">
          <?php if ($tickRow['status'] == 1): ?>
            <span class="text-warning">⏳ Tick läuft …</span>
          <?php else: ?>
            <?= gerdatum($tickRow['start']) ?>
            <span class="text-muted">(<?= $tickRow['dauer'] ?>s)</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endif; ?>
    </table>
  </div>

  <!-- Links-Card -->
  <div class="card">
    <div class="card-title" style="margin-bottom:var(--sp-sm);">🔗 Wichtige Links</div>
    <div style="display:flex; flex-direction:column; gap:var(--sp-xs);">
      <a href="quest_v2.php">📋 Quests</a>
      <a href="logbuch.php">📒 Logbuch</a>
      <a href="forum/">💬 Forum</a>
      <a href="bugs/">🐛 Bugs melden</a>
      <a href="wiki/">📖 Wiki / Tutorial</a>
      <a href="gacode.htm">✏️ GA-Code (Textformatierung)</a>
    </div>
  </div>

</div>

<!-- ── Kein Heimatplanet → Auswahl ───────────────────────────────── -->
<?php if (!$hasHomePlanet): ?>
<div class="alert alert-danger">
  ⚠️ Du besitzt noch keinen Heimatplaneten! Wähle einen aus der Liste:
</div>
<table class="ga-table" style="max-width:700px;">
  <thead>
    <tr><th>Display</th><th>Name</th><th>System</th><th>Koordinaten</th></tr>
  </thead>
  <tbody>
  <?php
    $abfrage = mysqli_query($verbindung,
        "SELECT planeten.id FROM planeten,systeme
          WHERE planeten.system=systeme.id
            AND systeme.x>=1000 AND systeme.y>=1000
            AND planeten.besitzer='2'"
    );
    while ($planet = mysqli_fetch_array($abfrage)):
        $newpl = new Planeten($planet[0]);
  ?>
    <tr>
      <td><a href="getPlanet.php?pid=<?= $newpl->id ?>"><img src="images/misc/<?= $newpl->bild ?>" style="display:block;"></a></td>
      <td>noname (<?= $newpl->id ?>)</td>
      <td><?= $newpl->position->system->name ?></td>
      <td class="coords"><?= $newpl->position->system->x ?>/<?= $newpl->position->system->y ?></td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
<?php endif; ?>

<!-- ── Zufallstipp ──────────────────────────────────────────────── -->
<?php if ($randomTip): ?>
<div class="alert alert-info" style="margin-top:var(--sp-lg); font-size:var(--fs-sm);">
  💡 <strong>Tipp:</strong> <?= $randomTip ?>
</div>
<?php endif; ?>

<!-- ── Farming-Hinweis ──────────────────────────────────────────── -->
<p class="text-muted text-xs" style="margin-top:var(--sp-lg);">
  Solltest du Opfer von permanenten Diebstählen werden (sogenanntes Farming),
  wende dich an das <a href="forum/">Forum – Gerichtshof</a>.
</p>

<?php include 'foot.php'; ?>
