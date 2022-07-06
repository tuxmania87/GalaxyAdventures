<?php
include_once("connect.php");
for($i=0;$i<=5;$i++) {

$abfrage=mysql_query("SELECT * FROM schiffe WHERE besitzer=4 AND energie>0 AND typ='s' AND klasse='Drohne'");
while($s=mysql_fetch_array($abfrage))
{
$sid=$s["id"];
$schiff=new schiff($sid);


$wert=rand(0,3);
echo 'Schiff; ',$schiff->name,' (',$schiff->id,') bewegt sich von ',$schiff->x,'|',$schiff->y,' nach ';
if($wert==0 && $schiff->x>1) { $schiff->energie--; $schiff->x--; } 
if($wert==1 && $schiff->y>1) { $schiff->energie--; $schiff->y--; }
if($wert==2 && $schiff->x<100) { $schiff->energie--; $schiff->x++; }
if($wert==3 && $schiff->y<100) { $schiff->energie--; $schiff->y++; }
echo $schiff->x,'|',$schiff->y;
$schiff->setData();
$abfrage2=mysql_query("SELECT * FROM schiffe WHERE x='$schiff->x' AND y='$schiff->y' AND typ!='s'");
while($s2=mysql_fetch_array($abfrage2))
{
echo '---->',$s2["typ"],' ',id2name($s2["besitzer"]);
if($s2["typ"]=='m') { 
$empf=$s2["besitzer"];
$datum=date("Y-m-d H:i:s");

$check3=false;
$npclog=mysql_query("SELECT * FROM npclog WHERE besitzer='$empf'");
while($npc=mysql_fetch_array($npclog))
$check3=true;


if(!$check3) {
$betreff='Besuch bei '.$schiff->x.'|'.$schiff->y;
$inhalt="Spezies $empf \n\n im Gitter entdeckt. Scanne....  Keine Bedrohung f&uuml;r das Kollektiv. Bewege zu Gitte 3423-alpha.";
mysql_query("INSERT INTO mail (absender,empfaenger,betreff,inhalt,datum,neu) VALUES ('4','$empf','$betreff','$inhalt','$datum','1')"); }
}
$ziel=$s2["id"];
mysql_query("INSERT INTO npclog (datum,besitzer,sid,ziel) VALUES ('$datum','$empf','$schiff->id','$ziel')");


}
echo '<br />';
}
echo '<hr />';
}


?>
