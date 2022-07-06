<?php
include_once("connect.php");
for($y=100;$y<=300;$y=$y+10)
for($x=0;$x<=200;$x=$x+10)
{
mysql_query("INSERT INTO schiffe (besitzer,typ,name,x,y,maxhull,hull,schilde,maxschilde,alarmstufe) VALUES ('3','h','Handelsstation','$x','$y','100','100','200','200','green')");
}
?>






