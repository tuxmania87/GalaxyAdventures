<?php
/**
 * Galaxy Adventures - Zentrale Konfiguration
 *
 * Alle deployment-spezifischen Werte hier eintragen.
 * Diese Datei NICHT ins Repository committen (steht in .gitignore).
 */

// Basis-URL der Installation (kein trailing slash)
define('GA_BASE_URL', 'http://localhost/GalaxyAdventures');

// Spielname
define('GA_GAME_NAME', 'Star Trek - Galaxy Adventures II');

// Admin-E-Mail
define('GA_ADMIN_EMAIL', 'admin@example.com');

// Absender-Adresse für Systemmails
define('GA_MAIL_FROM', 'noreply@example.com');

// Pfad zum Tick-Script (für head.php redirect)
define('GA_TICK_URL', GA_BASE_URL . '/maintick.php');

// Entwicklungsmodus: true = Fehlermeldungen anzeigen
define('GA_DEV_MODE', true);

if (GA_DEV_MODE) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
