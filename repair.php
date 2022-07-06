<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

$id=$_SESSION["Id"];
$pid=$_GET["pid"];
$sid=$_POST["sid"];
if(!isset($_POST["sid"])) $sid=$_GET["sid"];

//Betray
$betray=false;
if(!ctype_digit($pid)) $betray=true;
if(!ctype_digit($sid) && $sid>0) $betray=true;

if($betray) echo "No Valid ID."; else 
{

$planet=new Planeten($pid);
if(isset($sid)) $schiff=new Schiffe($sid);
if($schiff->besitzer->id!=$_SESSION["Id"] || $planet->besitzer->id!=$_SESSION["Id"]) $betray=false;
if($betray) echo "Betray Failure."; else
{

if($_POST["sent"]==1) { 		//Schiff reparieren
$hamount=$_POST["hamount"];
if($hamount+$schiff->hull>$schiff->maxhull) $hamount=$schiff->maxhull-$schiff->hull; if($hamount<0) $hamount=0;
$error="";
if($planet->frachtraum->baustoff<$hamount*2) $error="Zu Wenig Baustoffe. Verlangt werden $hamount*2 , vorhanden sind nur ".$planet->frachtraum->baustoff." <br />";
if($planet->frachtraum->duranium<$hamount) $error="Zu Wenig Duranium. Verlangt werden $hamount , vorhanden sind nur ".$planet->frachtraum->duranium." <br />";
if($error=="") {
$planet->frachtraum->baustoff-=($hamount*2);
$planet->frachtraum->duranium-=$hamount;
$schiff->hull+=$hamount;
mysql_query("UPDATE schiffe SET hull='".$schiff->hull."' WHERE id='".$schiff->id."'");
$planet->frachtraum->save();
echo "Schiff wurde repariert!<br />";
} else echo $error;
}

if(!isset($sid)) { //schiff waehlen
echo '<h3>Schiff ausw&auml;hlen</h3><form action="repair.php?pid=',$planet->id,'" method="post"><select name="sid">';
$abfrage=mysql_query("SELECT * FROM schiffe WHERE besitzer='$id' AND (x='".$planet->position->x."' AND y='".$planet->position->y."' AND system='".$planet->position->system->id."' AND typ='s' AND orbit=1)");
while($row=mysql_fetch_array($abfrage))
echo '<option value="',$row["id"],'">',$row["name"],' Klasse:', $row["klasse"],' Energie: ',$row["energie"],'/',$row["maxenergie"],'</option>';
echo '</select>';
echo '<input type="submit" value="ausw&auml;hlen..." /></form>';
} else {
//Menue

echo '<h3> Schiffsreperatur</h3><br />Um einne H&uuml;llenpunkt zu reparieren brauchst du 2 Baustoffe und 1 Duranium.<br /><br />';
echo 'Waren auf deinem Planeten:<br />';
echo '<img src="images/baustoff.png" border="0" />Baustoff: ',$planet->frachtraum->baustoff,'<br />';
echo '<img src="images/duranium.png" border="0" />Duranium: ',$planet->frachtraum->duranium,'<br />';
echo '<br />Zustand deines Schiffes: ',$schiff->hull,'/',$schiff->maxhull,'<br />';
echo '<br /><br /><form action="repair.php?pid=',$pid,'&sid=',$sid,'" method="post" /><input type="hidden" name="sent" value="1" />Schiff um <input type="text" name="hamount" size="3"> H&uuml;lle reparieren.<br /><input type="submit" value="reaprieren"></form>';

}
echo '<br /><a href="planet.php?pid=',$pid,'">zur&uuml;ck zum Planeten</a>';
}
}
include("foot.php");