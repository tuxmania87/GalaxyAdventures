<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");

$self=$_SESSION["Id"];
$me=new konto(); $me->getData($self);

$inhalt=array("rohstoffa","rohstoffb","rohstoffc","rohstoffd","isochips","tritanium","dili","antimaterie","deuterium","npcborg","npcrom","npcfer","npcfod","npckling","npccard");
$inhaltcap=array("Baustoff","Duranium","Erz","Sorium","Isochips","Tritanium","Dilithium","Antimaterie","Deuterium","Vinkulum","Ale","Latinum","Château Picard","Blutwein","Taspar Eier");

//CHEATSCHUTZ
$betray=false;
if(!isset($_SESSION["Id"])) $betray=true;
if($betray) echo 'Es ist ein Fehler aufgetreten (System.Exception(1))'; else {
//b

if($_POST["bietbool"]==1 && ctype_digit($_POST["handelid"]) && ctype_digit($_POST["gebottext"])) { 	//bieten
$handelid=$_POST["handelid"];
$sebayresult=mysqli_query($verbindung, "SELECT * FROM sebay WHERE id='$handelid'");
while($ebay=mysqli_fetch_array($sebayresult)) {
$gebot=$ebay["gebot"];
$bieter=$ebay["bieter"];
$neuesgebot=$_POST["gebottext"];
if( ($neuesgebot>$gebot && $me->$ebay["rohstoff"]>=$neuesgebot) || ($bieter==$_SESSION["Id"] && $neuesgebot>$gebot && $gebot+$me->$ebay["rohstoff"]>=$neuesgebot)) {
$alterbieter=new konto(); $alterbieter->getData($ebay["bieter"]);
if($ebay["bieter"]==$_SESSION["Id"]) $me->$ebay["rohstoff"]+=$gebot; else $alterbieter->$ebay["rohstoff"]+=$gebot;
$me->$ebay["rohstoff"]-=$neuesgebot;
echo '!!! ',$me->$ebay["rohstoff"];
$me->setData($self); if($ebay["bieter"] != $_SESSION["Id"])  $alterbieter->setData($bieter);
mysqli_query($verbindung, "UPDATE sebay SET gebot='$neuesgebot',bieter='$self' WHERE id='$handelid'");
} else echo 'Du musst mindestens 1 mehr bieten als der Vorherige UND die entsprechende Ware auf deinem Warenkonto haben!<br />';
}

}

echo '<h3>Schiffsversteigerung</h3>Hier findest du Angebote auf die du bieten kannst, m&ouml;chtest du selber ein Schiff versteigern, scrolle bitte nach unten f&uuml;r weitere Informationen.<br /><br />';
echo '<table class="bordered"><tr><td>Bild</td><td>Klasse</td><td>Position</td><td>Verk&auml;ufer</td><td>Zahlungsmittel</td><td>Gebot</td><td>B</td><td>Ende</td></tr>';
$sebayresult=mysqli_query($verbindung, "SELECT * FROM sebay ORDER BY id DESC");
while($ebay=mysqli_fetch_array($sebayresult)) {
$schiff=new schiff(); $schiff->getData($ebay["sid"]);
echo '<tr><td><a href="sebay2.php?aid=',$ebay["id"],'" onmouseover="Tip(sebay)" onmouseout="UnTip()"><img src="',$schiff->img,'" border="0" /></a></td><td>',$schiff->klasse,'</td><td>',$schiff->x,'|',$schiff->y,'</td><td>',id2name($schiff->besitzer),'</td><td>',$inhaltcap[array_search($ebay["rohstoff"],$inhalt)],'</td><td>',$ebay["gebot"],' (',id2name($ebay["bieter"]),')</td><td><a href="sebay2.php?aid=',$ebay["id"],'">bieten</a></td><td>',gerdatum($ebay["ende"]),'</td></tr>';
}
echo '</table><br /><br /><h3> Angebot einstellen </h3>(noch nicht freigegeben)';
}
include("foot.php");
?>
