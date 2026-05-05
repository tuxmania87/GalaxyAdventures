<?php
$inhalt=array("rohstoffa","rohstoffb","rohstoffc","rohstoffd","isochips","tritanium","dili","antimaterie","deuterium");
include_once("connect.php");
$result=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='m' AND besitzer!=2");
while($row=mysqli_fetch_array($result))
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
mysqli_query($verbindung, "INSERT INTO planetenlog (pid,$firststring) VALUES ('$pid',$valuestring)") or die(mysqli_error($verbindung));
}
?>