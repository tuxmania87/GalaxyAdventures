<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");

$sid=$_GET["sid"];

//BETRAY
$betray=false;
if(!ctype_digit($sid)) $betray=true;

if($betray) echo 'No valid ID'; else {

if($_POST["sent"]==1) {
$rate=$_POST["amount"];
$error="";
if($rate*50+$schiff->warpkern>$schiff->maxwarpkern) $rate=floor(($schiff->maxwarpkern-$schiff->warpkern)/50);
if($schiff->deuterium<$rate*2) $error="Zu wenig Deuterium an Board. Vorhanden: $schiff->deuterium. Verlangt: $rate*2";
if($schiff->antimaterie<$rate*2) $error="Zu wenig Antimaterie an Board. Vorhanden: $schiff->antimaterie. Verlangt: $rate*2";
if($schiff->dili<$rate) $error="Zu wenig Dilitium an Board. Vorhanden: $schiff->dili. Verlangt: $rate";
if($error=="") {
$schiff->deuterium-=($rate*2);
$schiff->antimaterie-=($rate*2);
$schiff->dili-=$rate;
$schiff->warpkern+=(50*$rate);
echo 'Dein Warpkern wurde aufgeladen!';
}
}

$schiff=new schiff($sid);
if($schiff->besitzer!=$_SESSION["Id"]) $betray=true;

if($betray) echo 'Not your Ship'; else {

echo '<h3>Warpkern-Aufladung</h3><br />';
echo 'Um 50 Einheiten des Warpkerns aufzuladen, brauchst du 1 Dilithium, 2 Antimaterie und 2 Deuterium. Pro Tick werden dann 2 Energie mehr erzeugt und die Ladung des Warpkerns verbaucht anstatt des Deuteriums.<br /><br />Waren auf deinem Schiff:';
echo '<br /><img src="images/dili.png" border="0" />Dilithium: ',$schiff->dili;
echo '<br /><img src="images/antimaterie.png" border="0" />Antimaterie: ',$schiff->antimaterie;
echo '<br /><img src="images/deuterium.png" border="0" />Deuterium: ',$schiff->deuterium;
echo '<br /><br /><img src="images/warpkern.png" border="0" />Warpkern: ',$schiff->warpkern;
echo '<br /><br /><form action="warpload.php?sid=',$sid,'" method="post"><input type="hidden" name="sent" value="1" />Den Warpkern um <input type="text" name="wamount" size="3" /> aufladen!<br /><input type="submit" value="aufladen" /></form>';
echo '<br /><a href="schiffe.php?sid=',$sid,'">zur&uuml;ck zum Schiff</a>';
}
}
include("foot.php");
