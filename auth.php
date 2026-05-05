<?php
/**
 * Galaxy Adventures - Zentrale Auth & Validierungs-Hilfsfunktionen
 *
 * Ersetzt den Copy-Paste "CHEATSCHUTZ" in allen Controller-Dateien.
 * Einbinden via: include 'auth.php'; (nach head.php / klassen.php)
 */

/**
 * Prüft ob der User eingeloggt ist.
 * Bricht mit Fehlermeldung ab wenn nicht.
 * Gibt die Session-ID als Integer zurück.
 */
function requireLogin(): int
{
    if (!isset($_SESSION['Id']) || !ctype_digit((string)$_SESSION['Id']) || $_SESSION['Id'] <= 0) {
        exit('Fehler: Session abgelaufen. Bitte neu <a href="index.php">einloggen</a>.');
    }
    return intval($_SESSION['Id']);
}

/**
 * Liest einen GET-Parameter als validierten Integer.
 * Bricht ab wenn nicht vorhanden oder nicht numerisch.
 */
function requireIntParam(string $key, string $source = 'GET'): int
{
    $bag = $source === 'POST' ? $_POST : $_GET;
    if (!isset($bag[$key]) || !ctype_digit((string)$bag[$key])) {
        exit("Fehler: Parameter '$key' fehlt oder ist ungültig.");
    }
    return intval($bag[$key]);
}

/**
 * Liest einen optionalen GET-Parameter als Integer.
 * Gibt $default zurück wenn nicht vorhanden.
 */
function optionalIntParam(string $key, int $default = 0, string $source = 'GET'): int
{
    $bag = $source === 'POST' ? $_POST : $_GET;
    if (!isset($bag[$key]) || !ctype_digit((string)$bag[$key])) {
        return $default;
    }
    return intval($bag[$key]);
}

/**
 * Liest einen POST-Parameter als escaped String.
 * Bricht ab wenn nicht vorhanden.
 */
function requireStringParam(string $key, string $source = 'POST'): string
{
    global $verbindung;
    $bag = $source === 'POST' ? $_POST : $_GET;
    if (!isset($bag[$key])) {
        exit("Fehler: Parameter '$key' fehlt.");
    }
    changeit($bag[$key]);
    return mysqli_real_escape_string($verbindung, $bag[$key]);
}

/**
 * Liest einen optionalen POST-String, gibt '' zurück wenn nicht vorhanden.
 */
function optionalStringParam(string $key, string $default = '', string $source = 'POST'): string
{
    global $verbindung;
    $bag = $source === 'POST' ? $_POST : $_GET;
    if (!isset($bag[$key])) {
        return $default;
    }
    $val = $bag[$key];
    changeit($val);
    return mysqli_real_escape_string($verbindung, $val);
}

/**
 * Prüft ob der eingeloggte User Besitzer eines Objekts ist.
 * $ownerId = die besitzer-ID aus der DB
 * Bricht ab wenn nicht übereinstimmend.
 */
function requireOwnership(int $ownerId, string $context = 'Objekt'): void
{
    $userId = requireLogin();
    if ($userId !== $ownerId) {
        exit("Fehler: Du bist nicht der Besitzer dieses $context.");
    }
}

/**
 * Gibt den eingeloggte User-Account zurück (Account-Objekt).
 * Bricht ab wenn nicht eingeloggt.
 */
function getLoggedInAccount(): Account
{
    $id = requireLogin();
    return new Account($id);
}

/**
 * Prüft ob ein Admin eingeloggt ist (Id < 100).
 */
function requireAdmin(): int
{
    $id = requireLogin();
    if ($id >= 100) {
        exit('Fehler: Kein Adminzugriff.');
    }
    return $id;
}
