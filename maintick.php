<?php
//Test auf Tick
// Verbindung via get_verbindung() aus connect.php
// DB-Selektion via get_verbindung()
$tm=mysqli_query($verbindung, "SELECT * FROM `ticklog` WHERE id=(SELECT max(id) FROM `ticklog`)") or die(mysqli_error($verbindung));
while($tm2=mysqli_fetch_array($tm)) {
if($tm2["status"]==0) { 
header ("Location: http://www.keinerspieltmitmir.de/devga/main.php");
exit;
}
}
// mysqli_close($verbindung); // war: mysql_close($db)
//endetest
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="de">
<head>
 <script type="text/javascript">

function frage(zahl) {
  if (zahl==1) return confirm("Willst du das Geb&auml;ude wirklich abreissen?");
  if (zahl==2) return confirm("Willst du den Bau wirklich abbrechen?");
  if (zahl==3) return confirm("Willst du deine Kolonie wirklich sprengen?");
  if (zahl==4) return confirm("Willst du dein Schiff wirklich zerst&ouml;ren?");
}
</script>
<script type="text/javascript" src="tooltip.js"></script>
  <title>Star Trek - Galaxy Adventures II</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<script type="text/javascript" src="wz_tooltip.js"></script>

<ul id="menu">
  <li class="button ubersicht"><span>&Uuml;bersicht</span></li>
  <li class="button kolonien"><span>Kolonien</span></li>

  <li class="button schiffe"><span>Schiffe</span></li>

  <li class="button flotten"><span>Flotten</span></li>

  <li class="button kommunikation"><span>Kommunikation</span></li>
  <li class="button datenbank"><span>Datenbank</span></li>
  <li class="button hilfe"><span>Hilfe</span></li>

  <li class="button optionen"><span>Optionen</span></li>
  <li class="button logout"><span>Logout</span></li>

  <?php if($_SESSION["Id"]<10) echo '<li>Schiffe beantragen</li>'; ?>
  <?php if($_SESSION["Id"]=='3') echo '<li>Handelsschiffe anzeigen</li>'; ?>
</ul>
<h2>Es wird gerade ein Tick ausgef&uuml;hrt! In der Zeit kannst du nicht auf das Spiel zugreifen. Klicke <a href="maintick.php">hier</a> um zu testen ob der Tick vorbei ist!</h2>
<?php
include("foot.php");
?>
