<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");

$id=$_SESSION["Id"];
$sid=$_GET["sid"];

//BETRAY
$betray=false;
if(!($id>0)) $betray=true;

if(ctype_digit($sid)) { 
$schiff=new schiff($sid); 
if($schiff->besitzer!=$_SESSION["Id"]) $betray=true;
if($schiff->klasse!='Horchposten') $betray=true;
} else $betray=true;

//ENDE

if($betray) echo 'Es ist ein Fehler aufgetreten...'; else {

echo '<h3>Bericht des Horchposten im Sektor ',$schiff->x,'/',$schiff->y,'</h3>';
echo '<table class="bordered2"><tr><td>Datum</td><td>Bild</td><td>Klasse</td><td>Besitzer</td><td>Sektor</td></tr>';
$result=mysql_query("SELECT * FROM horchlog WHERE ich='$sid' ORDER BY id DESC");
while($row=mysql_fetch_array($result))
{
echo '<tr><td>',gerdatum($row["datum"]),'</td><td><img src="',$row["img"],'" border="0" /></td><td>',$row["klasse"],'</td><td>',id2name($row["besitzer"]),'</td><td>',$row["x"],'/',$row["y"],'</td></tr>';
}
echo '</table>';
echo '<br /><a href="schiffe.php?sid=',$sid,'">zur&uuml;ck</a>';
}
include("foot.php"); 
?>