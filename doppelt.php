<?php
include_once("connect.php");
$result=mysql_query("SELECT * FROM schiffe WHERE typ='m'");
while($row=mysql_fetch_array($result))
{
$id=$row["id"];
$x=$row["x"];
$y=$row["y"];
$result2=mysql_query("SELECT * FROM schiffe WHERE typ='m' AND id!='$id' AND x='$x' AND y='$y'");
while($row2=mysql_fetch_array($result2))
echo 'Planet: ',$x,'/',$y,'<br />';
}
?>

