<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");

$inhalt=array("rohstoffa","rohstoffb","rohstoffc","rohstoffd","isochips","tritanium","dili","antimaterie","deuterium","npcborg","npcrom","npcfer","npcfod","npckling","npccard");
$inhaltcap=array("Baustoff","Duranium","Erz","Sorium","Isochips","Tritanium","Dilithium","Antimaterie","Deuterium","Vinkulum","Ale","Latinum","Château Picard","Blutwein","Taspar Eier");

$aid=$_GET["aid"];
include_once 'auth.php';
$userId = requireLogin();
$aid = requireIntParam('aid');
{
//


echo '<h3>Schiffsversteigerung</h3><br />';
echo '<table class="bordered"><tr><td>Bild</td><td>Klasse</td><td>Position</td><td>Verk&auml;ufer</td><td>Zahlungsmittel</td><td>Gebot</td><td>B</td><td>Ende</td></tr>';
$sebayresult=mysqli_query($verbindung, "SELECT * FROM sebay WHERE id='$aid'");
while($ebay=mysqli_fetch_array($sebayresult)) {
$schiff=new schiff($ebay["sid"]); 
echo '<tr><td><img src="',$schiff->img,'" border="0" /></td><td>',$schiff->klasse,'</td><td>',$schiff->x,'|',$schiff->y,'</td><td>',id2name($schiff->besitzer),'</td><td>',$inhaltcap[array_search($ebay["rohstoff"],$inhalt)],'</td><td>',$ebay["gebot"],' (',id2name($ebay["bieter"]),')</td><td><a href="sebay.php?aid=',$ebay["id"],'">bieten</a></td><td>',gerdatum($ebay["ende"]),'</td></tr>';

echo '</table><br />';
echo '<table class="bordered2">';
echo '<tr><td>Schilde</td><td>',$schiff->schilde,'/',$schiff->maxschilde,'</td></tr>';
echo '<tr><td>H&uuml;lle</td><td>',$schiff->hull,'/',$schiff->maxhull,'</td></tr>';
echo '<tr><td>Gondelerhitzung</td><td>',$schiff->maxgondeln,'</td></tr>';
echo '<tr><td>Phasererhitzung</td><td>',$schiff->maxphaser,'</td></tr>';
echo '<tr><td>Phaserst&auml;rke</td><td>',$schiff->laser,'</td></tr>';
echo '<tr><td>Energie</td><td>',$schiff->energie,'/',$schiff->maxenergie,' (+',$schiff->energieoutput,')</td></tr>';
echo '<tr><td>Lagerraum</td><td>',$schiff->lager,'</td></tr>';
echo '<tr><td>Sensorreichweite</td><td>',2+($schiff->lrs),'</td></tr>';
echo '<tr><td>Warpkern</td><td>',$schiff->maxwarpkern>0?'ja ('.$schiff->maxwarpkern.')':'nein','</td></tr>';
echo '<tr><td>Tarnung</td><td>',$schiff->skilltarnung==1?'ja':'nein','</td></tr>';
echo '<tr><td>Transwarp</td><td>',$schiff->skilltranswarp==1?'ja':'nein','</td></tr>';
echo '<tr><td>Deuteriumsauger / Erzsauger</td><td>',$schiff->skilldeut==0?'nein':'ja',' / ',$schiff->skillerz==0?'nein':'ja','</td></tr>';
echo '</table><br />';
echo '<h3>Bieten</h3><form action="sebay.php" method="post">';
echo 'Biete mindestens ',$ebay["gebot"]+1,' ',$inhaltcap[array_search($ebay["rohstoff"],$inhalt)],'<br />';
echo '<input type="text" name="gebottext" size="3" />  ',$inhaltcap[array_search($ebay["rohstoff"],$inhalt)],'<br />';
echo '<input type="hidden" name="bietbool" value="1" /><input type="hidden" name="handelid" value="',$ebay["id"],'" /><input type="submit" value="bieten!" /></form>';

}
}
include("foot.php");
?>
