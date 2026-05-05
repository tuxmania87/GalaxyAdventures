<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");

include_once 'auth.php';
$userId = requireLogin();
$sid = requireIntParam('sid');
$schiff = new schiff($sid);
if ($schiff->besitzer !== $userId) exit('Fehler: Nicht dein Schiff.');
if ($schiff->klasse !== 'Horchposten') exit('Fehler: Kein Horchposten.');
{

echo '<h3>Bericht des Horchposten im Sektor ',$schiff->x,'/',$schiff->y,'</h3>';
echo '<table class="bordered2"><tr><td>Datum</td><td>Bild</td><td>Klasse</td><td>Besitzer</td><td>Sektor</td></tr>';
$result=mysqli_query($verbindung, "SELECT * FROM horchlog WHERE ich='$sid' ORDER BY id DESC");
while($row=mysqli_fetch_array($result))
{
echo '<tr><td>',gerdatum($row["datum"]),'</td><td><img src="',$row["img"],'" border="0" /></td><td>',$row["klasse"],'</td><td>',id2name($row["besitzer"]),'</td><td>',$row["x"],'/',$row["y"],'</td></tr>';
}
echo '</table>';
echo '<br /><a href="schiffe.php?sid=',$sid,'">zur&uuml;ck</a>';
}
include("foot.php"); 
?>