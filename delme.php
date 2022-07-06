<?php
include_once("connect.php");

$abfrage=mysql_query("SELECT * FROM planet2");
while($row=mysql_fetch_array($abfrage)) {
$pid=$row["pid"];
for($i=1;$i<=50;$i++)
{
$tempfeld='feld'.$i;
$a=""; $b=""; $c="";
splitfeld($row[$tempfeld],$a,$b,$c,$d);
$neuevar= $a . "-" . $b . "-" . $c . "-60-1";
mysql_query("UPDATE planet2 SET $tempfeld='$neuevar' WHERE pid='$pid'");
}
}