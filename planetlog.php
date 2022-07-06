<?php
$inhalt=array("rohstoffa","rohstoffb","rohstoffc","rohstoffd","isochips","tritanium","dili","antimaterie","deuterium");
include_once("connect.php");
$result=mysql_query("SELECT * FROM schiffe WHERE typ='m' AND besitzer!=2");
while($row=mysql_fetch_array($result))
{
$pid=$row["id"];
$valuestring="";
$firststring="";
$schiff=new schiff($row["id"]);

//echo 'TESTECHO: ',$schiff->$inhalt[$i],' !!! ',$inhalt[$i],'<br />';

for($i=0;$i<sizeof($inhalt);$i++) {
if($i!=sizeof($inhalt)-1) $valuestring.='\''.$schiff->$inhalt[$i].'\',';
if($i==sizeof($inhalt)-1) $valuestring.='\''.$schiff->$inhalt[$i].'\'';
}

for($i=0;$i<sizeof($inhalt);$i++) {
if($i!=sizeof($inhalt)-1) $firststring.=$inhalt[$i].',';
if($i==sizeof($inhalt)-1) $firststring.=$inhalt[$i];
}
$datum=date("Y-m-d H:i:s");
mysql_query("INSERT INTO planetenlog (was,datum,pid,$firststring) VALUES ('autolog','$datum','$pid',$valuestring)") or die(mysql_error());

}
?>