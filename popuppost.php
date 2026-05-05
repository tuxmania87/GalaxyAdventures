<?php
session_start();
include_once("connect.php");
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
$id=$_SESSION["Id"];
$count=0;
mysqli_query($verbindung, "UPDATE mail SET popup='0' WHERE empfaenger='$id'")or die(mysqli_error($verbindung));
$abfrage=mysqli_query($verbindung, "SELECT * FROM mail WHERE empfaenger='$id' AND neu='1' AND del='0'");
while($row=mysqli_fetch_array($abfrage))
	$count++;
echo 'Du hast ',$count,' <a href="mail.php"><span style="color:green;font-weight:bold;">neue Nachrichten</span></a>';
?>
</body>
</html>
