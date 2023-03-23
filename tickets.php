<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

$verbindung = get_verbindung();

if(isset($_POST["ptitel"])) {
	$tit = mysqli_real_escape_string($verbindung, $_POST["ptitel"]);
	$msg = mysqli_real_escape_string($verbindung, $_POST["ptext"]);
	
	$t = new Ticket(0);
	$t->assignedTo = new Account(2);
	$t->createdBy = new Account($_SESSION["Id"]);
	$t->titel = $tit;
	$t->nachricht = $msg;
	$t->saveToDB();
	
	echo '<span style="color:green;font-weight:bold;">Ticket erstellt</span>';
}


echo '<h2>Ticket&uuml;bersicht</h2>
	  <h3>offene Tickets</h3>';

$q = mysqli_query($verbindung, "SELECT * FROM tickets WHERE cr = ".$_SESSION["Id"]." AND status = 1 ORDER BY id DESC");
while($r = mysqli_fetch_array($q)) {
		$ticket = new Ticket($r["id"]);
		echo  '<a href="ticket.php?id='.$ticket->id.'">'.$ticket->titel.' ( '.$ticket->statusArr[$ticket->status].')</a><br />';
}
if(mysqli_num_rows($q) == 0) {
		echo 'keine';
}

echo '<br /><h3>erledigte Tickets</h3>';

$q = mysqli_query($verbindung, "SELECT * FROM tickets WHERE cr = ".$_SESSION["Id"]." AND status = 0 ORDER BY id DESC");
while($r = mysqli_fetch_array($q)) {
		$ticket = new Ticket($r["id"]);
		echo  '<a href="ticket.php?id='.$ticket->id.'">'.$ticket->titel.' ( '.$ticket->statusArr[$ticket->status].')</a><br />';
}
if(mysqli_num_rows($q) == 0) {
		echo 'keine';
}

echo '<br /><h3>Tickets in Arbeit</h3>';

$q = mysqli_query($verbindung, "SELECT * FROM tickets WHERE cr = ".$_SESSION["Id"]." AND status > 1 ORDER BY id DESC");
while($r = mysqli_fetch_array($q)) {
	$ticket = new Ticket($r["id"]);
		echo  '<a href="ticket.php?id='.$ticket->id.'">'.$ticket->titel.' ( '.$ticket->statusArr[$ticket->status].')</a><br />';
}
if(mysqli_num_rows($q) == 0) {
	echo 'keine';
}

echo '<br /><br />';
echo '<h2>Ticket erstellen</h2>';

echo '<form action="tickets.php" method="post">
		Titel:<br /><input type="text" name="ptitel" /><br /><br />
		Beschreibung:<br /><textarea cols="20" rows="8" name="ptext"></textarea><br /><br />
		<input type="submit" value="Ticket einreichen" />
	  </form>';

include("foot.php");
?>
