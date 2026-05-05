<?php
include("head.php");
include("navlogged.php");
include("klassen.php");
$pid=$_GET["pid"];
$fid=$_GET["fid"];
$do=$_GET["do"];

$selfid=$_SESSION["Id"];
$res1=mysqli_query($verbindung, "SELECT mitglied FROM account WHERE id='$selfid'");
$row1=mysqli_fetch_array($res1);
$mitglied=$row1["mitglied"];

$ich=new Account($_SESSION["Id"]);

$ich->level=20;

if($ich->id==3) { $npcware='Latinum'; $npcname='Handelsturm'; $npcbild='npcferg'; }
//if($ich->id==4) { $npcware='Vinkulum'; $npcname='Handelsturm'; $npcbild='npcferg'; }
if($ich->id==5) { $npcware='Chateau Picard'; $npcname='F&ouml;derationsrat'; $npcbild='npcfod'; }
if($ich->id==6) { $npcware='Ale'; $npcname='Imperialer Senat'; $npcbild='npcrom'; }
if($ich->id==7) { $npcware='Blutwein'; $npcname='Grosse Halle'; $npcbild='npckling'; }
if($ich->id==8) { $npcware='Taspar Eier'; $npcname='Detapa-Rat'; $npcbild='npccard'; }

//CHEATSCHUTZ ANFANG


$betray=false;



//CHEATSCHUTZ ENDE




/*
$dilitest=false;
for($h=1;$h<=50;$h++) {
$tempt='feld'; $tempt.=$h;
splitfeld($feld->$tempt,$q,$w,$asd,$xcxyd,$e);
if($q=='23') $dilitest=true;
}
*/


//--------------
echo '<br /><span style="color:yellow;font-size:larger;">Auf Bilder klicken um Geb&auml;ude zu bauen!</span>';
echo '<table class="bordered"><tr><td>Display</td><td>Name</td><td>Kosten</td><td>Dauerkosten</td><td>Effekt</td><td>Dauereffekt</td><td>Bauzeiten</td></tr>';
//Waldfelder
if(true) {
 echo '<tr><td><a href="build.php?do=11&pid=',$pid,'&fid=',$fid,'"><img src="terra1.gif" border="0" title="Wald in Wiese" /></a></td><td>Terraforming I: Wald abbrennen</td><td>-200 Deuterium<br />-100 Energie</td><td>-</td><td>Aus Wald, Wiese machen</td><td>-</td><td>4 Tick</td></tr>';
}
if(true) {
echo '<tr><td><a href="build.php?do=1&pid=',$pid,'&fid=',$fid,'"><img src="bm.gif" border="0" title="Baustofffabrik" /></a></td><td>Baustofffabrik</td><td>-20 Baustoff<br />-5 Energie</td><td>-5 Energie</td><td>-</td><td>+5 Baustoff</td><td>2 Ticks</td></tr>';
echo '<tr><td><a href="build.php?do=2&pid=',$pid,'&fid=',$fid,'"><img src="lager.gif" border="0" title="Lager" /></a></td><td>Lager</td><td>-10 Baustoff<br />-10 Energie</td><td>-</td><td>+500 Lager<br />+20 E Speicher</td><td>-</td><td>1 Tick</td></tr>';
echo '<tr><td><a href="build.php?do=10&pid=',$pid,'&fid=',$fid,'"><img src="forschung.gif" border="0" title="Forschungsstation" /></a></td><td>Forschungsstation</td><td>-200 Baustoff<br />-150 Duranium<br />-50 Energie</td><td>-2 Antimaterie<br />-10 Baumaterial<br />-1 Dilithium<br />-15 Deuterium</td><td>Forschung</td><td>+1 ISO Chip</td><td>6 Tick</td></tr>';
echo '<tr><td><a href="build.php?do=24&pid=',$pid,'&fid=',$fid,'"><img src="fusion.png" border="0" title="Fusionsreaktor" /></a></td><td>Fusionsreaktor</td><td>-100 Baustoff<br />-40 Duranium<br />-30 Energie</td><td>-20 Deuterium</td><td>-</td><td>+20 Energie</td><td>2 Tick</td></tr>';
echo '<tr><td><a href="build.php?do=21&pid=',$pid,'&fid=',$fid,'"><img src="teilchen.png" border="0" title="Teilchenbeschleuniger" /></a></td><td>Teilchenbeschleuniger</td><td>-200 Baustoff<br />-150 Duranium<br />-50 Energie</td><td>-10 Energie</td><td>-</td><td>+1 Antimaterie</td><td>4 Tick</td></tr>';
echo '<tr><td><a href="build.php?do=26&pid=',$pid,'&fid=',$fid,'"><img src="doppel.png" border="0" title="Doppelbeschleuniger" /></a></td><td>Doppelbeschleuniger</td><td>-400 Baustoff<br />-300 Duranium<br />-80 Energie</td><td>-20 Energie</td><td>-</td><td>+2 Antimaterie</td><td>4 Tick</td></tr>';
if($ich->id<10) echo '<tr><td><a href="build.php?do=28&pid=',$pid,'&fid=',$fid,'"><img src="',$npcbild,'.png" border="0" title="',$npcname,'" /></a></td><td>',$npcname,'</td><td>-150 Baustoff<br />-50 Duranium<br />-20 Energie</td><td>-</td><td>-</td><td>+1 ',$npcware,'</td><td>2 Tick</td></tr>';
}
if(true) {
echo '<tr><td><a href="build.php?do=8&pid=',$pid,'&fid=',$fid,'"><img src="minewuestengebirge.png" border="0" title="Mine" /></a></td><td>Mine</td><td>-20 Baustoff<br />-10 Energie</td><td>-3 Energie</td><td>-</td><td>+8 Erz</td><td>1 Ticks</td></tr>';
echo '<tr><td><a href="build.php?do=22&pid=',$pid,'&fid=',$fid,'"><img src="tritanium.png" border="0" title="Tritaniumanlage" /></a></td><td>Tritaniumanlage</td><td>-250 Baustoff<br />-150 Duranium<br />-20 Energie</td><td>-10 Duranium<br />-12 Erz<br />-60 Energie</td><td>-</td><td>+1 Tritanium</td><td>4 Ticks</td></tr>';

}
if(true) {
echo '<tr><td><a href="build.php?do=3&pid=',$pid,'&fid=',$fid,'"><img src="solarkomplex.png" border="0" title="Solarstation" /></a></td><td>Solarstation</td><td>-20 Baustoff<br />-5 Energie</td><td>-</td><td>-</td><td>+15 Energie</td><td>2 Ticks</td></tr>';
if($ich->level>=2) echo '<tr><td><a href="build.php?do=9&pid=',$pid,'&fid=',$fid,'"><img src="duraniumwueste.png" border="0" title="Duraniumanlage" /></a></td><td>Duraniumanlage</td><td>-50 Baustoff<br />-10 Energie</td><td>8 Erz<br />6 Energie</td><td>-</td><td>+5 Duranium</td><td>5 Ticks</td></tr>';
if($ich->level>=4) echo '<tr><td><a href="build.php?do=5&pid=',$pid,'&fid=',$fid,'"><img src="kanonewueste.png" border="0" title="Plasmakannone" /></a></td><td>Plasmakanone</td><td>-130 Baustoff<br />-125 Duranium<br />-40 Energie</td><td>2 Energie Pro Schuss</td><td>6 schaden pro schuss</td><td>-</td><td>9 Ticks</td></tr>';
if($ich->level>=4) echo '<tr><td><a href="build.php?do=6&pid=',$pid,'&fid=',$fid,'"><img src="schildturmwueste.png" border="0" title="Schildturm" /></a></td><td>Schildturm</td><td>-130 Baustoff<br />-80 Duranium<br />-30 Energie</td><td>-</td><td>+80 Schilde</td><td>-</td><td>4 Ticks</td></tr>';
echo '<tr><td><a href="build.php?do=2&pid=',$pid,'&fid=',$fid,'"><img src="lagerwueste.png" border="0" title="Lager" /></a></td><td>Lager</td><td>-10 Baustoff<br />-10 Energie</td><td>-</td><td>+500 Lager<br />+20 E Speicher</td><td>-</td><td>1 Tick</td></tr>';
if($ich->level>=5) echo '<tr><td><a href="build.php?do=21&pid=',$pid,'&fid=',$fid,'"><img src="beschleunigerwueste.png" border="0" title="Teilchenbeschleuniger" /></a></td><td>Teilchenbeschleuniger</td><td>-200 Baustoff<br />-150 Duranium<br />-50 Energie</td><td>-10 Energie</td><td>-</td><td>+1 Antimaterie</td><td>4 Tick</td></tr>';
if($ich->level>=5) if($forschung->doppel==1) echo '<tr><td><a href="build.php?do=26&pid=',$pid,'&fid=',$fid,'"><img src="doppelwueste.png" border="0" title="Doppelbeschleuniger" /></a></td><td>Doppelbeschleuniger</td><td>-400 Baustoff<br />-300 Duranium<br />-80 Energie</td><td>-20 Energie</td><td>-</td><td>+2 Antimaterie</td><td>4 Tick</td></tr>';
if($forschung->torpedo1==2) echo '<tr><td><a href="build.php?do=27&pid=',$pid,'&fid=',$fid,'"><img src="torpedofabrikwueste.png" border="0" title="Torpedofabrik" /></a></td><td>Torpedofabrik</td><td>-250 Baustoff<br />-150 Duranium<br />-40 Energie</td><td>je nach Torpedoart</td><td>Torpedoproduktion</td><td>-</td><td>4 Tick</td></tr>';
}
if(true) {
if($ich->level>=2) echo '<tr><td><a href="build.php?do=9&pid=',$pid,'&fid=',$fid,'"><img src="dura.gif" border="0" title="Duraniumanlage" /></a></td><td>Duraniumanlage</td><td>-50 Baustoff<br />-10 Energie</td><td>8 Erz<br />6 Energie</td><td>-</td><td>+5 Duranium</td><td>3 Ticks</td></tr>';
if($ich->level>=3) echo '<tr><td><a href="build.php?do=4&pid=',$pid,'&fid=',$fid,'"><img src="werft.gif" border="0" title="Werft" /></a></td><td>Werft</td><td>-100 Baustoff<br />-50 Duranium<br />-20 Energie</td><td>-</td><td>-</td><td>Schiffsbau<br />Modulbau</td><td>5 Ticks</td></tr>';
if($ich->level>=4) echo '<tr><td><a href="build.php?do=5&pid=',$pid,'&fid=',$fid,'"><img src="plasma.gif" border="0" title="Plasmakannone" /></a></td><td>Plasmakanone</td><td>-130 Baustoff<br />-125 Duranium<br />-40 Energie</td><td>-</td><td>6 schaden pro schuss</td><td>-</td><td>4 Ticks</td></tr>';
if($ich->level>=4) echo '<tr><td><a href="build.php?do=6&pid=',$pid,'&fid=',$fid,'"><img src="schilde.gif" border="0" title="Schildturm" /></a></td><td>Schildturm</td><td>-130 Baustoff<br />-80 Duranium<br />-30 Energie</td><td>-</td><td>+80 Schilde</td><td>-</td><td>4 Ticks</td></tr>';
echo '<tr><td><a href="build.php?do=2&pid=',$pid,'&fid=',$fid,'"><img src="lagereis.png" border="0" title="Lager" /></a></td><td>Lager</td><td>-10 Baustoff<br />-10 Energie</td><td>-</td><td>+500 Lager<br />+20 E Speicher</td><td>-</td><td>1 Tick</td></tr>';
if($ich->level>=4) echo '<tr><td><a href="build.php?do=24&pid=',$pid,'&fid=',$fid,'"><img src="fusioneis.png" border="0" title="Fusionsreaktor" /></a></td><td>Fusionsreaktor</td><td>-100 Baustoff<br />-40 Duranium<br />-30 Energie</td><td>-20 Deuterium</td><td>-</td><td>+20 Energie</td><td>2 Tick</td></tr>';
if($ich->level>=5) echo '<tr><td><a href="build.php?do=21&pid=',$pid,'&fid=',$fid,'"><img src="beschleunigereis.png" border="0" title="Teilchenbeschleuniger" /></a></td><td>Teilchenbeschleuniger</td><td>-200 Baustoff<br />-150 Duranium<br />-50 Energie</td><td>-10 Energie</td><td>-</td><td>+1 Antimaterie</td><td>4 Tick</td></tr>';
if($ich->level>=5) if($forschung->doppel==1) echo '<tr><td><a href="build.php?do=26&pid=',$pid,'&fid=',$fid,'"><img src="doppeleis.png" border="0" title="Doppelbeschleuniger" /></a></td><td>Doppelbeschleuniger</td><td>-400 Baustoff<br />-300 Duranium<br />-80 Energie</td><td>-20 Energie</td><td>-</td><td>+2 Antimaterie</td><td>4 Tick</td></tr>';
if($forschung->torpedo1==2) echo '<tr><td><a href="build.php?do=27&pid=',$pid,'&fid=',$fid,'"><img src="torpedofabrikeis.png" border="0" title="Torpedofabrik" /></a></td><td>Torpedofabrik</td><td>-250 Baustoff<br />-150 Duranium<br />-40 Energie</td><td>je nach Torpedoart</td><td>Torpedoproduktion</td><td>-</td><td>4 Tick</td></tr>';
}

if(true) {
if($ich->level>=3) echo '<tr><td><a href="build.php?do=25&pid=',$pid,'&fid=',$fid,'"><img src="geokraftwerk.png" border="0" title="Geothermales Kraftwerk" /></a></td><td>geothermales Kraftwerk</td><td>-150 Baustoff<br />-110 Duranium<br />-30 Energie</td><td>-</td><td>+30 E Speicher</td><td>+40 Energie</td><td>3 Tick</td></tr>';
}

if(true)
if($ich->level>=3) echo '<tr><td><a href="build.php?do=7&pid=',$pid,'&fid=',$fid,'"><img src="wasserkraft.gif" border="0" title="Wasserkraftwerk" /></a></td><td>Wasserkraftwerk</td><td>-30 Baustoff<br />-10 Duranium<br />-10 Energie</td><td>-</td><td>-</td><td>+10 Energie</td><td>2 Ticks</td></tr>';

if(true) {
if($ich->level>=2) echo '<tr><td><a href="build.php?do=8&pid=',$pid,'&fid=',$fid,'"><img src="mine.gif" border="0" title="Mine" /></a></td><td>Mine</td><td>-20 Baustoff<br />-10 Energie</td><td>-3 Energie</td><td>-</td><td>+4 Erz</td><td>2 Ticks</td></tr>';
echo 'FOR ',$forschung->terra2;
if($forschung->terra2==1) echo '<tr><td><a href="build.php?do=15&pid=',$pid,'&fid=',$fid,'"><img src="terra2.gif" border="0" title="Berg in Wasser" /></a></td><td>Terraforming II: Berg sprengen</td><td>-200 Deuterium<br />-100 Energie</td><td>-</td><td>Aus Berg einen Wasserbecken erstellen</td><td>-</td><td>4 Tick</td></tr>';
if(!$dilitest && $ich->level>=4) echo '<tr><td><a href="build.php?do=23&pid=',$pid,'&fid=',$fid,'"><img src="dilimine.png" border="0" title="Dilithiummine" /></a></td><td>Dilitiummine</td><td>-100 Baustoff<br />100 Duranium<br />50 Energie</td><td>-50 Energie</td><td>-</td><td>+1 Dilitium</td><td>3 Ticks</td></tr>';
}
//LAVA
if(true) {
echo '<tr><td><a href="build.php?do=12&pid=',$pid,'&fid=',$fid,'"><img src="lagersystemgebirge.png" border="0" title="Lagerh&ouml;hle" /></a></td><td>Lagerh&ouml;hle</td><td>-10 Baustoff<br />-5 Duranium<br />-5 Energie</td><td>-</td><td>+200 Lagerraum</td><td>-</td><td>1 Ticks</td></tr>';
if($ich->level>=4) echo '<tr><td><a href="build.php?do=6&pid=',$pid,'&fid=',$fid,'"><img src="schildturmgestein.png" border="0" title="Schildturm" /></a></td><td>Schildturm</td><td>-130 Baustoff<br />-80 Duranium<br />-30 Energie</td><td>-</td><td>+80 Schilde</td><td>-</td><td>4 Ticks</td></tr>';
}
if(true) {
if($ich->level>=3) echo '<tr><td><a href="build.php?do=13&pid=',$pid,'&fid=',$fid,'"><img src="lavawerk.gif" border="0" title="Hitzekraftwerk" /></a></td><td>Hitzekraftwerk</td><td>-40 Baustoff<br />-20 Duranium<br />-10 Energie</td><td>-</td><td>+20 Energiespeicher</td><td>+10 Energie</td><td>3 Ticks</td></tr>';
if($ich->level>=4) echo '<tr><td><a href="build.php?do=5&pid=',$pid,'&fid=',$fid,'"><img src="kanonelava.png" border="0" title="Plasmakanone" /></a></td><td>Plasmakanone</td><td>-130 Baustoff<br />-125 Duranium<br />-40 Energie</td><td>-</td><td>+6 Laserst&auml;rke</td><td>-</td><td>4 Ticks</td></tr>';
}
if(true) {
if($ich->level>=3) echo '<tr><td><a href="build.php?do=14&pid=',$pid,'&fid=',$fid,'"><img src="sorium.gif" border="0" title="Soriumfabrik" /></a></td><td>Sorium</td><td>-300 Baustoff<br />-100 Duranium<br />-20 Energie</td><td>-10 Energie</td><td>-</td><td>+1 Sorium</td><td>4 Ticks</td></tr>';
}
if(true) {
if($ich->level>=2) echo '<tr><td><a href="build.php?do=20&pid=',$pid,'&fid=',$fid,'"><img src="water.png" border="0" title="Wasser" /></a></td><td>Wasser</td><td>1 Energie und Schiff im Orbit</td><td>-</td><td>-</td><td>Der Schuss schmilzt das Eis</td><td>0 Ticks</td></tr>';
}
if(true) {
if($ich->level>=4) echo '<tr><td><a href="build.php?do=23&pid=',$pid,'&fid=',$fid,'"><img src="dilimineeis.png" border="0" title="Dilithiummine" /></a></td><td>Dilitiummine</td><td>-100 Baustoff<br />100 Duranium<br />50 Energie</td><td>-50 Energie</td><td>-</td><td>+2 Dilitium</td><td>3 Ticks</td></tr>';
}







echo '</table><br /><a href="planet.php?pid=',$pid,'">zur&uuml;ck</a>';

include("foot.php");
?>
