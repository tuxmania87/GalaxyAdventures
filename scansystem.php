<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

$schiffid=$_GET["sid"];
$systemid=$_GET["id"];
if(!ctype_digit($systemid)) die("Fehler: ID Konflikt!");

$system=new System($systemid);

$check=false;
$abfrage=mysql_query("SELECT * FROM schiffe WHERE system=0 AND x='".$system->x."' AND y='".$system->y."' AND besitzer='".$_SESSION["Id"]."'");
while($row=mysql_fetch_array($abfrage))
$check=true;

if(!$check) die("FEHLER");

//QUestabfrage

$abfrage=mysql_query("SELECT erfolge.id FROM erfolge,quests WHERE erfolge.qid=quests.id AND erfolge.uid='".$_SESSION["Id"]."' AND erfolge.erledigt=0 AND quests.zusatz='$systemid'");
while($row=mysql_fetch_assoc($abfrage))
{
mysql_query("UPDATE erfolge SET erledigt=1 WHERE erledigt=0 AND id='".$row["id"]."'") or die(mysql_error());
echo "<a href=\"showquest.php\">Quest erledigt!</a><br />";
}

//SCAN..
$count=0; $planet=array();
$abfrage=mysql_query("SELECT * FROM planeten WHERE system='$systemid' AND besitzer!=2 AND besitzer!=''");
while($row=mysql_fetch_array($abfrage))
{
$count++; $planet[]=$row["besitzer"];
}
echo '<h3>Scan vom ',$system->name,'-System</h3>';
echo '<br />besiedelte Planeten: ',$count,'<br />Gefundene Spezies: ';
for($i=0;$i<sizeof($planet);$i++) { $x=new Account($planet[$i]); echo $x->nickname.","; }

$count=0; $schiff=array();
$abfrage=mysql_query("SELECT * FROM schiffe WHERE system='$systemid' AND besitzer!=2 AND besitzer!=''");
while($row=mysql_fetch_array($abfrage))
{
$count++; $schiff[]=$row["besitzer"];
}


echo '<br /><br />gefunden Schiffe: ',$count,'<br />Schiffssignaturen: ';
for($i=0;$i<sizeof($schiff);$i++) { $x=new Account($schiff[$i]); echo $x->nickname.","; }
echo '<br /><br /><a href="schiffe.php?sid=',$schiffid,'">zur&uuml;ck</a>';
?>
