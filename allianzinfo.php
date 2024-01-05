<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';
include_once 'connect.php';

$verbindung = get_verbindung();

$aid = $_GET['id'];

// BETRY

$betray = false;
if (!ctype_digit($aid)) {
    exit('Fehler: ID not valid');
}

// ENDE

$allianz = new Allianz($aid);
$aleiter = new Account($allianz->leiter);
echo '<table class="invitetable"><tr><th>Allianzname</th><td>', $allianz->name, '</td></tr><tr><th>K&uuml;rzel</th><td>', $allianz->tag, '</td></tr><tr><th>Leiter</th><td>', $aleiter->nickname, '</td></tr><tr><th>Beschreibung</th><td>', nl2br(pruefetext($allianz->info)), '</td></tr></table>';
echo '<br /><hr /><br /><table class="invitetable"><tr><th>Spieler</th><th>Nachricht senden</th></tr>';
$who = mysqli_query($verbindung, "SELECT * FROM account WHERE allianz='$aid'");
while ($row = mysqli_fetch_array($who)) {
    $usr = new Account($row['id']);
    echo '<tr><td>', $row['id'] == $allianz->leiter ? '<span style="color:red;font-weight:bold;">' : '<span>', $usr->nickname, '</span></td><td>';

    $bu = new Button('newmail.php?to='.$row['id'], 'Nachricht senden');
    $bu->printme();
    echo '</td></tr>';
}
echo '</table>';

echo '<br />';
$bu = new Button('allianz.php', 'zurück zur Allianzübersicht');
$bu->printme();

include 'foot.php';
