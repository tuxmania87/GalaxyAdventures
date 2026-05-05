<?php
include_once 'connect.php';

session_start();
include 'klassen.php';

$verbindung = get_verbindung();

$hash = $_GET['hash'];
$sesid = $_SESSION['Id'] == '' || $_SESSION['Id'] == 0 ? $_SESSION['nick'] : $_SESSION['Id'];
mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$hash','".$_SERVER['REMOTE_ADDR']."','".$sesid."')") or exit($verbindung->error);
mysqli_query($verbindung, "UPDATE account SET aktion='".date('Y-m-d H:i:s')."' WHERE id='".intval(\$_SESSION['Id'])."'");

// HASH abfrage
if (substr($hash, 0, 5) == '!seen') {
    if (ctype_digit(substr($hash, 6, strlen($hash) - 6))) {
        $usr = new Account(substr($hash, 6, strlen($hash) - 6));
        $msg = 'User [Spieler:'.$usr->id.'] war zuletzt online am: '.gerdatum($usr->aktion);
        mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$msg','".$_SERVER['REMOTE_ADDR']."','9')") or exit($verbindung->error);
    } else {
        $msg = 'Falscher Syntax: !seen <ID des Users>';
        mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$msg','".$_SERVER['REMOTE_ADDR']."','9')") or exit($verbindung->error);
    }
}

if (substr($hash, 0, 8) == '!wpunkte') {
    if (ctype_digit(substr($hash, 9, strlen($hash) - 9))) {
        $usr = new Account(substr($hash, 9, strlen($hash) - 9));
        $msg = 'User [Spieler:'.$usr->id.'] hat '.$usr->wpunkte.' Wirtschaftspunkte!';
        mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$msg','".$_SERVER['REMOTE_ADDR']."','9')") or exit($verbindung->error);
    } else {
        $msg = 'Falscher Syntax: !wpunkte <ID des Users>';
        mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$msg','".$_SERVER['REMOTE_ADDR']."','9')") or exit($verbindung->error);
    }
}

if (substr($hash, 0, 7) == '!gacode') {
    $msg = 'Bitte verwende statt HTML Code ->  Gacode: [link:http://www.galaxy-adventures.net/de/gacode.htm]';
    mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$msg','".$_SERVER['REMOTE_ADDR']."','9')") or exit($verbindung->error);
}

if (substr($hash, 0, 5) == '!help') {
    $msg = 'folgende Kommandos sind verfuegbar: ';
    mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$msg','".$_SERVER['REMOTE_ADDR']."','9')") or exit($verbindung->error);
    $msg = '!seen ID - Zeigt an wann User mit Nummer ID zuletzt online war.';
    mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$msg','".$_SERVER['REMOTE_ADDR']."','9')") or exit($verbindung->error);
    $msg = '!wpunkte ID - Zeigt die Wirtschaftspunkte fuer User ID an.';
    mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$msg','".$_SERVER['REMOTE_ADDR']."','9')") or exit($verbindung->error);
    $msg = '!gacode - Hinweise zur Textformatierung.';
    mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$msg','".$_SERVER['REMOTE_ADDR']."','9')") or exit($verbindung->error);
    $msg = '!help - Diese Seite.';
    mysqli_query($verbindung, "INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date('Y-m-d H:i:s')."','$msg','".$_SERVER['REMOTE_ADDR']."','9')") or exit($verbindung->error);
}
?>



