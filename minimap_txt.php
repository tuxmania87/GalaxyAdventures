<?php
include_once("connect.php");

$getx=$_POST["x"];
$gety=$_POST["y"];


for($y=20;$y<=120;$y++)
for($x=20;$x<=120;$x++)
{



$checked="";
$abfrage=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ!='s' AND x='$x' AND y='$y'");
while($feld=mysqli_fetch_array($abfrage))
{
$checked=$feld["typ"]!='m'?$feld["typ"]:$feld["klasse"];
}
/*
if($checked=='m')
echo '1';
if($checked=='l')
echo '2';
if($checked=='i')
echo '3';
if($checked=='z')
echo '4';
if($checked=='d')
echo '5';
if($checked=='h')
echo '6';
if($checked=='g')
echo '7';
if($checked=='b')
echo '8';
if($checked=='e')
echo '9';
if($checked=='x')
echo 'x';
*/
echo $checked;


if($checked=='')
echo '0';

if($x==120) echo '<br />';
}

echo '<form action="minimap.php" method="post">x/y<br /><input type="text" name="x" size="2"> / <input type="text" name="y" size="3"><br /><br /><input type="submit" value="Startkoordinate einstellen"></form>';

?>
