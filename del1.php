<?php
include_once("connect.php");
$i=1;
for($y=10;$y<=200;$y+=10)
for($x=10;$x<=200;$x+=10)
{
$tmpname = "Station Nr. ".$i;
mysqli_query($verbindung, "INSERT INTO schiffe (name,typ,x,y,hull,maxhull,schilde,maxschilde,laser,besitzer) VALUES ('$tmpname','h','$x','$y','4000','4200','5000','5000','40','3')") or die(mysqli_error($verbindung));
$i++;
}
?>
