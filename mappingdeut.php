<?php
if($_GET["pw"]=='klaus') {
include("head.php");
include("nav.php");
include_once("connect.php");
if($_GET["set"]==1)
{
$newx=$_GET["setx"];
$newy=$_GET["sety"];
$ch=false;
$tt=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='m' AND typ='h' AND typ='d' AND x='$newx' AND y='$newy'");
while($t=mysqli_fetch_array($tt))
$ch=true;
if(!$ch)
mysqli_query($verbindung, "INSERT INTO schiffe (typ,x,y) VALUES ('d','$newx','$newy')") or die(mysqli_error($verbindung));
else echo '!!ERROR!!';
}
$getx=$_GET["x"];
$gety=$_GET["y"];
echo '<table>';
for($y=1+$gety;$y<=10+$gety;$y++)
{
for($x=1+$getx;$x<=10+$getx;$x++)
{
if($x==1+$getx) echo '<tr>';
$checked="";
$abfrage=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='m' AND x='$x' AND y='$y'");
while($feld=mysqli_fetch_array($abfrage))
{
$checked="m";
}
$abfrage=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='d' AND x='$x' AND y='$y'");
while($feld=mysqli_fetch_array($abfrage))
{
$checked="d";
}
$abfrage=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='h' AND x='$x' AND y='$y'");
while($feld=mysqli_fetch_array($abfrage))
{
$checked="h";
}

if($checked=='m')
echo '<td><img src="planet.jpg"></td>';
if($checked=='d')
echo '<td><img src="deut.jpg"></td>';
if($checked=='h')
echo '<td><img src="hstation.jpg"></td>';
if($checked=='')
echo '<td><a href="mappingdeut.php?pw=klaus&x=',$getx,'&y=',$gety,'&set=1&setx=',$x,'&sety=',$y,'"><img src="weltraum.jpg"></a></td>';
if($x==10+$getx) echo '</tr>';
}
}
echo '</table>';
include("foot.php");
}
?>
