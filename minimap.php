<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");

$getx=$_POST["x"];
$gety=$_POST["y"];

echo '<table class="bordered3">';

echo '<tr><td>x/y</td>';
for($i=1+$getx;$i<=20+$getx;$i++) echo '<td>',$i,'</td>';
echo '</tr>';

for($y=1+$gety;$y<=20+$gety;$y++)
for($x=1+$getx;$x<=20+$getx;$x++)
{
if($x==1+$getx && $y>1+$gety-1) echo '<tr><td>',$y,'</td>';


$checked="";
$abfrage=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ!='s' AND x='$x' AND y='$y'");
while($feld=mysqli_fetch_array($abfrage))
{
$checked=$feld["typ"];
}

if($checked=='m')
echo '<td><img src="minimap_m.gif"></td>';
if($checked=='d')
echo '<td><img src="minimap_d.gif"></td>';
if($checked=='h')
echo '<td><img src="minimap_h.jpg"></td>';
if($checked=='g')
echo '<td><img src="minimap_g.jpg"></td>';
if($checked=='b')
echo '<td><img src="minimap_n.jpg"></td>';
if($checked=='e')
echo '<td><img src="minimap_e.gif"></td>';
if($checked=='w')
echo '<td><img src="minimap_w.gif"></td>';


if($checked=='')
echo '<td></td>';

if($x==30+$getx) echo '</tr>';
}

echo '</table>';
echo '<form action="minimap.php" method="post">x/y<br /><input type="text" name="x" size="2"> / <input type="text" name="y" size="3"><br /><br /><input type="submit" value="Startkoordinate einstellen"></form>';

include("foot.php");
?>
