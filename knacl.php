<?php

include("head.php");
?>
<script type="text/javascript">
var xmlhttpreq = null;

function GetHTTPRequest() {
  var xmlhttp = false;
    try {
      xmlhttp = new XMLHttpRequest();
    } catch (trymicrosoft) {
      try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
      } catch (othermicrosoft) {
        try {
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (failed) {
          xmlhttp = false;
        }
      }
    }
  return xmlhttp;
}
 
xmlhttpreq = GetHTTPRequest();
 
function StartRequest() {
  // Persistente Verbindung öffnen
  var myvar = document.getElementById("idinput").value;
  xmlhttpreq.open('GET', 'idmapper.php?id='+myvar, true);
  xmlhttpreq.onreadystatechange = handle_response;
  xmlhttpreq.send(null);
}

function handle_response() {
  if (xmlhttpreq.readyState==4 && xmlhttpreq.status==200) {
    document.getElementById("showuser").innerHTML = xmlhttpreq.responseText;
  }
  // Bei Verbindungsabbruch gleich neu initialisieren
  if (xmlhttpreq.readyState==4) {
    //StartRequest();
  }
}


</script>

<?php
include("navlogged.php");
include("klassen.php");
echo '<div style="min-width:700px;max-width:950px;">';
//CHEATSCHUTZ ANFANG


$betray = false;
if(!ctype_digit($_GET["cid"])) {
    die("Fehler: Channel ID fehlerhaft");
}

$channel = $_GET["cid"];
$ich = new Account($_SESSION["Id"]);

$ch = new Channel($channel);

if($ch->founder->id != $ich->id) {
    die("unautorisierter Zugriff!");
}

if(isset($_GET["rm"]) && ctype_digit($_GET["rm"])) {
    mysql_query("delete from channelabo where uid=".$_GET["rm"]." and cid=".$ch->id);
}

if(isset($_POST["inviteid"]) && ctype_digit($_POST["inviteid"])) {
    mysql_query("insert into channelabo (cid,uid,status) values ('".$ch->id."','".$_POST["inviteid"]."','2')");
}


if($ch->founder->id != $ich->id) {
    echo "Dies ist die Zugriffskontrollsteuerung vom Channel ".$ch->caption." (".$ch->id.").";
    echo "Du bist nicht der Gründer dieser Gruppe, deswegen kannst du keine Einstellungen vornehmen.<br /><br />";
    $bu = new Button("kommunikation.php","zum Kommunikationsnetz zurückkehren");
    $bu->printme();
    die();
}

echo '<h2>Zugriffskontrolle für '.$ch->caption.' ('.$ch->id.')</h2>';

$q = mysql_query("select uid from channelabo where cid=".$ch->id." and status=1");

if(mysql_num_rows($q) > 0) {
    echo '<h3>autorisierte Nutzer</h3><table class="liste">';
    echo '<tr><th>ID</th><th>Name</th><th>entfernen</th></tr>';
} else {
    echo '<h3>es gibt noch keine autorisierten Nutzer</h3>';
}

while($r = mysql_fetch_array($q)) {
    $t_acc = new Account($r["uid"]);
    echo '<tr><td>'.$t_acc->id.'</td><td>'.$t_acc->nickname.'</td><td><a href="knacl.php?cid='.$ch->id.'&rm='.$t_acc->id.'">';
    echo '<img src="images/misc/nix.png" border="0" onmouseover="Tip(\'<b>User von der Liste entfernen</b>\')" onmouseout="UnTip()" /></a></td></tr>';
}

if(mysql_num_rows($q) > 0) 
    echo "</table><br />";

//Pending

$q = mysql_query("select uid from channelabo where cid=".$ch->id." and status=2");

if(mysql_num_rows($q) > 0) {
    echo '<h3>eingeladene Nutzer</h3><table class="liste">';
    echo '<tr><th>ID</th><th>Name</th><th>entfernen</th></tr>';
} else {
    echo '<h3>es gibt keine eingeladenen Nutzer</h3>';
}

while($r = mysql_fetch_array($q)) {
    $t_acc = new Account($r["uid"]);
    echo '<tr><td>'.$t_acc->id.'</td><td>'.$t_acc->nickname.'</td><td><a href="knacl.php?cid='.$ch->id.'&rm='.$t_acc->id.'">';
    echo '<img src="images/misc/nix.png" onmouseover="Tip(\'<b>Einladung zurücknehmen</b>\')" onmouseout="UnTip()" border="0" /></a></td></tr>';
}

if(mysql_num_rows($q) > 0) 
    echo "</table>";

//invite
echo '<br /><h3>Einladen</h3>';
echo '<form action="knacl.php?cid='.$ch->id.'" method="post">User mit der ID <input type="text" size="2" id="idinput" name="inviteid" onkeyup="StartRequest();" />';
echo '<br /><br /><span style="width:300px" id="showuser">Spielername</span>&nbsp;&nbsp;&nbsp;';
$bu = new Button(""," einladen " );
$bu->printme();
echo '</form>';

echo "<br /><br />";

$bu = new Button("kommunikation.php","zurück zum Kommunikationsnetzwerk");
$bu->printme();

include("foot.php");
?>
