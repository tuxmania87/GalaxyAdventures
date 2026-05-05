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
$modul = "modul";
$modul .= $_GET["modul"];
$modulid=$_GET["modul"];
$sub = $_POST["sub"];

$aktPlanet = new Planet;
$aktPlanet->getData($pid);

$aktPlanet->name;

if($sub==1)
	{ //modul A ausbauen
$abfrage=mysqli_query($verbindung, "SELECT * FROM `$modul` WHERE pid='$pid'");
while($tmp=mysqli_fetch_array($abfrage))
{
$getmodul = $tmp["modultyp"];
$aktbau=$tmp["bauzeit"];
}

//if(bezahlbar('rohA',$pid,$stufe))

if($getmodul=="") $stufe=1;//
else if($getmodul[5]=='')
$stufe=$getmodul[4]+1; else { $stufe=$getmodul[4].$getmodul[5]; $stufe++; }
if($stufe==1 && bezahlbar('rohA',$pid,$stufe) && $aktbau==0)
{
$bauzeit=2;
mysqli_query($verbindung, "INSERT INTO `$modul` (modultyp,pid,besitzer,bauzeit) VALUES ('rohA1','$pid','$besitzer','$bauzeit')");
$kostenRa=kostenA('rohA',$stufe);
$kostenRb=kostenB('rohA',$stufe);
$aktPlanet->rohstoffa-=$kostenRa;
$aktPlanet->rohstoffb-=$kostenRb;
$aktPlanet->setData($aktPlanet->id);
$aktPlanet->getData($aktPlanet->id);
}
if($stufe==1 && !(bezahlbar('rohA',$pid,$stufe)) && $aktbau==0) echo 'Nicht genug Rohstoffe!';
if($stufe!=1 && bezahlbar('rohA',$pid,$stufe) && $aktbau==0) {
$newmodul="rohA";$newmodul.=$stufe;
$bauzeit=2+floor($stufe/6);
mysqli_query($verbindung, "UPDATE `$modul` SET modultyp='$newmodul',bauzeit='$bauzeit' WHERE pid = '$pid'");
$kostenRa=kostenA('rohA',$stufe);
$kostenRb=kostenB('rohA',$stufe);
$aktPlanet->rohstoffa-=$kostenRa;
$aktPlanet->rohstoffb-=$kostenRb;
$aktPlanet->setData($aktPlanet->id);
$aktPlanet->getData($aktPlanet->id);
}
}


if($sub==2)
	{ //modul B ausbauen
$abfrage=mysqli_query($verbindung, "SELECT * FROM `$modul` WHERE pid='$pid'");
while($tmp=mysqli_fetch_array($abfrage))
{
$getmodul = $tmp["modultyp"];
$aktbau=$tmp["bauzeit"];
}
if($getmodul=="")
$stufe=1;
else if($getmodul[5]=='')
$stufe=$getmodul[4]+1; else { $stufe=$getmodul[4].$getmodul[5]; $stufe++; }


if($stufe==1 && bezahlbar('rohB',$pid,$stufe) && $aktbau==0)
{
$bauzeit=3;
mysqli_query($verbindung, "INSERT INTO `$modul` (modultyp,pid,besitzer,bauzeit) VALUES ('rohB1','$pid','$besitzer','$bauzeit')");
$kostenRa=kostenA('rohB',$stufe);
$kostenRb=kostenB('rohB',$stufe);
mysqli_query($verbindung, "UPDATE `schiffe` SET rohstoffa=rohstoffa-$kostenRa,rohstoffb=rohstoffb-$kostenRb WHERE id='$aktPlanet->id'");
$aktPlanet->getData($aktPlanet->id);
}
if($stufe!=1 && bezahlbar('rohA',$pid,$stufe) && $aktbau==0)
{

$newmodul="rohB";$newmodul.=$stufe;
$bauzeit=3+floor($stufe/5);
mysqli_query($verbindung, "UPDATE `$modul` SET modultyp='$newmodul',bauzeit='$bauzeit' WHERE pid = '$pid'");
$kostenRa=kostenA('rohB',$stufe);
$kostenRb=kostenB('rohB',$stufe);
mysqli_query($verbindung, "UPDATE `schiffe` SET rohstoffa=rohstoffa-$kostenRa,rohstoffb=rohstoffb-$kostenRb WHERE id='$aktPlanet->id'");
$aktPlanet->getData($aktPlanet->id);
}

	}



if($sub==3)
	{ //modul Lager ausbauen
	}



$besitzer = $_SESSION["Id"];
$pid = $_GET["pid"];
$modul = "modul";
$modul .= $_GET["modul"];
$checked=false;
$abfrage=mysqli_query($verbindung, "SELECT * FROM `$modul` WHERE pid='$pid' AND besitzer='$besitzer'");
while($mod=mysqli_fetch_array($abfrage))
{
$tmpvar = $mod["modultyp"]; // Abfragevar
$checked=true;
if($tmpvar[0] == "r" && $tmpvar[1] == "o" && $tmpvar[2] == "h" && $tmpvar[3] == "A")
	{ // modul rohX ist installiert --> errechnen und modul+1 anbieten
	$bauzeit = $mod["bauzeit"];
	if($tmpvar[5]=='') $rohA=$tmpvar[4];
	else $rohA=$tmpvar[4].$tmpvar[5];			//Stufe modul A
	}
if($tmpvar[0] == "r" && $tmpvar[1] == "o" && $tmpvar[2] == "h" && $tmpvar[3] == "B")
	{ // modul rohX ist installiert --> errechnen und modul+1 anbieten
	if($tmpvar[5]=='') $rohB=$tmpvar[4];
	else $rohB=$tmpvar[4].$tmpvar[5];
	$bauzeit = $mod["bauzeit"];
	}
if($tmpvar[0] == "f" && $tmpvar[1] == "o" && $tmpvar[2] == "r")
	{ // modul forX ist installiert --> errechnen und modul+1 anbieten
	}
}
if($checked)
	{
if(isset($rohA)) {
?>
<form action="build.php?pid=<?php echo $pid; ?>&modul=<?php echo $modulid; ?>"  method="post">
<div class="rahmen"><h4>Rohstoff A Fabrik bauen:</h4>
<table>
<tr><td>Kosten Rohstoff A: </td><td><?php echo kostenA('rohA',$rohA+1),' (',$aktPlanet->rohstoffa,')</td></tr>'; ?>
<tr><td>Kosten Rohstoff B: </td><td><?php echo kostenB('rohA',$rohA+1),' (',$aktPlanet->rohstoffb,')</td></tr>'; ?>
<tr><td>Effekt: </td><td><span style="color:green;font-weight;">+<?php echo 10*($rohA+1); ?> Rohstoff-A</span></td></tr>
</table>
<?php
$bool=bezahlbar('rohA',$pid,$rohA+1);
?>
<input type="hidden" name="sub" value="1">
<input type="submit" value="Rohstoff A Fabrik(<?php echo $rohA+1; ?>) bauen" <?php if(!$bool || $bauzeit >0) echo 'disabled=true'; ?>>
</form></div><br />
<?php
}
if(isset($rohB)) {
?>
<form action="build.php?pid=<?php echo $pid; ?>&modul=<?php echo $modulid; ?>"  method="post">
<div class="rahmen"><h4>Rohstoff B Fabrik bauen:</h4>
<table>
<tr><td>Kosten Rohstoff A: </td><td><?php echo kostenA('rohB',$rohB+1),' (',$aktPlanet->rohstoffa,')</td></tr>'; ?>
<tr><td>Kosten Rohstoff B: </td><td><?php echo kostenB('rohB',$rohB+1),' (',$aktPlanet->rohstoffb,')</td></tr>'; ?>
<tr><td>Effekt: </td><td><span style="color:green;font-weight;">+<?php echo 5*($rohB+1); ?> Rohstoff-B</span></td></tr>
</table>
<?php
$bool=bezahlbar('rohB',$pid,$rohB+1);
?>
<input type="hidden" name="sub" value="2">
<input type="submit" value="Rohstoff B Fabrik(<?php echo $rohB+1; ?>) bauen" <?php if(!$bool || $bauzeit >0) echo 'disabled=true'; ?>>
</form></div><br />
<?php
}




}
else
	{
	echo 'kein Modul installiert<br /><br />';
	//modultabelle ->  modul.dat
	?>
<form action="build.php?pid=<?php echo $pid; ?>&modul=<?php echo $modulid; ?>"  method="post">
<div class="rahmen"><h4>Rohstoff A Fabrik bauen:</h4>
<table>
<tr><td>Kosten Rohstoff A: </td><td><?php echo '40 (',$aktPlanet->rohstoffa,')</td></tr>'; ?>
<tr><td>Kosten Rohstoff B: </td><td><?php echo '0    (',$aktPlanet->rohstoffb,')</td></tr>'; ?>
<tr><td>Effekt: </td><td><span style="color:green;font-weight;">+10 Rohstoff-A</span></td></tr>
</table>
<?php
$bool=bezahlbar('rohA',$pid,$rohA+1);
?>
<input type="hidden" name="sub" value="1">
<input type="submit" value="Rohstoff A Fabrik(1) bauen" <?php if(!$bool) echo 'disabled=true'; ?>>
</form></div><br />
<form action="build.php?pid=<?php echo $pid; ?>&modul=<?php echo $modulid; ?>"  method="post">
<div class="rahmen"><h4>Rohstoff B Fabrik bauen:</h4>
<table>
<tr><td>Kosten Rohstoff A: </td><td><?php echo '100 (',$aktPlanet->rohstoffa,')</td></tr>'; ?>
<tr><td>Kosten Rohstiff B: </td><td><?php echo '15  (',$aktPlanet->rohstoffb,')</td></tr>'; ?>
<tr><td>Effekt: </td><td><span style="color:green;font-weight;">+5 Rohstoff-B</span></td></tr>
</table>

<?php
$bool=bezahlbar('rohB',$pid,$rohB+1);
?>
<input type="hidden" name="sub" value="2">
<input type="submit" value="Rohstoff B Fabrik(1) bauen" <?php if(!$bool) echo 'disabled=true'; ?>>
</form></div><br />
<?php
	}
echo '<br /><a href="planet.php?pid=',$aktPlanet->id,'">zur&uuml;ck</a>';
}
include("foot.php");
?>
