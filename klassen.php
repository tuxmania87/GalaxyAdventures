<?php
/**
 * klassen.php - Autoloader
 * Lädt alle Klassen aus dem classes/ Verzeichnis.
 * Bleibt für Rückwärtskompatibilität erhalten.
 */
include_once __DIR__ . '/connect.php';

foreach (glob(__DIR__ . '/classes/class.*.php') as $classFile) {
    include_once $classFile;
}
