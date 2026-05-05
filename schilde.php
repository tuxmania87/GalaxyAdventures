<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

include_once 'auth.php';
$userId = requireLogin();
$pid = requireIntParam('pid');
$fid = requireIntParam('fid');
$tmp = mysqli_query($verbindung, "SELECT besitzer FROM planeten WHERE id='$pid'");
while ($testtmp = mysqli_fetch_array($tmp))
    if ($userId != $testtmp["besitzer"]) die("Fehler: Besitzer-ID ung&uuml;tig");




$do=$_POST["do"];
$aktPlanet=new Planeten($pid);

if($do==6) { // schilde aufladen
$amount=ceil($_POST["samount"]);
echo $aktPlanet->fehler[$aktPlanet->schildaufladen($amount)];
}

if($_GET["do"]==7)  // schilde aktivieren
echo $aktPlanet->fehler[$aktPlanet->schilde()];


echo '<h3>Schildkontrolle</h3>';
echo 'Enerige: ',$aktPlanet->energie,'/',$aktPlanet->maxenergie,'<br />Schilde: ',$aktPlanet->schildstatus==0?'<span>':'<span style="color:yellow;">',$aktPlanet->schilde,'/',$aktPlanet->maxschilde,'</span>   ';
echo '<form action="schilde.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="6"><input type="text" name="samount"> <input type="submit" value="schilde aufladen"></form><br /><a href="schilde.php?pid=',$pid,'&fid=',$fid,'&do=7">Schilde (de)aktivieren</a><br /><br /><a href="planet.php?pid=',$pid,'">zur&uuml;ck zum Planten!</a>';
echo '<br /><hr />';
echo 'Wenn du den Schildturm abreisst erh&auml;lst du 50 Baustoff und 25 Duranium zur&uuml;ck!<br />';
echo '<br /><form action="destroy.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(1)"><input type="hidden" name="del" value="6"><input type="submit" value="abreissen"></form>';



include("foot.php");
?>
