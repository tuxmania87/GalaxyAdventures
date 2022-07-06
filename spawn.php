<?php
include("klassen.php");
$klasse='Sondenzerst&ouml;rer';
$abfrage=mysql_query("SELECT * FROM account");
while($row=mysql_fetch_array($abfrage))
	{
	$check=false;
	$uid=$row["id"];
	$abfrage2=mysql_query("SELECT system,x,y FROM planeten WHERE besitzer='$uid'");
	while($row2=mysql_fetch_array($abfrage2))
	{
	$check=true;
	$x=$row2["x"];
	$y=$row2["y"];
	$system=$row2["system"];
	}
if(!$check) { $x=rand(1,200); $y=rand(1,200); $system=0; }
	mysql_query("INSERT INTO schiffe (schildstatus,baustoff,duranium,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT  '1','0','0','noname','$x','$y','$system','$uid',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'s',lager,'9',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'0' FROM bauplan WHERE klasse='$klasse'") or die(mysql_error());
}
?>	