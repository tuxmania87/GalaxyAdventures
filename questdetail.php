<?php
session_start();
include("klassen.php");
?>
<html>
<head>
<style type="text/css">

 /* Überschrift der 1. Ebene */
body {
 background-color:black;
 color:white;
}
</style>

</head>
<body>
<?php

if(!ctype_digit($_GET["id"])) die();

$id=$_GET["id"];

$quest=new Quest($id);
$x=new Account('5');
if($quest->geber>0) $schiff=new Schiffe($quest->geber);
echo 'Auftraggeber: ',$quest->geber>0?$schiff->besitzer->nickname:$x->nickname,'<br />';
echo 'Quest-Titel: ',$quest->name,'<br /><br />';
if($quest->erledigt>1) echo '<span style="font-weight:bold;text-decoration:underline;">Beschreibung: </span><br /><br />',nl2br($quest->abgabetext),'<br /><br />';
else					echo '<span style="font-weight:bold;text-decoration:underline;">Beschreibung: </span><br /><br />',nl2br($quest->text),'<br /><br />';
if($quest->typ==1) {
	$tar=new Account($quest->zusatz);
	echo '<font color="yellow">abgeschossene Schiffe von ',$tar->nickname,': ',$quest->anzahl,'/',$quest->max,'</font><br /><br />';
	
	}
if($quest->typ==2) {
$inhalt=array("baustoff","duranium","erz","sorium","isochips","tritanium","dili","antimaterie","deuterium","borg","romulaner","ferengi","foderation","klingonen","cardassianer");
$inhaltcap=array("Baustoff","Duranium","Erz","Sorium","Isochips","Tritanium","Dilithium","Antimaterie","Deuterium","Vinkulum","Ale","Latinum","Château Picard","Blutwein","Taspar Eier");
$inhaltimg=array("baustoff.png","duranium.png","erz.png","sorium.png","isochips.png","tritanium.png","dili.png","antimaterie.png","deuterium.png","vinkulum.png","ale.png","latinum.png","chateau.png","blutwein.png","eier.png");
	if($quest->abgeber>0) $schiff2=new Schiffe($quest->abgeber);
	echo 'wenn du ',$quest->max,'  <img src="images/',$inhaltimg[array_search($quest->zusatz,$inhalt)],'" border="0" /> ',$inhaltcap[array_search($quest->zusatz,$inhalt)],' gesammelt hast,';

	if($quest->abgeber>0) echo 'begib dich zum Questabgeber bei ',$schiff2->position->x,'|',$schiff2->position->y,' ins ',$schiff2->position->system->name,'-System (',$schiff2->position->system->x,'|',$schiff2->position->system->y;
	else echo ' kannst du die Quest <a href="quest.php?sid=0">abgeben</a><br />';
	}

if($quest->typ==3) {
	$tar=new System($quest->zusatz);
	echo $tar->name,'-System gescannt : ',$quest->erledigt,'/1<br /><br />';
	}
	
if($quest->typ==4) {
	echo '<font color="yellow">Geb&auml;ude errichtet? : ',$quest->erledigt==1?'ja':'nein','</font></a><br /><br />';
	}
	
if($quest->typ==6) {
$it=new Item($quest->item);
if($quest->erledigt==1) echo '<span style="color:silver;font-weight:bold;">'; 
	echo $it->name,' gesammelt: ',$quest->anzahl,'/',$quest->max;
if($quest->erledigt==1) echo ' (fertig)</span>';
echo "<br /><br />";
	}

	if($quest->typ==7) {
	if($quest->erledigt==1) echo '<span style="color:silver;font-weight:bold;">'; 
	echo $quest->zusatz,' gesammelt: ',$quest->anzahl,'/',$quest->max;
if($quest->erledigt==1) echo ' (fertig)</span>';
echo "<br /><br />";
	}

	if($quest->typ==9) {
	if($quest->erledigt==1) echo '<span style="color:silver;font-weight:bold;">'; 
	echo 'Objekt vom Typ: ',$quest->zusatz,' gescannt: ',$quest->anzahl,'/',$quest->max;
if($quest->erledigt==1) echo ' (fertig)</span>';
echo "<br /><br />";
	}

	
if($quest->erledigt==1 && $quest->abgeber>0 && $quest->qid != 31) {
			$schiff2=new Schiffe($quest->abgeber);
			if($schiff2->position->id>0) echo '<br />FERTIG, Quest bitte bei Schiff ',$schiff2->name,' (',$schiff2->id,') im Sektor ',$schiff2->position->x,'/',$schiff2->position->y,' abgeben im ',$schiff2->position->system->name,'-System (',$schiff2->position->system->x,'/',$schiff2->position->system->y,') abgeben!';
			if($schiff2->position->id==0) echo '<br />FERTIG, Quest bitte bei Schiff ',$schiff2->name,' (',$schiff2->id,') im Sektor ',$schiff2->position->x,'/',$schiff2->position->y,' im Weltraum abgeben!';	
				}
if($quest->erledigt==1 && $quest->abgeber==0) {
			echo '<br />FERTIG, <a href="quest.php?sid=0">Quest abgeben!</a>';
			}
?>
</body>
</html>