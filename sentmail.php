<?php
include("head.php");
include("navlogged.php");
include("klassen.php");
//Mailverwaltung

$verbindung = get_verbindung();

$id=$_SESSION["Id"];				//später cookie regelung!
$delpost=$_POST["delpost"];
if($delpost!='') {
mysqli_query($verbindung,"UPDATE mail SET sdel=1 WHERE id='$delpost' AND absender='$id'") or die(mysql_error());
}
echo '<h2>gesendete Nachrichten</h2><br /><table border="1">';
echo '<tr><td>gesendet am</td><td>Empf&auml;nger</td><td>Betreff (klicken um Nachricht zu lesen)</td><td>l&ouml;schen</td></tr>';
$neuPostZaehler=0;
$postquery=mysqli_query($verbindung,"SELECT * FROM mail WHERE absender='$id' AND sdel=0 ORDER BY datum DESC"); //id einsetzen
while($post=mysqli_fetch_array($postquery)) 			//Abfrage der accountdaten
	{
	$usr=new Account($post["empfaenger"]);
	echo '<tr><td>',gerdatum($post["datum"]),'</td><td>',$usr->nickname;
	echo '</td><td><a class="general" href="viewmail.php?brief=',$post["id"],'">',$post["betreff"],'</a></td>';
	echo '<form action="sentmail.php" method="post"><input type="hidden" name="delpost" value="',$post["id"],'"><td><input type="submit" value="l&ouml;schen" /></td></form></tr>';
	}
echo '</table>';
include("foot.php");
?>
