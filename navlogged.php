<?php
/**
 * navlogged.php — Sidebar-Navigation (Design v2)
 *
 * Bindet #ga-nav ein. Wird in head.php durch <body id="ga-wrap"> ergänzt.
 * Enthält: Logo, Spieler-Info, Navlinks, Uhrzeit.
 */
if (!isset($_SESSION['Id'])) {
    die("Fehler: Session abgelaufen! Bitte neu <a href='index.php'>einloggen</a>");
}

// Aktuelle Seite für Active-State ermitteln
$current_page = basename($_SERVER['SCRIPT_NAME']);

// Ungelesene Nachrichten zählen (mini-Query, kein volles Account-Objekt nötig)
$_nav_uid = (int)$_SESSION['Id'];
$_nav_mail = 0;
$_nav_q = @mysqli_query($verbindung, "SELECT COUNT(*) as n FROM mail WHERE empfaenger='$_nav_uid' AND neu=1 AND del=0");
if ($_nav_q && $_nav_r = mysqli_fetch_assoc($_nav_q)) {
    $_nav_mail = (int)$_nav_r['n'];
}

// Spieler-Nick + Level (schnell)
$_nav_nick  = $_SESSION['nick'] ?? '';
$_nav_level = 0;
if ($_nav_nick === '') {
    $_nav_q2 = @mysqli_query($verbindung, "SELECT nickname, level FROM account WHERE id='$_nav_uid' LIMIT 1");
    if ($_nav_q2 && $_nav_r2 = mysqli_fetch_assoc($_nav_q2)) {
        $_nav_nick  = $_nav_r2['nickname'];
        $_nav_level = (int)$_nav_r2['level'];
    }
} else {
    $_nav_q2 = @mysqli_query($verbindung, "SELECT level FROM account WHERE id='$_nav_uid' LIMIT 1");
    if ($_nav_q2 && $_nav_r2 = mysqli_fetch_assoc($_nav_q2)) {
        $_nav_level = (int)$_nav_r2['level'];
    }
}

/**
 * Hilfsfunktion: Nav-Link rendern
 */
function _nav_link(string $href, string $icon, string $label, string $current, string $page_key, int $badge = 0): void {
    $active = ($current === $page_key || $current === $href) ? ' active' : '';
    $badge_html = $badge > 0 ? ' <span class="badge">' . $badge . '</span>' : '';
    echo '<li><a href="' . htmlspecialchars($href) . '" class="' . ltrim($active) . '">';
    echo '<i class="nav-icon">' . $icon . '</i>';
    echo htmlspecialchars($label) . $badge_html;
    echo '</a></li>' . "\n";
}
?>

<nav id="ga-nav">

  <!-- Logo -->
  <div class="ga-nav-logo">
    <span class="ga-nav-logo-title">Galaxy Adventures</span>
    <span class="ga-nav-logo-sub">Star Trek Browser Game</span>
  </div>

  <!-- Spieler-Info -->
  <div class="ga-nav-player">
    <strong><?= htmlspecialchars($_nav_nick) ?></strong>
    <span>Stufe <span class="ga-nav-player-level"><?= $_nav_level ?></span></span>
  </div>

  <!-- Navlinks -->
  <ul class="ga-nav-links">
    <?php _nav_link('main.php',          '◈',  'Übersicht',      $current_page, 'main.php'); ?>
    <?php _nav_link('planetchoice.php',  '🌍', 'Kolonien',       $current_page, 'planetchoice.php'); ?>
    <?php _nav_link('schiffchoice.php',  '🚀', 'Schiffe',        $current_page, 'schiffchoice.php'); ?>
    <?php _nav_link('flotte.php?fid=0',  '⚓', 'Flotten',        $current_page, 'flotte.php'); ?>

    <li><hr class="ga-nav-sep"></li>

    <?php _nav_link('kommunikation.php', '✉️', 'Kommunikation',  $current_page, 'kommunikation.php', $_nav_mail); ?>
    <?php _nav_link('quest_v2.php',      '📋', 'Quests',         $current_page, 'quest_v2.php'); ?>

    <li><hr class="ga-nav-sep"></li>

    <?php _nav_link('datenbank.php',     '📖', 'Datenbank',      $current_page, 'datenbank.php'); ?>
    <?php _nav_link('statistik.php',     '📊', 'Statistik',      $current_page, 'statistik.php'); ?>
    <?php _nav_link('tickets.php',       '🎫', 'Hilfe',          $current_page, 'tickets.php'); ?>

    <li><hr class="ga-nav-sep"></li>

    <?php _nav_link('option.php',        '⚙️', 'Optionen',       $current_page, 'option.php'); ?>
    <?php _nav_link('logout.php',        '⏻',  'Logout',         $current_page, 'logout.php'); ?>

    <?php if ($_nav_uid < 100 && $_nav_uid > 0): ?>
      <li><hr class="ga-nav-sep"></li>
      <?php _nav_link('order.php', '🔧', 'Schiff beantragen', $current_page, 'order.php'); ?>
    <?php endif; ?>

    <?php if ($_nav_uid == 1 || $_nav_uid == 3): ?>
      <?php _nav_link('quest_admin.php', '🛠️', 'Quest-Admin', $current_page, 'quest_admin.php'); ?>
    <?php endif; ?>
  </ul>

  <!-- Uhrzeit -->
  <div class="ga-nav-clock">
    <div><?= date('d.m.Y') ?></div>
    <div id="time">--:--:--</div>
  </div>

</nav>

<!-- Hauptbereich öffnen -->
<div id="ga-main">

<!-- Mobile Hamburger -->
<button class="ga-hamburger" onclick="document.getElementById('ga-nav').classList.toggle('open')" aria-label="Menü">☰</button>

<script>
// Uhrzeit-Ticker (ersetzt den alten time()-Call)
(function () {
    function tick() {
        const el = document.getElementById('time');
        if (!el) return;
        const now = new Date();
        const p = n => String(n).padStart(2, '0');
        el.textContent = p(now.getHours()) + ':' + p(now.getMinutes()) + ':' + p(now.getSeconds());
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
