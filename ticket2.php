<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

echo '<h2>Ticket&uuml;bersicht</h2>
	  <h3>offene Tickets</h3>';

$q = mysql_query("SELECT * FROM tickets WHERE cr = ".$_SESSION["Id"]." AND status = 1 ORDER BY id DESC");
while($r = mysql_fetch_array($q)) {
		
}
if(mysql_num_rows($q) == 0) {
		echo 'keine';
}

echo '<h3>erledigte Tickets</h3>';

$q = mysql_query("SELECT * FROM tickets WHERE cr = ".$_SESSION["Id"]." AND status = 0 ORDER BY id DESC");
while($r = mysql_fetch_array($q)) {
		
}
if(mysql_num_rows($q) == 0) {
		echo 'keine';
}

echo '<h3>Tickets in Arbeit</h3>';

$q = mysql_query("SELECT * FROM tickets WHERE cr = ".$_SESSION["Id"]." AND status > 1 ORDER BY id DESC");
while($r = mysql_fetch_array($q)) {

}
if(mysql_num_rows($q) == 0) {
	echo 'keine';
}


include("foot.php");
?>
