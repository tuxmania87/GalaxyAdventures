<?php
include("klassen.php");
$abfrage=mysql_query("SELECT id FROM planeten WHERE besitzer!=2 AND besitzer > 0");
while($row=mysql_fetch_array($abfrage))
{
$faktor=1;
$tplanet=new Planeten($row[0]);
$energie=20;
$lager=500;

for($i=0;$i<=50;$i++){
$a=$tplanet->feld[$i]->was;
$b=$tplanet->feld[$i]->bauzeit;
$c=$tplanet->feld[$i]->untergrund;
$d=$tplanet->feld[$i]->hull;
$e=$tplanet->feld[$i]->aktiv;

if($b==0) { //alle behandlung die fertig werden müssen
if($a==2) { //lager
$lager+=(500*$faktor); $energie+=(20*$faktor); $b=0; }
if($a==7) { //wasserwerk
$energie+=20;  }
if($a==25) { //geo
$energie+=($faktor*30); }
if($a==3) { //solar
$energie+=($faktor*20); }
if($a==12) { //flager
$lager+=($faktor*200);  }
if($a==13) { //hitzekraftwerk
$energie+=($faktor*20); }

}
}
//echo "Justierung fuer Planeten[" . $row[0] . "] Besitzer: " . $tplanet->besitzer->nickname . "<br />Energie:".$energie."<br />Lager:".$lager."<br /><br />";
mysql_query("UPDATE planeten SET energie='$energie',maxenergie='$energie',lager='$lager' WHERE id='$tplanet->id'");
}