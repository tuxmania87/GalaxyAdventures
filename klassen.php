<?php
/**
 * klassen.php - Autoloader
 * Lädt alle Klassen aus dem classes/ Verzeichnis.
 * Bleibt für Rückwärtskompatibilität erhalten.
 *
 * Ladereihenfolge ist explizit, da einige Klassen von anderen abhängen:
 *   Rohling, Frachtraum  → definiert in class.ship.php
 *   Planeten extends Rohling, Warenkonto extends Frachtraum → müssen danach
 */
include_once __DIR__ . '/connect.php';

// Basis-Klassen zuerst (Rohling, Frachtraum)
include_once __DIR__ . '/classes/class.ship.php';

// Restliche Klassen in beliebiger Reihenfolge (inkl. class.questengine.php)
foreach (glob(__DIR__ . '/classes/class.*.php') as $classFile) {
    include_once $classFile;
}
