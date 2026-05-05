<?php
include_once("connect.php");
$result=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='m'");
while($row=mysqli_fetch_array($result))
{
$id=$row["id"];
$x=$row["x"];
$y=$row["y"];
$result2=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='m' AND id!='$id' AND x='$x' AND y='$y'");
while($row2=mysqli_fetch_array($result2))
echo 'Planet: ',$x,'/',$y,'<br />';
}
?>

