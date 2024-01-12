<?php

include_once 'connect.php';
include 'klassen.php';

$verbindung = get_verbindung();

echo '<table>';

$abfrage = mysqli_query($verbindung, 'SELECT * FROM chat ORDER BY id DESC LIMIT 70');
while ($row = mysqli_fetch_array($abfrage)) {
    $text = pruefetext($row['nachricht']);
    $usr = new Account($row['uid']);
    echo '<tr><td style="width:135px;">',$row['zeit'],'</td><td style="min-width:140px;">',$usr->nickname,'</td><td>',$text,'</td></tr>';
}
echo '</table>';
