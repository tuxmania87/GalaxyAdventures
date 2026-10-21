<?php
include 'head.php';
include 'navlogged.php';
include 'klassen.php';
include_once 'auth.php';
$userId = requireLogin();

$id    = (int)$_SESSION['Id'];
$order = $_GET['order'] ?? '';
$handel = (int)($_GET['handel'] ?? 0);

// Whitelist sortierung
$allowed_orders = ['na', 'nd', 'ka', 'kd', 'ta', 'td'];
$sort = '';
if (in_array($order, $allowed_orders)) {
    $sort_map = [
        'na' => 'ORDER BY name ASC',
        'nd' => 'ORDER BY name DESC',
        'ka' => 'ORDER BY x,y ASC',
        'kd' => 'ORDER BY x,y DESC',
        'ta' => 'ORDER BY klasse ASC',
        'td' => 'ORDER BY klasse DESC',
    ];
    $sort = $sort_map[$order];
}

// Slots berechnen
$slots     = getSlot($id);
$slots_max = 50;
$slots_class = $slots >= $slots_max ? 'text-danger' : 'text-success';

// Schiffe laden
if ($handel === 1) {
    $q = mysqli_query($verbindung, "SELECT id FROM schiffe WHERE besitzer='$id' AND typ='s' AND klasse='Handelsschiff' $sort");
} else {
    $q = mysqli_query($verbindung, "SELECT id FROM schiffe WHERE besitzer='$id' AND typ='s' AND klasse!='Handelsschiff' $sort");
}
$schiffe = [];
while ($r = mysqli_fetch_assoc($q)) { $schiffe[] = new Schiffe($r['id']); }

// Im Bau
$q_bau = mysqli_query($verbindung, "SELECT id FROM schiffe WHERE besitzer='$id' AND typ=''");
$im_bau = [];
while ($r = mysqli_fetch_assoc($q_bau)) { $im_bau[] = new Schiffe($r['id']); }

// Sortierhilfe: Toggle-Link
function sort_link(string $label, string $asc, string $desc, string $current, string $page): string {
    $next = ($current === $asc) ? $desc : $asc;
    $arrow = $current === $asc ? ' ↑' : ($current === $desc ? ' ↓' : '');
    return '<a href="' . $page . '?order=' . $next . '">' . htmlspecialchars($label . $arrow) . '</a>';
}
$base = 'schiffchoice.php';
?>

<h1 class="page-title">🚀 Meine Schiffe</h1>

<!-- Slots-Badge -->
<div style="margin-bottom:var(--sp-md);">
  <span class="stat-chip">
    Slots: <strong class="<?= $slots_class ?>"><?= $slots ?></strong>/<?= $slots_max ?>
  </span>
  <?php if ($handel === 0): ?>
    <a href="schiffchoice.php?handel=1" class="btn btn-sm" style="margin-left:var(--sp-sm);">🛒 Handelsschiffe</a>
  <?php else: ?>
    <a href="schiffchoice.php" class="btn btn-sm" style="margin-left:var(--sp-sm);">⚔️ Kampfschiffe</a>
  <?php endif; ?>
</div>

<!-- Flotte erstellen -->
<form action="createflotte.php" method="post">
<input type="hidden" name="anzahl" value="<?= count($schiffe) ?>">

<?php if (count($schiffe) === 0): ?>
  <div class="alert alert-info">Keine Schiffe vorhanden.</div>
<?php else: ?>
<div style="overflow-x:auto;">
<table class="ga-table">
  <thead>
    <tr>
      <th></th>
      <th><?= sort_link('Typ', 'ta', 'td', $order, $base) ?></th>
      <th><?= sort_link('Name', 'na', 'nd', $order, $base) ?></th>
      <th><?= sort_link('Position', 'ka', 'kd', $order, $base) ?></th>
      <th>System</th>
      <th>Energie</th>
      <th>Warpkern</th>
      <th>Hülle</th>
      <th>Schilde</th>
      <th>Orbit</th>
      <th>Flotte</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($schiffe as $i => $s):
    $system = $s->position->system;
    $hull_pct  = $s->maxhull > 0 ? ($s->hull / $s->maxhull) : 1;
    $hull_class = $hull_pct <= 0.2 ? 'progress-bar-danger'
                : ($hull_pct <= 0.5 ? 'progress-bar-warning' : 'progress-bar-success');
    $shield_pct = $s->maxschilde > 0 ? ($s->schilde / $s->maxschilde) : 0;
    $energy_pct = $s->maxenergie > 0 ? ($s->energie / $s->maxenergie) : 0;
    $warp_rounds = $s->energieoutput > 0 ? floor($s->warpkern / $s->energieoutput) : 0;
  ?>
    <tr>
      <!-- Bild -->
      <td style="width:44px; text-align:center;">
        <a href="schiffe.php?sid=<?= $s->id ?>">
          <img src="<?= htmlspecialchars($s->bild) ?>" alt="<?= htmlspecialchars($s->klasse) ?>" style="display:block;">
        </a>
      </td>

      <!-- Typ -->
      <td class="text-sm"><?= htmlspecialchars($s->klasse) ?></td>

      <!-- Name -->
      <td>
        <a href="schiffe.php?sid=<?= $s->id ?>" class="text-primary"><?= htmlspecialchars($s->name) ?></a>
        <span class="text-muted text-xs">(<?= $s->id ?>)</span>
      </td>

      <!-- Position -->
      <td class="text-mono text-sm"><?= $s->position->x ?>|<?= $s->position->y ?></td>

      <!-- System -->
      <td class="text-sm">
        <?= $system->id == 0 ? 'Weltraum' : htmlspecialchars($system->name) . '-System' ?>
        <?php if ($system->id > 0): ?>
          <span class="text-muted text-xs">(<?= $system->x ?>/<?= $system->y ?>)</span>
        <?php endif; ?>
      </td>

      <!-- Energie -->
      <td style="min-width:90px;">
        <div class="progress" style="width:80px; margin-bottom:2px;">
          <div class="progress-bar <?= $energy_pct < 0.3 ? 'progress-bar-danger' : 'progress-bar-primary' ?>"
               style="width:<?= floor($energy_pct * 100) ?>%;"></div>
        </div>
        <span class="text-xs text-muted"><?= $s->energie ?>/<?= $s->maxenergie ?>+<?= $s->energieoutput ?></span>
      </td>

      <!-- Warpkern -->
      <td class="text-sm text-center"><?= $s->warpkern ?><br><span class="text-muted text-xs">(<?= $warp_rounds ?>R)</span></td>

      <!-- Hülle -->
      <td style="min-width:90px;">
        <div class="progress" style="width:80px; margin-bottom:2px;">
          <div class="progress-bar <?= $hull_class ?>" style="width:<?= floor($hull_pct * 100) ?>%;"></div>
        </div>
        <span class="text-xs <?= $hull_pct <= 0.2 ? 'text-danger' : ($hull_pct <= 0.5 ? 'text-warning' : 'text-muted') ?>">
          <?= $s->hull ?>/<?= $s->maxhull ?>
        </span>
      </td>

      <!-- Schilde -->
      <td style="min-width:90px;">
        <div class="progress" style="width:80px; margin-bottom:2px;">
          <div class="progress-bar progress-bar-primary" style="width:<?= floor($shield_pct * 100) ?>%;"></div>
        </div>
        <span class="text-xs <?= $s->schildstatus == 1 ? 'text-warning' : 'text-muted' ?>">
          <?= $s->schilde ?>/<?= $s->maxschilde ?>
          <?= $s->schildstatus == 1 ? '⚡' : '' ?>
        </span>
      </td>

      <!-- Orbit -->
      <td class="text-xs text-center <?= $s->position->orbit == 1 ? 'text-success' : 'text-muted' ?>">
        <?= $s->position->orbit == 1 ? '✔ Orbit' : '— kein Orbit' ?>
      </td>

      <!-- Flotte-Checkbox -->
      <td class="text-center">
        <?php if ($s->werft == 0): ?>
          <input type="checkbox" name="schiff<?= $i + 1 ?>" value="<?= $s->id ?>"
                 style="width:16px; height:16px; cursor:pointer;">
        <?php else: ?>
          <span class="text-muted text-xs">🔧 Werft</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>

<!-- Flotte erstellen -->
<div style="display:flex; align-items:center; gap:var(--sp-md); margin-top:var(--sp-md);">
  <label class="text-sm">Flottenname:</label>
  <input type="text" name="flottenname" placeholder="Name der neuen Flotte …" style="width:220px;">
  <button type="submit" class="btn btn-primary">⚓ Flotte erstellen</button>
</div>
<?php endif; ?>
</form>

<!-- Im Bau -->
<?php if (count($im_bau) > 0): ?>
<div class="card" style="margin-top:var(--sp-xl);">
  <div class="card-title" style="margin-bottom:var(--sp-sm);">🔧 Im Bau</div>
  <table class="ga-table">
    <thead>
      <tr><th></th><th>Klasse</th><th>Position</th><th>Energie</th><th>Noch</th></tr>
    </thead>
    <tbody>
    <?php foreach ($im_bau as $s): ?>
      <tr>
        <td><img src="<?= htmlspecialchars($s->bild) ?>" alt="" style="display:block;"></td>
        <td><?= htmlspecialchars($s->klasse) ?></td>
        <td class="text-mono text-sm">
          <?= $s->position->x ?>|<?= $s->position->y ?>
          <?= $s->position->system->id > 0 ? ' · ' . htmlspecialchars($s->position->system->name) . '-System' : '' ?>
        </td>
        <td class="text-sm"><?= $s->energie ?>/<?= $s->maxenergie ?></td>
        <td><span class="text-warning">⏳ <?= $s->frachtraum->baustoff ?> Runden</span></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php include 'foot.php'; ?>
