<?php
$inhalt=array("rohstoffa","rohstoffb","rohstoffc","rohstoffd","isochips","tritanium","dili","antimaterie","deuterium");
include_once("connect.php");
$result=mysql_query("SELECT * FROM schiffe WHERE typ='m' AND besitzer!=2");
while($row=mysql_fetch_array($result))
{
$pid=$row["id"];
$valuestring="";
$schiff=new schiff();
$schiff->getData($row["id"]);
for($i=0;$i<sizeof($inhalt);$i++) {
if($i!=sizeof($inhalt)-1) $valuestring.="'$inhalt[$i]',";
if($i==sizeof($inhalt)-1) $valuestring.="'$inhalt[$i]'";
}
$firststring="";
for($i=0;$i<sizeof($inhalt);$i++) {
if($i!=sizeof($inhalt)-1) $firststring.="$inhalt,";
if($i<sizeof($inhalt)-1) $firststring.=$inhalt;
}
mysql_query("INSERT INTO planetenlog (pid,$firststring) VALUES ('$pid',$valuestring)") or die(mysql_error());
}
?>