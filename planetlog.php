<?php
$inhalt=array("rohstoffa","rohstoffb","rohstoffc","rohstoffd","isochips","tritanium","dili","antimaterie","deuterium");
include_once("connect.php");
$result=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='m' AND besitzer!=2");
while($row=mysqli_fetch_array($result))
{
$pid=$row["id"];
$valuestring="";
$firststring="";
$schiff=new schiff($row["id"]);

//echo 'TESTECHO: ',$schiff->$inhalt[$i],' !!! ',$inhalt[$i],'<br />';

for($i=0;$i<count($inhalt);$i++) {
if($i!=count($inhalt)-1) $valuestring.='\''.$schiff->$inhalt[$i].'\',';
if($i==count($inhalt)-1) $valuestring.='\''.$schiff->$inhalt[$i].'\'';
}

for($i=0;$i<count($inhalt);$i++) {
if($i!=count($inhalt)-1) $firststring.=$inhalt[$i].',';
if($i==count($inhalt)-1) $firststring.=$inhalt[$i];
}
$datum=date("Y-m-d H:i:s");
mysqli_query($verbindung, "INSERT INTO planetenlog (was,datum,pid,$firststring) VALUES ('autolog','$datum','$pid',$valuestring)") or die(mysqli_error($verbindung));

}
?>