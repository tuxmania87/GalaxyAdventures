<?php
include_once("connect.php");

$abfrage=mysql_query("SELECT * FROM account WHERE id<313");
while($t=mysql_fetch_array($abfrage))
{ 
$email=$t["email"];
$name=$t["name"];
$message="Hallo $name\n\nHier ist das GA-Team. Ich weise dich hiermit darauf hin, dass sich die domain geaendert hat.\nSie lautet nun http://www.galaxy-adventures.net/\n\n MfG admin"; 
mail($email, "Domainaenderung GA", $message,"From: GA-TEAM <noreply@galaxy-adventures.net>");
}

?>
