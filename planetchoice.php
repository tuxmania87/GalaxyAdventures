<?php
include 'head.php';
include 'navlogged.php';
include 'klassen.php';
include_once 'auth.php';
$userId = requireLogin();

// Selbstzerstörung eines Planeten
if (!empty($_POST['do']) && (int)$_POST['do'] === 5 && ctype_digit($_GET['pid'] ?? '')) {
    $pid = (int)$_GET['pid'];
    $pl  = new Planeten($pid);
    if ((int)$pl->besitzer->id !== (int)$_SESSION['Id']) {
        echo '<div class="alert alert-danger">⛔ Du versuchst, einen fremden Planeten zu zerstören!</div>';
    } else {
        mysqli_query($verbindung, "UPDATE planeten SET besitzer=13 WHERE id='$pid'");
        include 'clear_lite.php';
        echo '<div class="alert alert-warning">🗑️ Planet wurde freigegeben.</div>';
    }
}
?>

<h1 class="page-title">🌍 Meine Kolonien</h1>

<?php
$id      = (int)$_SESSION['Id'];
$abfrage = mysqli_query($verbindung, "SELECT id FROM planeten WHERE besitzer='$id'");
$planets = [];
while ($row = mysqli_fetch_assoc($abfrage)) {
    $planets[] = new Planeten($row['id']);
}

if (count($planets) === 0): ?>
  <div class="alert alert-info">Du besitzt noch keine Kolonien.</div>
<?php else: ?>

<table class="ga-table">
  <thead>
    <tr>
      <th></th>
      <th>Name</th>
      <th>Sektor</th>
      <th>System</th>
      <th>Energie</th>
      <th>Schilde</th>
      <th>Lager</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($planets as $p):
    $lager_pct = $p->frachtraum->max > 0
        ? floor(($p->frachtraum->gesamt() / $p->frachtraum->max) * 100)
        : 0;
    $energie_pct = $p->maxenergie > 0
        ? floor(($p->energie / $p->maxenergie) * 100)
        : 0;
    $schilde_pct = $p->maxschilde > 0
        ? floor(($p->schilde / $p->maxschilde) * 100)
        : 0;
  ?>
    <tr>
      <td style="width:48px; text-align:center;">
        <a href="planet.php?pid=<?= $p->id ?>">
          <img src="images/misc/<?= htmlspecialchars($p->bild) ?>" alt="Planet" style="display:block;">
        </a>
      </td>
      <td>
        <a href="planet.php?pid=<?= $p->id ?>" class="text-primary">
          <?= htmlspecialchars($p->name) ?>
        </a>
        <span class="text-muted text-xs">(#<?= $p->id ?>)</span>
      </td>
      <td class="text-mono text-sm"><?= $p->position->x ?>/<?= $p->position->y ?></td>
      <td class="text-sm">
        <?= htmlspecialchars($p->position->system->name) ?>-System
        <span class="text-muted text-xs">(<?= $p->position->system->x ?>/<?= $p->position->system->y ?>)</span>
      </td>
      <td>
        <div class="progress" style="width:80px; margin-bottom:2px;">
          <div class="progress-bar <?= $energie_pct < 30 ? 'progress-bar-danger' : 'progress-bar-success' ?>"
               style="width:<?= $energie_pct ?>%;"></div>
        </div>
        <span class="text-xs text-muted"><?= $p->energie ?>/<?= $p->maxenergie ?></span>
      </td>
      <td>
        <div class="progress" style="width:80px; margin-bottom:2px;">
          <div class="progress-bar <?= $schilde_pct < 30 ? 'progress-bar-danger' : 'progress-bar-primary' ?>"
               style="width:<?= $schilde_pct ?>%;"></div>
        </div>
        <span class="text-xs text-muted"><?= $p->schilde ?>/<?= $p->maxschilde ?></span>
      </td>
      <td>
        <div class="progress" style="width:80px; margin-bottom:2px;">
          <div class="progress-bar <?= $lager_pct > 85 ? 'progress-bar-danger' : 'progress-bar-primary' ?>"
               style="width:<?= $lager_pct ?>%;"></div>
        </div>
        <span class="text-xs text-muted"><?= $lager_pct ?> %</span>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<?php include 'foot.php'; ?>
