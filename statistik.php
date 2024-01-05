<?php

include 'head.php';
include 'navlogged.php';
include_once 'connect.php';

$verbindung = get_verbindung();

echo '<h2>Statistiken</h2><i>Traue keine Stastik die du nicht selber gef�lscht hast - Altes Ferengi Gesetz</i><br /><br />';
$abfrage = mysqli_query($verbindung, "SELECT COUNT(*) FROM schiffe WHERE klasse='Tanker' AND besitzer>9");
$variable = mysqli_fetch_array($abfrage);
echo 'Anzahl der Tanker: ',$variable[0],'<br />';
$abfrage = mysqli_query($verbindung, "SELECT COUNT(*) FROM schiffe WHERE klasse='Erzfrachter' AND besitzer>9");
$variable = mysqli_fetch_array($abfrage);
echo 'Anzahl der Frachter: ',$variable[0],'<br />';
$abfrage = mysqli_query($verbindung, "SELECT COUNT(*) FROM schiffe WHERE klasse='Oberth' AND besitzer>9");
$variable = mysqli_fetch_array($abfrage);
echo 'Anzahl der Oberth: ',$variable[0],'<br />';

$abfrage = mysqli_query($verbindung, "SELECT COUNT(*) FROM schiffe WHERE klasse='Miranda' AND besitzer>9");
$variable = mysqli_fetch_array($abfrage);
echo 'Anzahl der Miranda: ',$variable[0],'<br />';

$abfrage = mysqli_query($verbindung, "SELECT COUNT(*) FROM schiffe WHERE klasse='Constitution' AND besitzer>9");
$variable = mysqli_fetch_array($abfrage);
echo 'Anzahl der Constitution: ',$variable[0],'<br />';
$abfrage = mysqli_query($verbindung, "SELECT COUNT(*) FROM schiffe WHERE typ='m' AND besitzer>9");
$variable = mysqli_fetch_array($abfrage);
echo 'Anzahl besiedelter Planeten: ',$variable[0],'<br />';
$abfrage = mysqli_query($verbindung, "SELECT COUNT(*) FROM schiffe WHERE typ='m' AND besitzer=2");
$variable = mysqli_fetch_array($abfrage);
echo 'Anzahl freier Planeten: ',$variable[0],'<br />';

include 'foot.php';
