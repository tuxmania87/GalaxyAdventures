<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");


echo '<table class="bordered">';
$abfrage=mysql_query("SELECT * FROM iplog");
while($liste=mysql_fetch_array($abfrage)) {
$ip=$liste["ip"];
$id=$liste["id"];
$abfrage2=mysql_query("SELECT * FROM iplog WHERE ip='$ip' AND id!='$id'");
while($liste2=mysql_fetch_array($abfrage2)) {


echo '<tr><td>',id2name($liste["besitzer"]),'</td><td>',id2name($liste2["besitzer"]),'</td><td>',gerdatum($liste["datum"]),'<br />',gerdatum($liste2["datum"]),'</td><td><b>',$liste["ip"],'</b></td></tr>';




}
}
echo '</table>';
include("foot.php");
?>
