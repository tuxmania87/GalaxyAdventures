<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

$account=new Account($_SESSION["Id"]);
if(!$account->mapper) die("Error: Insufficient Access Level");

if(!isset($_GET["system"])) die("Bitte System eingeben.");
if(!ctype_digit($_GET["system"])) die("ID Konflikt");

$system=new System($_GET["system"]);

$pinsel=explode("-",$_GET["pinsel"]);
$klasse=$pinsel[0];
$typ=$pinsel[1];

echo '<h3>Du mappst gerade im ',$system->name,' im Weltraum gelegen unter: ',$system->x,'/',$system->y,'</h3><img src="',$system->bild,'" border="0" /><br />';
echo '<a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'"><b>AKTUALISIEREN</b></a><br />';
//Berechnungen
if(ctype_digit($_GET["dx"]) && $_GET["dx"]>-25 && ctype_digit($_GET["dy"]) && $_GET["dy"]>-25)
	{		//loeschen
	mysqli_query($verbindung, "DELETE FROM planeten WHERE x='".$_GET["dx"]."' AND y='".$_GET["dy"]."' AND system='".$system->id."'");
	mysqli_query($verbindung, "DELETE FROM weltraum WHERE x='".$_GET["dx"]."' AND y='".$_GET["dy"]."' AND system='".$system->id."'");
	}




//Editiermodus
if(ctype_digit($_GET["sx"]) && $_GET["sx"]>-25 && ctype_digit($_GET["sy"]) && $_GET["sy"]>-25)
	{
	if($klasse=='P' || $klasse=='W') 
	{
	switch ($typ) {
		case "m": mysqli_query($verbindung, "INSERT INTO planeten (x,y,system,besitzer,energie,maxenergie,lager,baustoff,duranium,typ,name,orbit,bild) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','".$_GET["system"]."','2','20','20','300','0','0','m','noname','1','planet.jpg')") or die(mysqli_error($verbindung));
				mysqli_query($verbindung, "INSERT INTO `planet2` (`feld1`, `feld2`, `feld3`, `feld4`, `feld5`, `feld6`, `feld7`, `feld8`, `feld9`, `feld10`, `feld11`, `feld12`, `feld13`, `feld14`, `feld15`, `feld16`, `feld17`, `feld18`, `feld19`, `feld20`, `feld21`, `feld22`, `feld23`, `feld24`, `feld25`, `feld26`, `feld27`, `feld28`, `feld29`, `feld30`, `feld31`, `feld32`, `feld33`, `feld34`, `feld35`, `feld36`, `feld37`, `feld38`, `feld39`, `feld40`, `feld41`, `feld42`, `feld43`, `feld44`, `feld45`, `feld46`, `feld47`, `feld48`, `feld49`, `feld50`) VALUES('0-0-i', '0-0-i', '0-0-i', '0-0-g', '0-0-g', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-d', '0-0-f', '0-0-g', '0-0-f', '0-0-m', '0-0-f', '0-0-m', '0-0-g', '0-0-f', '0-0-f', '0-0-d', '0-0-f', '0-0-d', '0-0-m', '0-0-m', '0-0-g', '0-0-g', '0-0-w', '0-0-g', '0-0-d', '0-0-g', '0-0-w', '0-0-g', '0-0-g', '0-0-f', '0-0-d', '0-0-w', '0-0-f', '0-0-g', '0-0-g', '0-0-i', '0-0-i', '0-0-g', '0-0-f', '0-0-g', '0-0-g', '0-0-i', '0-0-g', '0-0-g', '0-0-i')");
				break;
		case "l": mysqli_query($verbindung, "INSERT INTO planeten (x,y,system,besitzer,energie,maxenergie,lager,baustoff,duranium,typ,name,orbit,bild) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','".$_GET["system"]."','2','20','20','300','0','0','l','noname','1','lava.jpg')") or die(mysqli_error($verbindung));
				mysqli_query($verbindung, "INSERT INTO `planet2` ( `feld1`, `feld2`, `feld3`, `feld4`, `feld5`, `feld6`, `feld7`, `feld8`, `feld9`, `feld10`, `feld11`, `feld12`, `feld13`, `feld14`, `feld15`, `feld16`, `feld17`, `feld18`, `feld19`, `feld20`, `feld21`, `feld22`, `feld23`, `feld24`, `feld25`, `feld26`, `feld27`, `feld28`, `feld29`, `feld30`, `feld31`, `feld32`, `feld33`, `feld34`, `feld35`, `feld36`, `feld37`, `feld38`, `feld39`, `feld40`, `feld41`, `feld42`, `feld43`, `feld44`, `feld45`, `feld46`, `feld47`, `feld48`, `feld49`, `feld50`) VALUES( '0-0-l', '0-0-l', '0-0-s', '0-0-s', '0-0-l', '0-0-l', '0-0-l', '0-0-l', '0-0-l', '0-0-s', '0-0-l', '0-0-l', '0-0-v', '0-0-s', '0-0-s', '0-0-l', '0-0-l', '0-0-s', '0-0-l', '0-0-l', '0-0-l', '0-0-s', '0-0-v', '0-0-s', '0-0-l', '0-0-s', '0-0-s', '0-0-l', '0-0-l', '0-0-l', '0-0-s', '0-0-l', '0-0-v', '0-0-s', '0-0-l', '0-0-l', '0-0-s', '0-0-v', '0-0-l', '0-0-v', '0-0-v', '0-0-l', '0-0-s', '0-0-s', '0-0-s', '0-0-l', '0-0-l', '0-0-l', '0-0-l', '0-0-l')");
				break;
		case "i": mysqli_query($verbindung, "INSERT INTO planeten (x,y,system,besitzer,energie,maxenergie,lager,baustoff,duranium,typ,name,orbit,bild) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','".$_GET["system"]."','2','20','20','300','0','0','i','noname','1','eisplanet.jpg')") or die(mysqli_error($verbindung));
				mysqli_query($verbindung, "INSERT INTO `planet2` (`feld1`, `feld2`, `feld3`, `feld4`, `feld5`, `feld6`, `feld7`, `feld8`, `feld9`, `feld10`, `feld11`, `feld12`, `feld13`, `feld14`, `feld15`, `feld16`, `feld17`, `feld18`, `feld19`, `feld20`, `feld21`, `feld22`, `feld23`, `feld24`, `feld25`, `feld26`, `feld27`, `feld28`, `feld29`, `feld30`, `feld31`, `feld32`, `feld33`, `feld34`, `feld35`, `feld36`, `feld37`, `feld38`, `feld39`, `feld40`, `feld41`, `feld42`, `feld43`, `feld44`, `feld45`, `feld46`, `feld47`, `feld48`, `feld49`, `feld50`) VALUES ( '0-0-i-60-1', '0-0-is-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-fl-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-is-60-1', '0-0-i-60-1', '0-0-im-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-im-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-fl-60-1', '0-0-fl-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-im-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-im-60-1', '0-0-i-60-1', '0-0-fl-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-is-60-1', '0-0-fl-60-1', '0-0-i-60-1', '0-0-fl-60-1', '0-0-i-60-1', '0-0-i-60-1', '0-0-is-60-1', '0-0-i-60-1')");
				break;
		case "z": mysqli_query($verbindung, "INSERT INTO planeten (x,y,system,besitzer,energie,maxenergie,lager,baustoff,duranium,typ,name,orbit,bild) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','".$_GET["system"]."','2','20','20','300','0','0','z','noname','1','wuste.jpg')") or die(mysqli_error($verbindung));
				mysqli_query($verbindung, "INSERT INTO `planet2` ( `feld1`, `feld2`, `feld3`, `feld4`, `feld5`, `feld6`, `feld7`, `feld8`, `feld9`, `feld10`, `feld11`, `feld12`, `feld13`, `feld14`, `feld15`, `feld16`, `feld17`, `feld18`, `feld19`, `feld20`, `feld21`, `feld22`, `feld23`, `feld24`, `feld25`, `feld26`, `feld27`, `feld28`, `feld29`, `feld30`, `feld31`, `feld32`, `feld33`, `feld34`, `feld35`, `feld36`, `feld37`, `feld38`, `feld39`, `feld40`, `feld41`, `feld42`, `feld43`, `feld44`, `feld45`, `feld46`, `feld47`, `feld48`, `feld49`, `feld50`) VALUES ('0-0-d', '0-0-d', '0-0-d', '0-0-d', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-d', '0-0-d', '0-0-d', '0-0-dm', '0-0-d', '0-0-dm', '0-0-dm', '0-0-d', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-d', '0-0-dm', '0-0-d', '0-0-d', '0-0-dm', '0-0-d', '0-0-dm', '0-0-d', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-d', '0-0-d', '0-0-d', '0-0-dm', '0-0-d', '0-0-d', '0-0-dm', '0-0-dm', '0-0-d', '0-0-d', '0-0-d', '0-0-dm', '0-0-d', '0-0-d', '0-0-d', '0-0-d', '0-0-d', '0-0-d')");
				break;
		case "d": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','d','".$system->id."')"); break;
		case "dk": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','dk','".$system->id."')"); break;
		case "e": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','e','".$system->id."')"); break;
		case "ek": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','ek','".$system->id."')"); break;
		case "x": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','x','".$system->id."')"); break;
		case "metrion": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','metrion','".$system->id."')"); break;
		case "radio": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','radio','".$system->id."')"); break;
		case "b": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','b','".$system->id."')"); break;
		case "g": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','g','".$system->id."')"); break;
		case "p": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','p','".$system->id."')"); break;
		case "blau": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','blau','".$system->id."')"); break;
		case "b1": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','b1','".$system->id."')"); break;
		case "b2": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','b2','".$system->id."')"); break;
		case "b3": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','b3','".$system->id."')"); break;
		case "b4": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','b4','".$system->id."')"); break;
		case "gelb": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','gelb','".$system->id."')"); break;
		case "orange": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','orange','".$system->id."')"); break;
		case "rot": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','rot','".$system->id."')"); break;
		case "r1": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','r1','".$system->id."')"); break;
		case "r2": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','r2','".$system->id."')"); break;
		case "r3": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','r3','".$system->id."')"); break;
		case "r4": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','r4','".$system->id."')"); break;
		case "weiss": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','weiss','".$system->id."')"); break;
		case "bs": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','bs','".$system->id."')"); break;
		case "rs": mysqli_query($verbindung, "INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','rs','".$system->id."')"); break;
		case "lm": mysqli_query($verbindung, "INSERT INTO planeten (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','lm','".$system->id."')"); 
				 mysqli_query($verbindung, "INSERT INTO planet2 () VALUES ()");
				 break;
		case "mm": mysqli_query($verbindung, "INSERT INTO planeten (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','mm','".$system->id."')"); 
				 mysqli_query($verbindung, "INSERT INTO planet2 () VALUES ()");
				 break;
		case "om": mysqli_query($verbindung, "INSERT INTO planeten (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','om','".$system->id."')"); 
				mysqli_query($verbindung, "INSERT INTO planet2 () VALUES ()");
				break;
		case "gasi": mysqli_query($verbindung, "INSERT INTO planeten (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','gasi','".$system->id."')"); 
				mysqli_query($verbindung, "INSERT INTO planet2 () VALUES ()");
				break;
		case "gasj": mysqli_query($verbindung, "INSERT INTO planeten (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','gasj','".$system->id."')"); 
				mysqli_query($verbindung, "INSERT INTO planet2 () VALUES ()");
				break;
		case "gass": mysqli_query($verbindung, "INSERT INTO planeten (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','gass','".$system->id."')"); 
				mysqli_query($verbindung, "INSERT INTO planet2 () VALUES ()");
				break;
		default: echo 'not yet implemented!'; break;
		}
	}
}
	


{
if(!isset($_GET["pinsel"]) || $_GET["pinsel"]=='') {
	echo 'Du hast nichts ausgew&auml;hlt. Bitte <a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&height=470&x=',$_GET["x"],'&y=',$_GET["y"],'"> >HIER< </a> ausw&auml;hlen!<br />';
	}
	
if($klasse=='W')
	{
	echo '<div style="width:400px;border:2px solid red;">';
	switch ($typ) {
	case "d": $dovar=0; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="deut.jpg" border="0" /> - dichtes Deuteriumfeld</a><br />'; break;
	case "dk": $dovar=1;echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="deutklein.jpg" border="0" /> - d&uuml;nnes Deuteriumfeld</a><br />'; break;
	case "e": $dovar=2; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="erz.jpg" border="0" /> - dichtes Asteroidenfeld</a><br />'; break;
	case "ek": $dovar=3; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="erzklein.jpg" border="0" /> - d&uuml;nnes Asteroidenfeld</a><br />'; break;
	case "x": $dovar=4; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="schwarzesloch.jpg" border="0" /> - schwarzes Loch</a><br />'; break;
	case "b": $dovar=5; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="nebel.jpg" border="0" /> - Ceru Nebel</a><br />'; break;
	case "g": $dovar=6; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="green.jpg" border="0" /> - Meta Nebel</a><br />'; break;
	case "p": $dovar=7; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="pulsar.jpg" border="0" /> - gravi. Verzerrung</a><br />'; break;
	case "radio": $dovar=8; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="nebelgelb.jpg" border="0" /> - radioaktiver Nebel</a><br />'; break;
	case "metrion": $dovar=9; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="metrion.jpg" border="0" /> - Metriongasnebel</a><br />'; break;
	case "lim": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="limes.jpg" border="0" /> - Begrenzungsnebel</a><br />'; break;
	
	case "b1": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="blaul.jpg" border="0" /> - blauer Stern</a><br />'; break;
	case "b2": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="blau2.jpg" border="0" /> - blauer Stern</a><br />'; break;
	case "b3": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="blau3.jpg" border="0" /> - blauer Stern</a><br />'; break;
	case "b4": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="blau4.jpg" border="0" /> - blauer Stern</a><br />'; break;
	case "blau": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="blau.jpg" border="0" /> - blauer Stern</a><br />'; break;
	case "bs": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="blaustell.jpg" border="0" /> - blaue stellare Materie</a><br />'; break;
	case "r1": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="rot1.jpg" border="0" /> - roter Stern</a><br />'; break;
	case "r2": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="rot2.jpg" border="0" /> - roter Stern</a><br />'; break;
	case "r3": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="rot3.jpg" border="0" /> - roter Stern</a><br />'; break;
	case "r4": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="rot4.jpg" border="0" /> - roter Stern</a><br />'; break;
	case "rot": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="rot.jpg" border="0" /> - roter Stern</a><br />'; break;
	case "rs": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="rotstell.jpg" border="0" /> - rotte stellare Materie</a><br />'; break;
	case "orange": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="orange.jpg" border="0" /> - oranger Stern</a><br />'; break;
	case "weiss": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="weiss.jpg" border="0" /> - weiss Stern</a><br />'; break;
	case "gelb": $dovar=10; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="gelb.jpg" border="0" /> - gelb.jpg</a><br />'; break;
	
	default: $dovar=-1; break;
	}
	echo '</div>';
}

if($klasse=='P')
	{
	echo '<div style="width:400px;border:2px solid red;">';
	switch ($typ) {
	case "m": $dovar=0; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="planet.jpg" border="0" /> - Klasse M Planet</a><br />'; break;
	case "l": $dovar=1;echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="lava.jpg" border="0" /> - lavaplanet</a><br />'; break;
	case "i": $dovar=2; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="eisplanet.jpg" border="0" /> - Eisplanet</a><br />'; break;
	case "z": $dovar=3; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="wuste.jpg" border="0" /> - Wustenplanet</a><br />'; break;
	case "om": $dovar=4; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="omond.jpg" border="0" /> - O-MOND</a><br />'; break;
	case "mm": $dovar=5; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="mmond.jpg" border="0" /> - M-MOND</a><br />'; break;
	case "lm": $dovar=6; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="lmond.jpg" border="0" /> - L-MOND</a><br />'; break;
	case "gasi": $dovar=7; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="gasi.jpg" border="0" /> - GAS I</a><br />'; break;
	case "gasj": $dovar=8; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="gasj.jpg" border="0" /> - GAS J</a><br />'; break;
	case "gass": $dovar=9; echo '<a class="thickbox" href="editsystemtool.php?system=',$_GET["system"],'&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="gass.jpg" border="0" /> - GAS S</a><br />'; break;
	default: $dovar=-1; break;
	}
	echo '</div>';
}

echo '<table>';
for($y=0;$y<=20;$y++)
	for($x=0;$x<=20;$x++)
	{
	if($x==0) echo '<tr><td>',$y,'</td>';
		if($y==0 && $x>0) echo '<td><center>',$x,'</td>';
	if($y>0 && $x>0) {
	$done=false;
	$abfrage=mysqli_query($verbindung, "SELECT * FROM planeten WHERE system='".$system->id."' AND x='$x' AND y='$y'");
	while($row=mysqli_fetch_array($abfrage))
	{
	if($row["typ"]=='m') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="planet.jpg" border="0" /></a></td>';
	if($row["typ"]=='l') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="lava.jpg" border="0" /></a></td>';
	if($row["typ"]=='i') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="eisplanet.jpg" border="0" /></a></td>';
	if($row["typ"]=='z') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="wuste.jpg" border="0" /></a></td>';
	if($row["typ"]=='lm') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="lmond.jpg" border="0" /></a></td>';
	if($row["typ"]=='om') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="omond.jpg" border="0" /></a></td>';
	if($row["typ"]=='mm') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="mmond.jpg" border="0" /></a></td>';
	if($row["typ"]=='gasi') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="gasi.jpg" border="0" /></a></td>';
	if($row["typ"]=='gasj') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="gasj.jpg" border="0" /></a></td>';
	if($row["typ"]=='gass') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="gass.jpg" border="0" /></a></td>';
	$done=true;
	}
	$abfrage=mysqli_query($verbindung, "SELECT * FROM weltraum WHERE system='".$system->id."' AND x='$x' AND y='$y'");
	while($row=mysqli_fetch_array($abfrage))
	{
	if($row["typ"]=='b') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="nebel.jpg" border="0" /></a></td>';
	if($row["typ"]=='e') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="erz.jpg" border="0" /></a></td>';
	if($row["typ"]=='ek') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="erzklein.jpg" border="0" /></a></td>';
	if($row["typ"]=='g') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="green.jpg" border="0" /></a></td>';
	if($row["typ"]=='d') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="deut.jpg" border="0" /></a></td>';
	if($row["typ"]=='dk') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="deutklein.jpg" border="0" /></a></td>';
	if($row["typ"]=='x') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="schwarzesloch.jpg" border="0" /></a></td>';
	if($row["typ"]=='metrion') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="metrion.jpg" border="0" /></a></td>';
	if($row["typ"]=='radio') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="nebelgelb.jpg" border="0" /></a></td>';
	if($row["typ"]=='p') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="pulsar.jpg" border="0" /></a></td>';
	if($row["typ"]=='gelb') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="gelb.jpg" border="0" /></a></td>';
	if($row["typ"]=='weiss') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="weiss.jpg" border="0" /></a></td>';
	if($row["typ"]=='rot') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="rot.jpg" border="0" /></a></td>';
	if($row["typ"]=='orange') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="orange.jpg" border="0" /></a></td>';
	if($row["typ"]=='blau') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="blau.jpg" border="0" /></a></td>';
	if($row["typ"]=='r1') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="rot1.jpg" border="0" /></a></td>';
	if($row["typ"]=='r2') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="rot2.jpg" border="0" /></a></td>';
	if($row["typ"]=='r3') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="rot3.jpg" border="0" /></a></td>';
	if($row["typ"]=='r4') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="rot4.jpg" border="0" /></a></td>';
	if($row["typ"]=='b1') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="blau1.jpg" border="0" /></a></td>';
	if($row["typ"]=='b2') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="blau2.jpg" border="0" /></a></td>';
	if($row["typ"]=='b3') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="blau3.jpg" border="0" /></a></td>';
	if($row["typ"]=='b4') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="blau4.jpg" border="0" /></a></td>';
	if($row["typ"]=='rs') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="rotstell.jpg" border="0" /></a></td>';
	if($row["typ"]=='bs') echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$_GET["system"],'&dx=',$x,'&dy=',$y,'"><img src="blaustell.jpg" border="0" /></a></td>';
	
	$done=true;
	}
	if(!$done) echo '<td><a href="editsystem.php?pinsel=',$_GET["pinsel"],'&system=',$system->id,'&sx=',$x,'&sy=',$y,'"><img src="weltraum.jpg" border="0" /></a></td>';
	if($x==20) echo '</tr>';
	}
	}
echo '</table>';
echo '<br /><a href="editspace.php?x=',$system->x,'&y=',$system->y,'"><b><font color="green" size="22px">System verlassen</font></b></a>';
echo '<br /><br /><br /><br /><br /><br /><br /><a href="editspace.php?del=',$system->id,'&x=',$system->x,'&y=',$system->y,'"><b><font color="red">System l&ouml;schen!!!!</font></b></a>';
}
include("foot.php");
?>