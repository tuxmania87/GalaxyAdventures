<?php
/**
 * head.php — Gemeinsamer Header für alle eingeloggten Seiten
 *
 * Aufgaben:
 *  - Tick-Check (Weiterleitung zu maintick.php wenn Tick läuft)
 *  - Session-Start + Beta/Gamestatus-Check
 *  - Ausgabe des HTML <head> mit style_v2.css
 *  - Öffnet <body> und <div id="ga-wrap">
 *
 * Geändert in v2: style.css → style_v2.css, jQuery/thickbox entfernt,
 * moderne Flexbox-Layout-Struktur, <dialog>-basierte Bestätigungen.
 */

include_once 'connect.php';
if (file_exists('config.php')) include_once 'config.php';
$verbindung = get_verbindung();

// --- Tick-Check ---
$tm = mysqli_query($verbindung, 'SELECT * FROM `ticklog` WHERE id=(SELECT max(id) FROM `ticklog`)') or exit($verbindung->error);
while ($tm2 = mysqli_fetch_array($tm)) {
    if ($tm2['status'] == 1) {
        header('Location: ' . (defined('GA_TICK_URL') ? GA_TICK_URL : 'maintick.php'));
        exit;
    }
}

session_start();

// Beta / Gamestatus
$beta = 0;
$tm = mysqli_query($verbindung, "SELECT beta FROM account WHERE id='" . intval($_SESSION['Id']) . "'");
if ($tm) { $tm = mysqli_fetch_array($tm); $beta = (int)($tm[0] ?? 0); }

$tm = mysqli_query($verbindung, 'SELECT * FROM `gamestatus` WHERE id=(SELECT max(id) FROM `gamestatus`)') or exit($verbindung->error);
while ($tm2 = mysqli_fetch_array($tm)) {
    if ($tm2['status'] == 'offline' && $beta == 0) {
        // header('Location: wartung.php'); exit;
    }
}

// Level-Check: Spieler in verbotenem Sektor → move.php
$ich_temp = mysqli_query($verbindung, "SELECT level FROM account WHERE id='" . intval($_SESSION['Id']) . "'");
if ($ich_temp) {
    $ich_temp = mysqli_fetch_array($ich_temp);
    $ich_temp = (int)($ich_temp[0] ?? 0);
    if ($ich_temp >= 4) {
        $tm = mysqli_query($verbindung, "SELECT * FROM systeme,planeten WHERE planeten.system=systeme.id AND systeme.x>=1000 AND systeme.y>=1000 AND planeten.besitzer='" . intval($_SESSION['Id']) . "'") or exit($verbindung->error);
        while ($tm2 = mysqli_fetch_array($tm)) {
            header('Location: move.php');
            exit;
        }
    }
}

// --- Mail-Popup prüfen ---
$_head_popup = false;
$ssid = (int)$_SESSION['Id'];
$pa = mysqli_query($verbindung, "SELECT id FROM mail WHERE popup='1' AND empfaenger='$ssid' LIMIT 1");
if ($pa && mysqli_fetch_array($pa)) {
    $_head_popup = true;
}

// Chat-Seite?
$_head_is_main = in_array(
    $_SERVER['SCRIPT_NAME'],
    ['/GalaxyAdventures/main.php', '/de/index.php', '/main.php']
);
$_head_chat_active = $_head_is_main && (ctype_digit((string)$_SESSION['Id']) || !empty($_SESSION['nick']));
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Star Trek · Galaxy Adventures II</title>

  <!-- Design System v2 -->
  <link rel="stylesheet" href="style_v2.css">

  <!-- Tooltip-Lib (minimal, beibehalten für Rückwärtskompatibilität) -->
  <script src="wz_tooltip.js" defer></script>

  <!-- Chat-AJAX (jQuery-freie Variante) -->
  <script src="ajax.js" defer></script>

  <script>
    // --- Bestätigungs-Dialoge (ersetzt browser confirm()) ---
    // Wird von alten PHP-Seiten per onclick="return frage(N)" aufgerufen.
    // Fällt auf browser confirm() zurück wenn kein <dialog> vorhanden.
    function frage(zahl) {
      const msgs = {
        1: 'Willst du das Gebäude wirklich abreißen?',
        2: 'Willst du den Bau wirklich abbrechen?',
        3: 'Willst du deine Kolonie wirklich sprengen?',
        4: 'Willst du dein Schiff wirklich zerstören?'
      };
      const msg = msgs[zahl] || 'Bist du sicher?';
      const dlg = document.getElementById('ga-confirm-dialog');
      if (!dlg) return confirm(msg);
      document.getElementById('ga-confirm-text').textContent = msg;
      return new Promise(resolve => {
        dlg.showModal();
        document.getElementById('ga-confirm-ok').onclick  = () => { dlg.close(); resolve(true); };
        document.getElementById('ga-confirm-no').onclick  = () => { dlg.close(); resolve(false); };
      });
    }

    // LCAR-Hover-Effekte (altes Code-Erbgut)
    function highlight(id) {
      const l = document.getElementById('lcarl_' + id);
      const r = document.getElementById('lcarr_' + id);
      const h = document.getElementById('lcarh_' + id);
      if (l) l.src = 'images/misc/lh.gif';
      if (r) r.src = 'images/misc/rh.gif';
      if (h) h.src = 'images/misc/rhelph.gif';
    }
    function downlight(id) {
      const l = document.getElementById('lcarl_' + id);
      const r = document.getElementById('lcarr_' + id);
      const h = document.getElementById('lcarh_' + id);
      if (l) l.src = 'images/misc/l.gif';
      if (r) r.src = 'images/misc/r.gif';
      if (h) h.src = 'images/misc/rhelp.gif';
    }
    function highlighthelp(id) {
      const el = document.getElementById('lcarh_' + id);
      if (el) el.src = 'images/misc/rhelphtt.gif';
    }
    function downlighthelp(id) {
      const el = document.getElementById('lcarh_' + id);
      if (el) el.src = 'images/misc/rhelp.gif';
    }

    // Chat-Funktionen
    function sendchat() {
      const input = document.getElementById('sendchatform');
      if (!input || !input.value.trim()) return;
      const text = 'chat_send.php?hash=' + encodeURIComponent(input.value);
      input.value = '';
      setRequest(text);
      setRequest('chat_query.php');
    }

    // goback()
    function goback() { history.back(); }

    <?php if ($_head_popup): ?>
    // Mail-Popup
    window.addEventListener('DOMContentLoaded', function () {
      const dlg = document.getElementById('ga-mail-popup');
      if (dlg) dlg.showModal();
    });
    <?php endif; ?>

    <?php if ($_head_chat_active): ?>
    // Chat-Refresh auf main.php
    window.addEventListener('DOMContentLoaded', function () {
      setRequest('chat_query.php');
      setInterval(function () { setRequest('chat_query.php'); }, 1200);
    });
    <?php endif; ?>
  </script>
</head>
<body>

<!-- Bestätigungs-Dialog -->
<dialog class="ga-dialog" id="ga-confirm-dialog">
  <h3>Bestätigung</h3>
  <p id="ga-confirm-text"></p>
  <div class="dialog-actions">
    <button class="btn" id="ga-confirm-no">Abbrechen</button>
    <button class="btn btn-danger" id="ga-confirm-ok">Bestätigen</button>
  </div>
</dialog>

<?php if ($_head_popup): ?>
<!-- Mail-Popup -->
<dialog class="ga-dialog" id="ga-mail-popup">
  <h3>✉️ Neue Nachricht</h3>
  <p>Du hast eine neue Nachricht erhalten.</p>
  <div class="dialog-actions">
    <button class="btn" onclick="document.getElementById('ga-mail-popup').close()">Schließen</button>
    <a href="mail.php" class="btn btn-primary">Zur Nachricht</a>
  </div>
</dialog>
<?php endif; ?>

<div id="ga-wrap">
