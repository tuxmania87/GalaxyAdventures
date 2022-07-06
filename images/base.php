<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");

$sid=$_POST["sid"];
if(!isset($sid)) $sid=$_GET["sid"];
$schiff=new schiffgeneral(); $schiff->getData($sid);

$accid=$_SESSION["Id"];
if($job==1) { 	//Schiff 3 bauen
if($schiff->rohstoffa>=600 && $schiff->rohstoffb>=250 && $schiff->rohstoffd>=10) {
$schiff->rohstoffa-=600;
$schiff->rohstoffb-=250;
$schiff->rohstoffd-=10;
$schiff->setData($schiff->id);
$schiff->getData($schiff->id);
$lastid=checkforlastid('schiffe')+1;
mysql_query("INSERT INTO schiffe (klasse,skillbase,id,rohstoffa,rohstoffb,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,energie,maxenergie,energieoutput,img,lager,orbit) VALUES ('Raumtation','1','$lastid','4','$fid','100','2000','2000','1000','1000','50','noname','$accid','green','$schiff->x','$schiff->y','1000','1000','100','images/siedlerbase.png','1500','$schiff->orbit')") or die(mysql_error());
mysql_query("INSERT INTO schiffsmodule (sid,a1,a2,a3,b1,b2) VALUES ('$lastid','-1','-1','-1','-1','-1')");
echo 'Schiff gebaut!';
$gebaut=true;
} else echo 'Nicht genug Rohstoffe!';
}

if($job==2) { 	//Schiff 3 bauen
mysql_query("INSERT INTO schiffe (typ,klasse,skillbase,rohstoffa,rohstoffb,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,energie,maxenergie,energieoutput,img,lager,orbit) VALUES ('s','Raumtation','1','100','100','100','20000','20000','10000','10000','500','noname','$accid','green','$schiff->x','$schiff->y','10000','10000','1000','images/klingbase.png','15000','$schiff->orbit')") or die(mysql_error());
echo 'Schiff gebaut!';
$gebaut=true;
}

if($job==3) { 	//Schiff 3 bauen
mysql_query("INSERT INTO schiffe (typ,klasse,skillbase,rohstoffa,rohstoffb,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,energie,maxenergie,energieoutput,img,lager,orbit) VALUES ('s','Raumtation','1','100','100','100','20000','20000','10000','10000','500','noname','$accid','green','$schiff->x','$schiff->y','10000','10000','1000','images/fodbase.png','15000','$schiff->orbit')") or die(mysql_error());
echo 'Schiff gebaut!';
$gebaut=true;
}

if($job==4) { 	//Schiff 3 bauen
mysql_query("INSERT INTO schiffe (typ,klasse,skillbase,rohstoffa,rohstoffb,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,energie,maxenergie,energieoutput,img,lager,orbit) VALUES ('s','Raumtation','1','100','100','100','20000','20000','10000','10000','500','noname','$accid','green','$schiff->x','$schiff->y','10000','10000','1000','images/rombase.png','15000','$schiff->orbit')") or die(mysql_error());
echo 'Schiff gebaut!';
$gebaut=true;
}

if($job==5) { 	//Schiff 3 bauen
mysql_query("INSERT INTO schiffe (typ,klasse,skillbase,rohstoffa,rohstoffb,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,energie,maxenergie,energieoutput,img,lager,orbit) VALUES ('s','Raumtation','1','100','100','100','20000','20000','10000','10000','500','noname','$accid','green','$schiff->x','$schiff->y','10000','10000','1000','images/klingbase.png','15000','$schiff->orbit')") or die(mysql_error());
echo 'Schiff gebaut!';
$gebaut=true;
}

if($job==6) { 	//Schiff 3 bauen
mysql_query("INSERT INTO schiffe (typ,klasse,skillbase,rohstoffa,rohstoffb,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,energie,maxenergie,energieoutput,img,lager,orbit) VALUES ('s','Raumtation','1','100','100','100','20000','20000','10000','10000','500','noname','$accid','green','$schiff->x','$schiff->y','10000','10000','1000','images/ferbase.png','15000','$schiff->orbit')") or die(mysql_error());
echo 'Schiff gebaut!';
$gebaut=true;
}

if($job==7) { 	//Schiff 3 bauen
mysql_query("INSERT INTO schiffe (typ,klasse,skillbase,rohstoffa,rohstoffb,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,energie,maxenergie,energieoutput,img,lager,orbit) VALUES ('s','Raumtation','1','100','100','100','20000','20000','10000','10000','500','noname','$accid','green','$schiff->x','$schiff->y','10000','10000','1000','images/cardbase.png','15000','$schiff->orbit')") or die(mysql_error());
echo 'Schiff gebaut!';
$gebaut=true;
}


echo '<h3>Raumstation bauen im Sektor ',$schiff->x,'|',$schiff->y,'</h3>';
echo $schiff->rohstoffa>0?'Baustoff: '.$schiff->rohstoffa.'<br />':'';
echo $schiff->rohstoffb>0?'Duranium: '.$schiff->rohstoffb.'<br />':'';
echo $schiff->rohstoffc>0?'Erz: '.$schiff->rohstoffc.'<br />':'';
echo $schiff->rohstoffd>0?'Sorium: '.$schiff->rohstoffd.'<br />':'';
echo $schiff->deuterium>0?'Deuterium: '.$schiff->deuterium.'<br />':'';
echo '<br /><table class="bordered"><tr><td>Schiffsname</td><td>Bild</td><td>Baukosten</td><td>Schilde</td><td>H&uuml;lle</td><td>Laserst&auml;rke</td><td>A-Module</td><td>B-Module</td><td>C-Module</td><td>Lagerraum</td><td>Bauzeit in Ticks</td><td>sonstiges</td><td></td></tr>';

if($_SESSION["Id"]>9) echo '<td>Station</td><td><img src="images/siedlerbase.png" border="0"></td><td>600 Baustoff<br />250 Duranium<br />10 Sorium</td><td>1000</td><td>2000</td><td>50</td><td>3</td><td>2</td><td>0</td><td>1500</td><td>4</td><td>optimale Verteidigungsstation</td><td><a href="base.php?sid=',$schiff->id,'&job=1">Basis bauen!</a></td></tr>';
if($_SESSION["Id"]==7 || $_SESSION["Id"]==1) echo '<td>Station</td><td><img src="images/klingbase.png" border="0"></td><td>-</td><td>10000</td><td>20000</td><td>500</td><td>0</td><td>0</td><td>0</td><td>15000</td><td>0</td><td>optimale Verteidigungsstation</td><td><a href="base.php?sid=',$schiff->id,'&job=2">Basis bauen!</a></td></tr>';
if($_SESSION["Id"]==5 || $_SESSION["Id"]==1) echo '<td>Station</td><td><img src="images/fodbase.png" border="0"></td><td>-</td><td>10000</td><td>20000</td><td>500</td><td>0</td><td>0</td><td>0</td><td>15000</td><td>0</td><td>optimale Verteidigungsstation</td><td><a href="base.php?sid=',$schiff->id,'&job=3">Basis bauen!</a></td></tr>';
if($_SESSION["Id"]==6 || $_SESSION["Id"]==1) echo '<td>Station</td><td><img src="images/rombase.png" border="0"></td><td>-</td><td>10000</td><td>20000</td><td>500</td><td>0</td><td>0</td><td>0</td><td>15000</td><td>0</td><td>optimale Verteidigungsstation</td><td><a href="base.php?sid=',$schiff->id,'&job=4">Basis bauen!</a></td></tr>';
if($_SESSION["Id"]==3 || $_SESSION["Id"]==1) echo '<td>Station</td><td><img src="images/ferbase.png" border="0"></td><td>-</td><td>10000</td><td>20000</td><td>500</td><td>0</td><td>0</td><td>0</td><td>15000</td><td>0</td><td>optimale Verteidigungsstation</td><td><a href="base.php?sid=',$schiff->id,'&job=6">Basis bauen!</a></td></tr>';
if($_SESSION["Id"]==8 || $_SESSION["Id"]==1) echo '<td>Station</td><td><img src="images/cardbase.png" border="0"></td><td>-</td><td>10000</td><td>20000</td><td>500</td><td>0</td><td>0</td><td>0</td><td>15000</td><td>0</td><td>optimale Verteidigungsstation</td><td><a href="base.php?sid=',$schiff->id,'&job=7">Basis bauen!</a></td></tr>';


echo '</table>';
include("foot.php");
?>
