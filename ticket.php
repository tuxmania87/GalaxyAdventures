<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

$verbindung = get_verbindung();

$ticket_id = $_GET["id"];
if(!ctype_digit($ticket_id) && isset($ticket_id)) {
	die("Fehlerhafte Ticket ID!");
}

echo '<h2>Ticket&uuml;bersicht</h2>';

$ticket = new Ticket($ticket_id);

echo '<table>
		  <tr>
			  <td>Titel</td>
			  <td>'.$ticket->titel.'</td>
		  </tr>
		  <tr>
			  <td>Ersteller</td>
			  <td>'.$ticket->createdBy->nickname.'</td>
		  </tr>
		  <tr>
			  <td>zugeteilt</td>
			  <td>'.$ticket->assignedTo->nickname.'</td>
		  </tr>
		  <tr>
			  <td>Status</td>
			  <td>'.$ticket->statusArr[$ticket->status].'</td>
		  </tr>
		  <tr>
			  <td>Nachricht</td>
			  <td>'.$ticket->nachricht.'</td>
		  </tr>
		  <tr>
			  <td>Kommentare</td>
			  <td>';
			  $explode_var = explode('|',$ticket->comments);
			  for($i=0;$i<count($explode_var);$i++) {
				  echo '<br /><br />' . $exlode_var[$i];
			  }
			  echo '</td>
		  </tr>
	  </table>';
	  
?>
