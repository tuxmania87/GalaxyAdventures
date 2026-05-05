<?php
include_once("connect.php");

$abfrage=mysqli_query($verbindung, "SELECT * FROM account WHERE id<313");
while($t=mysqli_fetch_array($abfrage))
{ 
$email=$t["email"];
$name=$t["name"];
$message="Hallo $name\n\nHier ist das GA-Team. Ich weise dich hiermit darauf hin, dass sich die domain geaendert hat.\nSie lautet nun " . (defined('GA_BASE_URL') ? GA_BASE_URL : '.') . "/
\n\n MfG admin"; 
mail($email, "Domainaenderung GA", $message,"From: GA-TEAM <" . (defined('GA_MAIL_FROM') ? GA_MAIL_FROM : 'noreply@example.com') . ">");
}

?>
