<?php
include_once("connect.php");

$abfrage=mysqli_query($verbindung, "SELECT * FROM planet2");
while($row=mysqli_fetch_array($abfrage)) {
$pid=$row["pid"];
for($i=1;$i<=50;$i++)
{
$tempfeld='feld'.$i;
$a=""; $b=""; $c="";
splitfeld($row[$tempfeld],$a,$b,$c,$d);
$neuevar= $a . "-" . $b . "-" . $c . "-60-1";
mysqli_query($verbindung, "UPDATE planet2 SET $tempfeld='$neuevar' WHERE pid='$pid'");
}
}