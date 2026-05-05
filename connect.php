<?php

error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);

function get_verbindung(): mysqli
{
    $jsonData = json_decode(file_get_contents('config.json'));
    $verbindung = mysqli_connect(
        $jsonData->database->host,
        $jsonData->database->user,
        $jsonData->database->password
    );
    mysqli_select_db($verbindung, $jsonData->database->dbname);
    return $verbindung;
}

/**
 * Splitter: "was-bauzeit-untergrund-hull-aktiv" -> 5 Variablen
 * War: char-by-char Schleife, jetzt: explode
 */
function splitfeld(string $text, &$was, &$bauzeit, &$auf, &$hull, &$aktiv): void
{
    $parts   = explode('-', $text, 5);
    $was     = $parts[0] ?? '';
    $bauzeit = $parts[1] ?? '';
    $auf     = $parts[2] ?? '';
    $hull    = $parts[3] ?? 60;
    $aktiv   = $parts[4] ?? 1;
    if ($hull   === '') $hull   = 60;
    if ($aktiv  === '') $aktiv  = 1;
}

/**
 * Splitter: "a-b" -> zwei Variablen (Wrapper um explode)
 */
function splitintwo(string $wert, &$a, &$b): void
{
    [$a, $b] = explode('-', $wert, 2) + ['', ''];
}

/**
 * Prüft ob ein Timestamp (Y-m-d H:i:s) innerhalb der letzten 4 Minuten liegt.
 */
function isonline(string $aktion): bool
{
    $ts = strtotime($aktion);
    if ($ts === false) return false;
    $now = time();
    return ($ts >= $now - 240 && $ts <= $now);
}

/**
 * Datum von MySQL-Format (Y-m-d H:i:s) nach deutschem Format (d.m.Y H:i:s).
 */
function gerdatum(string $datum): string
{
    $ts = strtotime($datum);
    return $ts !== false ? date('d.m.Y H:i:s', $ts) : $datum;
}

/**
 * Gibt die Anzahl belegter Schiffsslots eines Spielers zurück.
 */
function getSlot(int $id): float
{
    $verbindung = get_verbindung();

    // Slot-Gewichte pro Klasse
    $slotGewichte = [
        'Tanker'       => 1,
        'Erzfrachter'  => 1,
        'Oberth'       => 1.5,
        'Miranda'      => 3,
        'Constitution' => 5,
    ];

    $count   = 0.0;
    $abfrage = mysqli_query($verbindung, "SELECT klasse, skillbase FROM schiffe WHERE typ='s' AND besitzer='$id'");
    while ($schiff = mysqli_fetch_array($abfrage)) {
        $count += $slotGewichte[$schiff['klasse']] ?? 0;
        if ($schiff['skillbase'] == 1) {
            $count += 4;
        }
    }
    return $count;
}

/**
 * Gibt die letzte ID einer Tabelle zurück.
 */
function checkforlastid(string $name): int
{
    $verbindung = get_verbindung();
    $tt = mysqli_query($verbindung, "SELECT MAX(id) FROM `$name`");
    $row = mysqli_fetch_array($tt);
    return intval($row[0] ?? 0);
}

/**
 * Bubblesort — PHP hat sort(), das ist nur noch für Kompatibilität.
 * @deprecated Nutze stattdessen sort()
 */
function bubblesort(array $feld): array
{
    sort($feld);
    return $feld;
}

$verbindung = get_verbindung();
include 'pruefetext.php';

// DB optimieren (einmal pro Request ist ineffizient - idealerweise als Cronjob)
mysqli_query($verbindung, 'OPTIMIZE TABLE `account`, `allianz`, `allychannel`, `bauplan`, `chat`,
    `counter`, `ebay`, `erfolge`, `flotte`, `forschung`, `fragen`, `gamestatus`, `handel`,
    `horchlog`, `iplog`, `item`, `kn_log`, `konto`, `logbuch`, `mail`, `news`, `npclog`,
    `planet2`, `planeten`, `planetenlog`, `quests`, `schiffe`, `schiffsmodule`, `sebay`,
    `skn`, `spawn`, `sskn`, `status`, `systeme`, `ticklog`, `tip`, `vertrag`, `weltraum`');

// Letzte Aktion tracken
if (session_id() !== '') {
    $action  = date('Y-m-d H:i:s');
    $selfid  = mysqli_real_escape_string($verbindung, session_id());
    mysqli_query($verbindung, "UPDATE account SET aktion='$action' WHERE sessionid='$selfid'");
}
