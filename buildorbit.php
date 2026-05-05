<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");
include_once 'auth.php';
$userId = requireLogin();
$sid = requireIntParam('sid');
$tmp = mysqli_query($verbindung, "SELECT besitzer FROM schiffe WHERE id='$sid'");
while ($testtmp = mysqli_fetch_array($tmp))
    if ($userId != $testtmp['besitzer']) exit('Fehler: Du bist nicht der Besitzer.');
{
$besitzer = $_SESSION["Id"];
$pid = $_GET["pid"];
$modul = "orbit";
$modul .= $_GET["modul"];
$modulid=$_GET["modul"];
$sub = $_POST["sub"];

$aktPlanet = new Planet;
$aktPlanet->getData($pid);


if($sub==1)
	{ //ausbauen
$abfrage=mysqli_query($verbindung, "SELECT * FROM `$modul` WHERE pid='$pid'");
while($tmp=mysqli_fetch_array($abfrage))
{
$getmodul = $tmp["modultyp"];
$aktbau=$tmp["bauzeit"];
}

//if(bezahlbar('rohA',$pid,$stufe))

if($getmodul=="") $stufe=1;//
else if($getmodul[6]=='')
$stufe=$getmodul[5]+1; else { $stufe=$getmodul[5].$getmodul[6]; $stufe++; }
if($stufe==1 && bezahlbar('orbit',$pid,$stufe) && $aktbau==0)
{
$bauzeit=3;
mysqli_query($verbindung, "INSERT INTO `$modul` (modultyp,pid,besitzer,bauzeit) VALUES ('orbit1','$pid','$besitzer','$bauzeit')");
$kostenRa=kostenA('orbit',$stufe);
$kostenRb=kostenB('orbit',$stufe);
$aktPlanet->rohstoffa-=$kostenRa;
$aktPlanet->rohstoffb-=$kostenRb;
$aktPlanet->setData($aktPlanet->id);
$aktPlanet->getData($aktPlanet->id);
}
if($stufe==1 && !(bezahlbar('orbit',$pid,$stufe)) && $aktbau==0) echo 'Nicht genug Rohstoffe!';
if($stufe!=1 && bezahlbar('orbit',$pid,$stufe) && $aktbau==0) {
$newmodul="orbit";$newmodul.=$stufe;
$bauzeit=3+floor($stufe/5);
mysqli_query($verbindung, "UPDATE `$modul` SET modultyp='$newmodul',bauzeit='$bauzeit' WHERE pid = '$pid'");
$kostenRa=kostenA('orbit',$stufe);
$kostenRb=kostenB('orbit',$stufe);
$aktPlanet->rohstoffa-=$kostenRa;
$aktPlanet->rohstoffb-=$kostenRb;
$aktPlanet->setData($aktPlanet->id);
$aktPlanet->getData($aktPlanet->id);
}
}



//body teil
$besitzer = $_SESSION["Id"];
$pid = $_GET["pid"];
$modul = "orbit";
$modul .= $_GET["modul"];
$checked=false;
$abfrage=mysqli_query($verbindung, "SELECT * FROM `$modul` WHERE pid='$pid' AND besitzer='$besitzer'");
while($mod=mysqli_fetch_array($abfrage))
{
$tmpvar = $mod["modultyp"]; // Abfragevar
$checked=true;
if($tmpvar[0] == "o" && $tmpvar[1] == "r" && $tmpvar[2] == "b")
	{ // modul rohX ist installiert --> errechnen und modul+1 anbieten
	$bauzeit = $mod["bauzeit"];
	if($tmpvar[6]=='') $rohA=$tmpvar[5];
	else $rohA=$tmpvar[5].$tmpvar[6];			//Stufe modul A
	}
}
if($checked)
	{
if(isset($rohA)) {
?>
<form action="buildorbit.php?pid=<?php echo $pid; ?>&modul=<?php echo $modulid; ?>"  method="post">
<div class="rahmen"><h4>Solarsatellit bauen:</h4>
<table>
<tr><td>Kosten Rohstoff A: </td><td><?php echo kostenA('orbit',$rohA+1),' (',$aktPlanet->rohstoffa,')</td></tr>'; ?>
<tr><td>Kosten Rohstoff B: </td><td><?php echo kostenB('orbit',$rohA+1),' (',$aktPlanet->rohstoffb,')</td></tr>'; ?>
<tr><td>Effekt: </td><td><span style="color:green;font-weight;">+<?php echo output('orbit',$rohA+1); ?> Energie</span></td></tr>
<tr><td>Dauerkosten: </td><td><span style="color:red;">-<?php echo $rohA+1; ?> Deuterium</span></td></tr>
</table>
<?php
$bool=bezahlbar('orbit',$pid,$rohA+1);
?>
<input type="hidden" name="sub" value="1">
<input type="submit" value="Solarsatellit (<?php echo $rohA+1; ?>) bauen" <?php if(!$bool || $bauzeit >0) echo 'disabled=true'; ?>>
</form></div><br />
<?php
}


}
else
	{
	echo 'kein Modul installiert<br /><br />';
	//modultabelle ->  modul.dat
	?>
<form action="buildorbit.php?pid=<?php echo $pid; ?>&modul=<?php echo $modulid; ?>"  method="post">
<div class="rahmen"><h4>Rohstoff A Fabrik bauen:</h4>
<table>
<tr><td>Kosten Rohstoff A: </td><td><?php echo '80 (',$aktPlanet->rohstoffa,')</td></tr>'; ?>
<tr><td>Kosten Rohstoff B: </td><td><?php echo '27  (',$aktPlanet->rohstoffb,')</td></tr>'; ?>
<tr><td>Effekt: </td><td><span style="color:green;font-weight;">+15 Energie</span></td></tr>
<tr><td>Dauerkosten: </td><td><span style="color:red;">-1 Deuterium</span></td></tr>
</table>
<?php
$bool=bezahlbar('orbit',$pid,$rohA+1);
?>
<input type="hidden" name="sub" value="1">
<input type="submit" value="Solarsatellit (1) bauen" <?php if(!$bool) echo 'disabled=true'; ?>>
</form></div><br />

<?php
	}
echo '<br /><a href="planet.php?pid=',$aktPlanet->id,'">zur&uuml;ck</a>';
}
include("foot.php");
?>
