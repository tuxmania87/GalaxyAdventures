<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

$sid=$_GET["sid"];
$tid=$_GET["tid"];

$schiff=new Schiffe($sid);
$target=new Planeten($tid);

$oflaeche = $target->typ[1]=='m' && strlen($target->typ)==2?24:50;

$stunde=date("H");
//CHEATSCHUTZ ANFANG


$betray=false;
if(!ctype_digit($_GET["sid"])) $betray=true;
if(!ctype_digit($_GET["tid"])) $betray=true;
if($schiff->typ!='s') $betray=true;
if($schiff->besitzer->id!=$_SESSION["Id"]) $betray=true;
if($betray) { echo 'Du bist nicht <a href="login.php">eingeloggt oder du versucht auf fremde Accounts zuzugreifen...'; } else {

//CHEATSCHUTZ ENDE

//QUESTABFRAGE

$abfrage=mysql_query("SELECT E.id FROM erfolge E,quests Q WHERE E.qid=Q.id AND Q.typ=9 AND E.erledigt=0 AND Q.zusatz='".$target->typ."'");
while($row=mysql_fetch_array($abfrage))
	{
	$qst = new Quest($row[0]);
	$qst->plus();
	$qst->done();
	echo 'Du hast einen (Teil)-Erfolg fuer eine Quest erreicht!<br />';
	}





//ENDE QUESTS

if($target->besitzer->id==3) { $npcware='Latinum'; $npcname='Handelsturm'; $npcbild='npcferg'; }
//if($target->besitzer->id==4) { $npcware='Vinkulum'; $npcname='Handelsturm'; $npcbild='npcferg'; }
if($target->besitzer->id==5) { $npcware='Chateau Picard'; $npcname='F&ouml;derationsrat'; $npcbild='npcfod'; }
if($target->besitzer->id==6) { $npcware='Ale'; $npcname='Imperialer Senat'; $npcbild='npcrom'; }
if($target->besitzer->id==7) { $npcware='Blutwein'; $npcname='Grosse Halle'; $npcbild='npckling'; }
if($target->besitzer->id==8) { $npcware='Taspar Eier'; $npcname='Detapa-Rat'; $npcbild='npccard'; }



if($schiff->position->x==$target->position->x && $schiff->position->y==$target->position->y && $schiff->position->orbit==1 && $schiff->position->system->id==$target->position->system->id) {
echo '<table class="bordered">';

for($i=1;$i<=$oflaeche;$i++) 
{
if(($i==1 || $i==21 || $i==31 || $i==41 || $i==11) && $oflaeche==50) echo '<tr>';
if(($i==1 || $i==7 || $i==13 || $i==19) && $oflaeche==24) echo '<tr>';
if($target->feld[$i]->was==0) {
switch($target->feld[$i]->untergrund) {
case "f": { $bild=$stunde>=19||$stunde<=7?"waldn.png":"forest.png"; $mouse="Wald"; break; }
case "g": { $bild=$stunde>=19||$stunde<=7?"wiesen.png":"grass.png"; $mouse="Wiese"; break; }
case "w": { $bild=$stunde>=19||$stunde<=7?"wassern.png":"water.png"; $mouse="Wasser"; break; }
case "m": { $bild=$stunde>=19||$stunde<=7?"gebirgen.png":"mountain.png"; $mouse="Gebirge"; break; }
case "d": { $bild=$stunde>=19||$stunde<=7?"wuesten.png":"desert.png"; $mouse="W&uuml;ste"; break; }
case "i": { $bild=$stunde>=19||$stunde<=7?"eisn.png":"ice.png"; $mouse="Eis"; break; }
case "l": { $bild=$stunde>=19||$stunde<=7?"lavan.png":"lava.png"; $mouse="Lava"; break; }
case "v": { $bild=$stunde>=19||$stunde<=7?"vulkann.png":"vulkan.png"; $mouse="Vulkan"; break; }
case "s": { $bild=$stunde>=19||$stunde<=7?"gesteinn.png":"gestein.png"; $mouse="Lavagestein"; break; }
case "im": { $bild=$stunde>=19||$stunde<=7?"eisbergn.png":"icem.png"; $mouse="Eisberg"; break; }
case "fl": { $bild=$stunde>=19||$stunde<=7?"packeisn.png":"frozen.png"; $mouse="gefrorener See"; break; }
case "dm": { $bild=$stunde>=19||$stunde<=7?"wuestengebirgen.png":"dmountain.png"; $mouse="W&uuml;stengebirge"; break; }
case "is": { $bild=$stunde>=19||$stunde<=7?"spalten.png":"ispalte.png"; $mouse="Kernspalte"; break; }
}
echo '<td><img src="images/buildings/',$bild,'" border="0" onmouseover="Tip(\'<b>',$mouse,'</b>\')" onmouseout="UnTip()" /></td>';
}
if($target->feld[$i]->was>0) {
$url="destroy.php";
switch ($target->feld[$i]->was) {
case 1: { $bild=$stunde>=19||$stunde<=7?"baustofffabrikn.png":"bm.gif"; $mouse="baustoff"; break; }
case 2: { 
	if($target->feld[$i]->untergrund=='g') $bild=$stunde>=19||$stunde<=7?"lagerwiesen.png":"lager.gif"; 
	if($target->feld[$i]->untergrund=='d') $bild=$stunde>=19||$stunde<=7?"lagerwuesten.png":"lagerwueste.png"; 
	if($target->feld[$i]->untergrund=='i') $bild=$stunde>=19||$stunde<=7?"lagereisn.png":"lagereis.png"; 
	$mouse="lager"; break; }
case 3: { $bild=$stunde>=19||$stunde<=7?"solarkomplexn.png":"solarkomplex.png"; $mouse="solar"; break; }
case 4: { $url="createship.php"; $bild="werft.gif"; $mouse="werft"; break; }
case 5: { 
	if($target->feld[$i]->untergrund=='i') $bild=$stunde>=19||$stunde<=7?"kanoneeisn.png":"plasma.gif"; 
	if($target->feld[$i]->untergrund=='d') $bild=$stunde>=19||$stunde<=7?"kanonewuesten.png":"kanonewueste.png"; 
	$mouse="plasma"; break; }
case 6: { 
	$url="schilde.php";
	if($target->feld[$i]->untergrund=='i') $bild=$stunde>=19||$stunde<=7?"schildturmeisn.png":"schild.gif";
	if($target->feld[$i]->untergrund=='d') $bild=$stunde>=19||$stunde<=7?"schildturmwuesten.png":"schildturmwueste.png";
	$mouse="schilde"; break; }
case 7: { 
	$bild=$stunde>=19||$stunde<=7?"turbinenn.png":"turbinen.png";
	$mouse="wasser"; break; }
case 8: { 
	if($target->feld[$i]->untergrund=='m') $bild=$stunde>=19||$stunde<=7?"minegebirgen.png":"mine.gif"; 
	if($target->feld[$i]->untergrund=='dm') $bild=$stunde>=19||$stunde<=7?"minenwuestengebirgen.png":"minewuestengebirge.png"; 
	$mouse="mine"; break; }
case 9: { 
	if($target->feld[$i]->untergrund=='i') $bild=$stunde>=19||$stunde<=7?"duraniumeisn.png":"dura.gif"; 
	if($target->feld[$i]->untergrund=='d') $bild=$stunde>=19||$stunde<=7?"duraniumwuesten.png":"duraniumwueste.png"; 
	$mouse="duranium"; break; }
case 10: { 
	$bild=$stunde>=19||$stunde<=7?"forschungskomplexn.png":"forschungskomplex.png";
	$mouse="forschung"; break; }
case 12: { 
	$bild=$stunde>=19||$stunde<=7?"lagersystemgebirgen.png":"lagersystemgebirge.png";
	$mouse="lagerhoehle"; break; }
case 13: { 
	$bild=$stunde>=19||$stunde<=7?"hitzekraftwerkn.png":"hitzekraftwerk.png";
	$mouse="lavawerk"; break; }
case 14: { 
	$bild=$stunde>=19||$stunde<=7?"soriumvulkann.png":"soriumvulkan.png";
	$mouse="sorium"; break; }
case 16: { 
	$bild=$stunde>=19||$stunde<=7?"schildturmgesteinn.png":"schildturmgestein.png";
	$url="schilde.php";
	$mouse="schilde"; break; }
case 17: { 
	$bild=$stunde>=19||$stunde<=7?"kanonelavan.png":"kanonelava.png";
	$mouse="plasma"; break; }
case 21: { 
	if($target->feld[$i]->untergrund=='g') $bild=$stunde>=19||$stunde<=7?"beschleunigerwiesen.png":"teilchen.png";
	if($target->feld[$i]->untergrund=='i') $bild=$stunde>=19||$stunde<=7?"beschleunigereisn.png":"beschleunigereis.png";
	if($target->feld[$i]->untergrund=='d') $bild=$stunde>=19||$stunde<=7?"beschleunigerwuesten.png":"beschleunigerwueste.png";
	$mouse="antimaterie"; break; }
case 22: { 
	$bild=$stunde>=19||$stunde<=7?"tritaniumwuesten.png":"tritanium.png";
	$mouse="tritanium"; break; }
case 23: { 
	if($target->feld[$i]->untergrund=='m') $bild=$stunde>=19||$stunde<=7?"diliminegebirgen.png":"dilimine.png";
	if($target->feld[$i]->untergrund=='im') $bild=$stunde>=19||$stunde<=7?"dilimineeisn.png":"dilimineeis.png";
	$mouse="dilimine"; break; }
case 24: { 
	if($target->feld[$i]->untergrund=='g') $bild=$stunde>=19||$stunde<=7?"fusionn.png":"fusion.png";
	if($target->feld[$i]->untergrund=='i') $bild=$stunde>=19||$stunde<=7?"fusioneisn.png":"fusioneis.png";
	$mouse="fusion"; break; }
case 25: { 
	$bild=$stunde>=19||$stunde<=7?"geokraftwerkn.png":"geokraftwerk.png";
	$mouse="geo"; break; }
case 26: { 
	if($target->feld[$i]->untergrund=='g') $bild=$stunde>=19||$stunde<=7?"doppeln.png":"doppel.png";
	if($target->feld[$i]->untergrund=='i') $bild=$stunde>=19||$stunde<=7?"doppeleisn.png":"doppeleis.png";
	if($target->feld[$i]->untergrund=='d') $bild=$stunde>=19||$stunde<=7?"doppelwuesten.png":"doppelwueste.png";
	$mouse="doppel"; break; }
case 27: { 
	if($target->feld[$i]->untergrund=='g') $bild=$stunde>=19||$stunde<=7?"torpedowiesen.png":"torpedofabrik.png";
	if($target->feld[$i]->untergrund=='i') $bild=$stunde>=19||$stunde<=7?"torpedoeisn.png":"torpedofabrikeis.png";
	if($target->feld[$i]->untergrund=='d') $bild=$stunde>=19||$stunde<=7?"torpedowuesten.png":"torpedofabrikwueste.png";
	if($target->feld[$i]->untergrund=='s') $bild=$stunde>=19||$stunde<=7?"torpedogesteinn.png":"torpedofabrikgestein.png";
	$mouse="torpedo"; break; }
case 28: { 
	if($target->feld[$i]->untergrund=='g') $bild=$stunde>=19||$stunde<=7?$npcbild."n.png":$npcbild.".png";
	$mouse="npc"; break; }
case 30: { 
	if($target->feld[$i]->untergrund=='g') $bild=$stunde>=19||$stunde<=7?"zentralen.png":"zentrale.png";
	if($target->feld[$i]->untergrund!='g') $bild=$stunde>=19||$stunde<=7?"basisn.png":"basis.png";
	$mouse=$target->feld[$i]->untergrund=='g'?"zentrale":"basis"; break; }	


}
if($target->feld[$i]->bauzeit==0) echo '<td><img src="images/buildings/',$bild,'" border="0" onmouseover="Tip(',$target->feld[$i]->aktiv>0?$mouse.'online':$mouse.'offline',')" onmouseout="UnTip()" /></td>';
if($target->feld[$i]->bauzeit>0) echo '<td><img src="images/buildings/',$bild,'" border="0" onmouseover="Tip(\'',$mouse,' | Fertig in ',$target->feld[$i]->bauzeit,' Ticks \')" onmouseout="UnTip()" /></td>';
}

if($i%10==0 && $oflaeche==50) echo '</tr>';
if($i%6==0 && $oflaeche==24) echo '</tr>';
}
echo '</table><hr />';
echo '<br /><table class="bordered2">';
$inhalt=array("rohstoffa","rohstoffb","rohstoffc","rohstoffd","isochips","tritanium","dili","antimaterie","deuterium","npcborg","npcrom","npcfer","npcfod","npckling","npccard");
$inhaltcap=array("Baustoff","Duranium","Erz","Sorium","Isochips","Tritanium","Dilithium","Antimaterie","Deuterium","Vinkulum","Ale","Latinum","Château Picard","Blutwein","Taspar Eier");
$inhaltimg=array("baustoff.png","duranium.png","erz.png","sorium.png","isochips.png","tritanium.png","dili.png","antimaterie.png","deuterium.png","vinkulum.png","ale.png","latinum.png","chateau.png","blutwein.png","eier.png");
for($i=0;$i<sizeof($inhalt);$i++) 
if($target->$inhalt[$i]>0) echo '<tr><td>',$inhaltcap[$i],'</td><td><img src="images/misc/',$inhaltimg[$i],'" border="0" /></td><td>',$target->$inhalt[$i],'</td></tr>';
echo '</table>';
echo '<br /><br />';
$but = new Button("schiffe.php?sid=".$sid,"zur&uuml;ck zum Schiff");
$but->printme();
}

}
include("foot.php");
?>
