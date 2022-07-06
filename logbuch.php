<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

$verbindung= get_verbindung();

$me=$_SESSION["Id"];
$seite=$_GET["seite"];
if($seite=='') $seite=1;
$zaehler=0;

$modus=$_GET["modus"];
if(!isset($_GET["modus"])) $modus=1;

echo '<h3>Logbuch</h3>';
//Eingang
if($modus==1) echo '<a href="logbuch.php?modus=1"><b>> Eingang <</b></a>&nbsp;&nbsp;&nbsp;&nbsp;'; else echo '<a href="logbuch.php?modus=1">Eingang</a>&nbsp;&nbsp;&nbsp;&nbsp;';
//ausgang
if($modus==2) echo '<a href="logbuch.php?modus=2"><b>> Ausgang <</b></a>'; else echo '<a href="logbuch.php?modus=2">Ausgang</a>&nbsp;&nbsp;&nbsp;&nbsp;';

//ordervar
$ordervar=$_GET["order"];
if($ordervar=='') $ordervar1="id DESC";

if($ordervar=="1D" && $modus==1) $ordervar1="initiator DESC";
if($ordervar=="1A" && $modus==1) $ordervar1="initiator ASC";

if($ordervar=="1D" && $modus==2) $ordervar1="betroffener DESC";
if($ordervar=="1A" && $modus==2) $ordervar1="betroffener ASC";

if($ordervar=="3A") $ordervar1="typ ASC";
if($ordervar=="3D") $ordervar1="typ DESC";

if($ordervar=="4A") $ordervar1="zeit ASC";
if($ordervar=="4D") $ordervar1="zeit DESC";


//endeordervar
echo '<br /><br />';
//weiteres
if($modus==1) {

echo '<table class="bordered"><tr><td><a href="logbuch.php?modus=',$modus,'&order=',$ordervar=='1D'?'1A':'1D','">Verursacher</a></td><td>Wo</td><td><a href="logbuch.php?modus=',$modus,'&order=',$ordervar=='3D'?'3A':'3D','">Typ</a></td><td><a href="logbuch.php?modus=',$modus,'&order=',$ordervar=='4D'?'4A':'4D','">Tatzeit</a></td><td>Abhandlung</td></tr>';
$abfrage=mysqli_query($verbindung,"SELECT * FROM logbuch WHERE klasse='Eingang' AND initiator != '".$_SESSION["Id"]."' AND betroffener='$me' ORDER BY $ordervar1");
while($log=mysqli_fetch_array($abfrage)) {
$zaehler++;
if($zaehler>=1+($seite-1)*25 && $zaehler<=$seite*25) {

$aktid=$log["id"];
//Anzeige
if($log["system"]>0) $sys=new System($log["system"]);
$usr=new Account($log["initiator"]);
echo '<tr><td>',$usr->nickname,'</td><td>',$log["x"],'/',$log["y"],' im ',$log["system"]==0?'Weltraum':$sys->name.'-System ('.$sys->x.'|'.$sys->y.')','</td><td>',$log["typ"],'</td><td>',gerdatum($log["zeit"]),'</td><td>',$log["text"],'</td></tr>';

}
}

echo '</table>';
}

if($modus==2) {
echo '<table class="bordered"><tr><td><a href="logbuch.php?modus=',$modus,'&order=',$ordervar=='1D'?'1A':'1D','">Betroffener</a></td><td>Wo</td><td><a href="logbuch.php?modus=',$modus,'&order=',$ordervar=='3D'?'3A':'3D','">Typ</a></td><td><a href="logbuch.php?modus=',$modus,'&order=',$ordervar=='4D'?'4A':'4D','">Tatzeit</a></td><td>Abhandlung</td></tr>';
$abfrage=mysqli_query($verbindung,"SELECT * FROM logbuch WHERE klasse='Ausgang' AND betroffener != '".$_SESSION["Id"]."' AND initiator='$me' ORDER BY $ordervar1");
while($log=mysqli_fetch_array($abfrage)) {
$zaehler++;
if($zaehler>=1+($seite-1)*25 && $zaehler<=$seite*25) {

$aktid=$log["id"];
//Anzeige
if($log["system"]>0) $sys=new System($log["system"]);
$usr=new Account($log["initiator"]);
echo '<tr><td>',$usr->nickname,'</td><td>',$log["x"],'/',$log["y"],' im ',$log["system"]==0?'Weltraum':$sys->name.'-System ('.$sys->x.'|'.$sys->y.')','</td><td>',$log["typ"],'</td><td>',gerdatum($log["zeit"]),'</td><td>',$log["text"],'</td></tr>';

}
}

echo '</table>';
}

//SEITENLOGIK
$einfaerben=false;
for($o=1;$o<=ceil($zaehler/25);$o++) {
if($zaehler>=1+($o-1)*25) {
if($seite==$o) $einfaerben=true;
echo $o==$seite?'<a href="logbuch.php?order='.$ordervar.'&seite='.$o.'&modus='.$modus.'"><span style="color:red;">'.$o.'</span></a>':'<a href="logbuch.php?order='.$ordervar.'&seite='.$o.'&modus='.$modus.'">'.$o.'</span>',$o==ceil($zaehler/25)?'':',';
}
}
include("foot.php");
?>
