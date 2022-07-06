<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

$account=new Account($_SESSION["Id"]);
if(!$account->mapper) die("Error: Insufficient Access Level");

$pinsel=explode("-",$_GET["pinsel"]);
$klasse=$pinsel[0];
$typ=$pinsel[1];


if(($_GET["x"])=='' || $_GET["y"]=='') 
	{
	$sx=15; $sy=15;
	} else
	{
	$sx=$_GET["x"]; $sy=$_GET["y"];
	}

if(ctype_digit($_GET["del"]) && $_GET["del"]>0) {
	$schiffgefunen=false;
	$planetengefunden=false;
	$abfrage=mysql_query("SELECT * FROM schiffe WHERE besitzer!=2 AND system='".$_GET["del"]."'");
	while($row=mysql_fetch_array($abfrage))
	$schiffgefunden=true;
	$abfrage=mysql_query("SELECT * FROM schiffe WHERE besitzer!=2 AND system='".$_GET["del"]."'");
	while($row=mysql_fetch_array($abfrage))
	$schiffgefunden=true;
	if($schiffgefunden) echo "Hinweis: Es sind noch schiffe im System. Es kann nicht gel&ouml;scht werden!";
	if($planetgefunden) echo "Hinweis: Es sind noch besidelte planeten im System. Es kann nicht gel&ouml;scht werden!";
	if(!$schiffgefunden && !$planetgefunden) {
				mysql_query("DELETE FROM weltraum WHERE system='".$_GET["del"]."'") or die(mysql_error());
				mysql_query("DELETE FROM schiffe WHERE system='".$_GET["del"]."'");
				mysql_query("DELETE FROM planeten WHERE system='".$_GET["del"]."'");
				mysql_query("DELETE FROM systeme WHERE id='".$_GET["del"]."'");
				}
	}

if(ctype_digit($_GET["dx"]) && $_GET["dx"]>-25 && ctype_digit($_GET["dy"]) && $_GET["dy"]>-25)
	{		//loeschen
	mysql_query("DELETE FROM planeten WHERE x='".$_GET["dx"]."' AND y='".$_GET["dy"]."' AND system='0'");
	mysql_query("DELETE FROM weltraum WHERE x='".$_GET["dx"]."' AND y='".$_GET["dy"]."' AND system='0'");
	}


if(ctype_digit($_GET["rx"]) && $_GET["rx"]>-25 && ctype_digit($_GET["ry"]) && $_GET["ry"]>-25 && $_POST["operation"]>=0 && isset($_POST["operation"]))
	{
	$systeme=array("bblaublau","bblaugelb","bblauorange","bblaurot","bblauschwarz","bblauweiss","blau","blaubig","blaublau","brotblau","brotgelb","brotorange","brotrot","brotschwarz","brotweiss","gelb","gelbblau","gelbgelb","gelbweiss","orange","orangegelb","orangeorange","orangeweiss","rot","rotbig","rotblau","rotgelb","rotorange","rotrot","rotweiss","weiss","weissblau");
	$bildinsert=$systeme[$_POST["operation"]];
		$lastid=checkforlastid("systeme")+1; 
		mysql_query("INSERT INTO systeme (id,x,y,name,bild) VALUES ('$lastid','".$_GET["rx"]."','".$_GET["ry"]."','".$_POST["sysname"]."','".$bildinsert.".jpg')") or die(mysql_error());
	}

	
	
if(ctype_digit($_GET["sx"]) && $_GET["sx"]>-25 && ctype_digit($_GET["sy"]) && $_GET["sy"]>-25)
	{
	if($_GET["pinsel"]=='') echo '<font color="red"><b>Bitte erst w&auml;hlen!</b></font><br />';
	//WELTALL
	if($klasse=='W') {
	switch ($typ) {
	case "d": $dovar=0; break;
	case "dk": $dovar=1; break;
	case "e": $dovar=2;  break;
	case "ek": $dovar=3;  break;
	case "x": $dovar=4;  break;
	case "b": $dovar=5;  break;
	case "g": $dovar=6;  break;
	case "p": $dovar=7;  break;
	case "radio": $dovar=8;  break;
	case "metrion": $dovar=9;  break;
	case "lim": $dovar=10;  break;
	default: $dovar=-1; break;
	}
	switch ($dovar) {
	case 0: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','d','0')"); break;
		case 1: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','dk','0')"); break;
		case 2: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','e','0')"); break;
		case 3: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','ek','0')"); break;
		case 4: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','x','0')"); break;
		case 5: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','b','0')"); break;
		case 6: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','g','0')"); break;
		case 7: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','p','0')"); break;
		case 8: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','radio','0')"); break;
		case 9: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','metrion','0')"); break;
		case 10: mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('".$_GET["sx"]."','".$_GET["sy"]."','lim','0')"); break;
		default: echo 'not yet implemented!'; break;
	}
	}
	if($klasse=='S') {
	//	0			1			2			3				4			5
	$systeme=array("bblaublau","bblaugelb","bblauorange","bblaurot","bblauschwarz","bblauweiss","blau","blaubig","blaublau","brotblau","brotgelb","brotorange","brotrot","brotschwarz","brotweiss","gelb","gelbblau","gelbgelb","gelbweiss","orange","orangegelb","orangeorange","orangeweiss","rot","rotbig","rotblau","rotgelb","rotorange","rotrot","rotweiss","weiss","weissblau");
	for($i=0;$i<sizeof($bild);$i++)
	echo '<a href="editspace2.php?x=',$_GET["x"],'&y=',$_GET["y"],'&rx=',$_GET["sx"],'&ry=',$_GET["sy"],'&o=',$i,'"><img height="16px" width="16px" src="',$bild[$i],'" border="0" /></a><br />';
	for($i=0;$i<sizeof($systeme);$i++)
		{
		echo '<form method="post" action="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&rx=',$_GET["sx"],'&ry=',$_GET["sy"],'"><input type="hidden" name="operation" value="',$i,'">';
		echo '<img height="16px" width="16px" src="',$systeme[$i],'.jpg" border="0" /> Name des Systems: <input type="text" name="sysname" /> <input type="submit" value="erstellen" /></form>';
		}
	}
}

if($klasse!='S' || !(ctype_digit($_GET["sx"]) && $_GET["sx"]>-25 && ctype_digit($_GET["sy"]) && $_GET["sy"]>-25)) {
//KEIN PINSEL
if(!isset($_GET["pinsel"]) || $_GET["pinsel"]=='') {
	echo 'Du hast nichts ausgew&auml;hlt. Bitte <a class="thickbox" href="editspacetool2.php?height=470&x=',$_GET["x"],'&y=',$_GET["y"],'"> >HIER< </a> ausw&auml;hlen!<br />';
	}
if($klasse=='W')
	{
	echo '<div style="width:400px;border:2px solid red;">';
	switch ($typ) {
	case "d": $dovar=0; echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="deut.jpg" border="0" /> - dichtes Deuteriumfeld</a><br />'; break;
	case "dk": $dovar=1;echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="deutklein.jpg" border="0" /> - d&uuml;nnes Deuteriumfeld</a><br />'; break;
	case "e": $dovar=2; echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="erz.jpg" border="0" /> - dichtes Asteroidenfeld</a><br />'; break;
	case "ek": $dovar=3; echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="erzklein.jpg" border="0" /> - d&uuml;nnes Asteroidenfeld</a><br />'; break;
	case "x": $dovar=4; echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="schwarzesloch.jpg" border="0" /> - schwarzes Loch</a><br />'; break;
	case "b": $dovar=5; echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="nebel.jpg" border="0" /> - Ceru Nebel</a><br />'; break;
	case "g": $dovar=6; echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="green.jpg" border="0" /> - Meta Nebel</a><br />'; break;
	case "p": $dovar=7; echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="pulsar.jpg" border="0" /> - gravi. Verzerrung</a><br />'; break;
	case "radio": $dovar=8; echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="nebelgelb.jpg" border="0" /> - radioaktiver Nebel</a><br />'; break;
	case "metrion": $dovar=9; echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="metrion.jpg" border="0" /> - Metriongasnebel</a><br />'; break;
	case "lim": $dovar=10; echo '<a class="thickbox" href="editspacetool2.php?x=',$_GET["x"],'&y=',$_GET["y"],'"><img height="16px" width="16px" src="limes.jpg" border="0" /> - Begrenzungsnebel</a><br />'; break;
	default: $dovar=-1; break;
	}
	echo '</div>';
}
	

	
echo '<br /><hr /><br /><table>';
for($y=$sy-15-1;$y<=$sy+15;$y++)
	for($x=$sx-15-1;$x<=$sx+15;$x++)
		{
		if($x==$sx-15-1) echo '<tr><td>',$y,'</td>';
		if($y==$sy-15-1 && $x>$sx-15-1) echo '<td><center>',$x-1,'</td>';
	if($y>$sy-15-1) {
	$done=false;
	$abfrage=mysql_query("SELECT * FROM weltraum WHERE system='0' AND x='$x' AND y='$y'");
	while($row=mysql_fetch_array($abfrage))
	{
	if($row["typ"]=='b') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="nebel.jpg" border="0" /></a></td>';
	if($row["typ"]=='e') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="erz.jpg" border="0" /></a></td>';
	if($row["typ"]=='ek') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="erzklein.jpg" border="0" /></a></td>';
	if($row["typ"]=='g') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="green.jpg" border="0" /></a></td>';
	if($row["typ"]=='d') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="deut.jpg" border="0" /></a></td>';
	if($row["typ"]=='dk') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="deutklein.jpg" border="0" /></a></td>';
	if($row["typ"]=='metrion') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="metrion.jpg" border="0" /></a></td>';
	if($row["typ"]=='radio') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="nebelgelb.jpg" border="0" /></a></td>';
	if($row["typ"]=='x') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="schwarzesloch.jpg" border="0" /></a></td>';
	if($row["typ"]=='p') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="pulsar.jpg" border="0" /></a></td>';
	if($row["typ"]=='lim') echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&dx=',$x,'&dy=',$y,'"><img height="16px" width="16px" src="limes.jpg" border="0" /></a></td>';
	$done=true;
	}
	$abfrage=mysql_query("SELECT * FROM systeme WHERE x='$x' AND y='$y'");
	while($row=mysql_fetch_array($abfrage))
	{
	echo '<td><a href="editsystem.php?system=',$row["id"],'"><img height="16px" width="16px" src="',$row["bild"],'" border="0" /></a></td>';
	$done=true;
	}
	if(!$done) {
		if($x<-15 || $y<-15) echo '<td><img height="16px" width="16px" src="weltraum.jpg" style="border:1px solid red;" /></td>';
		else echo '<td><a href="editspace2.php?pinsel=',$_GET["pinsel"],'&x=',$_GET["x"],'&y=',$_GET["y"],'&sx=',$x,'&sy=',$y,'"><img height="16px" width="16px" src="weltraum.jpg" border="0" /></a></td>';
		}
	if($x==$sx+15) echo '</tr>';
	}
}
echo '</table>';
echo '<br /><form action="editspace2.php" method="get"><input type="hidden" name="pinsel" value="',$_GET["pinsel"],'" /><input type="text" size="2" name="x" /> - <input type="text" size="2" name="y" /><br /><input type="submit" value="einstellen"></form>';
}
?>
