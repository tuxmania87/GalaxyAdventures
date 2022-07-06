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
$tt=mysql_query("SELECT * FROM schiffe WHERE typ='m' AND typ='h' AND typ='d' AND x='$newx' AND y='$newy'");
while($t=mysql_fetch_array($tt))
$ch=true;
if(!$ch)
mysql_query("INSERT INTO schiffe (typ,x,y) VALUES ('d','$newx','$newy')") or die(mysql_error());
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
$abfrage=mysql_query("SELECT * FROM schiffe WHERE typ='m' AND x='$x' AND y='$y'");
while($feld=mysql_fetch_array($abfrage))
{
$checked="m";
}
$abfrage=mysql_query("SELECT * FROM schiffe WHERE typ='d' AND x='$x' AND y='$y'");
while($feld=mysql_fetch_array($abfrage))
{
$checked="d";
}
$abfrage=mysql_query("SELECT * FROM schiffe WHERE typ='h' AND x='$x' AND y='$y'");
while($feld=mysql_fetch_array($abfrage))
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
