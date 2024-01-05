<?php

error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);
function get_verbindung()
{
    $jsonString = file_get_contents('config.json');
    $jsonData = json_decode($jsonString);

    $verbindung = mysqli_connect($jsonData->database->host, $jsonData->database->user, $jsonData->database->password);

    // mysqli_query($verbindung, "SET NAMES 'utf8'") or die($verbindung->error);
    // mysqli_query($verbindung, "SET character_set_connection = 'utf8'") or die($verbindung->error);
    // mysqli_query($verbindung, "SET character_set_database = 'utf8'") or die($verbindung->error);
    // mysqli_query($verbindung, "SET character_set_server = 'utf8'") or die($verbindung->error);

    mysqli_select_db($verbindung, $jsonData->database->dbname);

    return $verbindung;
}

function splitintwo($wert, &$a, &$b)
{
    $a = '';
    $b = '';
    $strich = false;
    for ($i = 0; $i <= strlen($wert) - 1; ++$i) {
        if ($wert[$i] == '-') {
            $strich = true;
        } else {
            if ($strich) {
                $b .= $wert[$i];
            } else {
                $a .= $wert[$i];
            }
        }
    }
}
function isonline($aktion)
{
    $tmpvar1 = $aktion[0].$aktion[1].$aktion[2].$aktion[3].$aktion[4].$aktion[5].$aktion[6].$aktion[7].$aktion[8].$aktion[9];
    if ($tmpvar1 == date('Y-m-d')) {
        $tmpvar2 = date('H') * 3600 + date('i') * 60 + date('s');
        $tmpvar3 = $aktion[11] * 10 + $aktion[12];
        $tmpvar3 *= 3600;
        $tmpvar4 = $aktion[14].$aktion[15];
        $tmpvar3 += $tmpvar4 * 60;
        $tmpvar4 = $aktion[17].$aktion[18];
        $tmpvar3 += $tmpvar4;
        if ($tmpvar3 >= $tmpvar2 - 240 && $tmpvar3 <= $tmpvar2) {
            return true;
        } else {
            return false;
        }
    }
}

function gerdatum($datum)
{
    $returnvar = $datum[8].$datum[9].'.'.$datum[5].$datum[6].'.'.$datum[0].$datum[1].$datum[2].$datum[3].' '.$datum[11].$datum[12].':'.$datum[14].$datum[15].':'.$datum[17].$datum[18];

    return $returnvar;
}

function getSlot($id)
{
    $verbindung = get_verbindung();
    $count = 0;
    $abfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='s' AND besitzer='$id'");
    while ($schiff = mysqli_fetch_array($abfrage)) {
        if ($schiff['klasse'] == 'Tanker') {
            ++$count;
        }
        if ($schiff['klasse'] == 'Erzfrachter') {
            ++$count;
        }
        if ($schiff['klasse'] == 'Oberth') {
            $count += 1.5;
        }
        if ($schiff['klasse'] == 'Miranda') {
            $count += 3;
        }
        if ($schiff['klasse'] == 'Constitution') {
            $count += 5;
        }
        if ($schiff['skillbase'] == 1) {
            $count += 4;
        }
    }

    return $count;
}

function bubblesort($feld)
{
    $getauscht = false;
    while ($getauscht) {
        $getauscht = false;
        for ($i = 0; $i < sizeof($feld) - 1; ++$i) {
            if ($feld[$i] > $feld[$i + 1]) {
                $h = $feld[$i];
                $feld[$i] = $feld[$i + 1];
                $feld[$i + 1] = $h;
                $getauscht = true;
            }
        }
    }

    return $feld;
}

function checkforlastid($name)
{
    $verbindung = get_verbindung();
    $checkid = 0;
    $tt = mysqli_query($verbindung, "SELECT MAX(id) FROM $name ");
    while ($t = mysqli_fetch_array($tt)) {
        return $t[0];
    }
}

function splitfeld($text, &$was, &$bauzeit, &$auf, &$hull, &$aktiv)
{
    $eins = false;
    $zwei = false;
    $drei = false;
    $vier = false;
    $was = '';
    $bauzeit = '';
    $auf = '';
    $hull = '';
    $aktiv = '';
    for ($i = 0; $i < strlen($text); ++$i) {
        $weiter = true;
        if ($text[$i] == '-') {
            $weiter = false;
            if (!$eins) {
                $eins = true;
            } elseif (!$zwei) {
                $zwei = true;
            } elseif (!$drei) {
                $drei = true;
            } elseif (!$vier) {
                $vier = true;
            }
        }

        if ($weiter) {
            if ($vier) {
                $aktiv .= $text[$i];
            } elseif ($drei) {
                $hull .= $text[$i];
            } elseif ($zwei) {
                $auf .= $text[$i];
            } elseif ($eins) {
                $bauzeit .= $text[$i];
            } else {
                $was .= $text[$i];
            }
        }
    }
    if ($hull == '') {
        $hull = 60;
    }
    if ($aktiv == '') {
        $aktiv = 1;
    }
}

$verbindung = get_verbindung();
include 'pruefetext.php';
mysqli_query($verbindung, ' OPTIMIZE TABLE `account` , `allianz` , `allychannel` , `bauplan` , `chat` , `counter` , `ebay` , `erfolge` , `flotte` , `forschung` , `fragen` , `gamestatus` , `handel` , `horchlog` , `iplog` , `item` , `kn_log` , `konto` , `logbuch` , `mail` , `news` , `npclog` , `planet2` , `planeten` , `planetenlog` , `quests` , `schiffe` , `schiffsmodule` , `sebay` , `skn` , `spawn` , `sskn` , `status` , `systeme` , `ticklog` , `tip` , `vertrag` , `weltraum`');

// letze Aktion
$action = date('Y-m-d H:i:s');
$selfid = session_id();
if (session_id() != '') {
    mysqli_query($verbindung, "UPDATE account SET aktion='$action' WHERE sessionid='".$selfid."'");
}
