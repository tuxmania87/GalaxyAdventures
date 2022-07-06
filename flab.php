<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

//CHEATSCHUTZ ANFANG

$pid=$_GET["pid"];
$fid=$_GET["fid"];
$do=$_POST["do"];


$betray=false;;
if(!ctype_digit($pid)) $betray=true;
if(!ctype_digit($fid)) $betray=true;


if($betray) { echo 'Du bist nicht eingeloggt oder du versucht auf fremde Accounts zuzugreifen...'; } else {

//CHEATSCHUTZ ENDE


$id=$_SESSION["Id"];

$forschung=new Forschungen($id);

$aktPlanet=new Planeten($pid);

if($_GET["do"]=='cyk') {
$continue=true;
if($aktPlanet->feld[$fid]->aktiv==1 && $continue) { $aktPlanet->feld[$fid]->aktiv=0; $aktPlanet->feld[$fid]->aktiv=0; $continue=false; }
if($aktPlanet->feld[$fid]->aktiv==0 && $continue) { $aktPlanet->feld[$fid]->aktiv=0; $aktPlanet->feld[$fid]->aktiv=1; $continue=false; }
$aktPlanet->feld[$fid]->save();


}

if($do==2) { // waffen1
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=10) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=10;


$forschung->waffen=4;
$forschung->save();

echo 'Waffen I wird erforscht!<br />';
} else echo 'Nicht genug Geld oder Energie!<br />';
}

if($do==3) { // waffen2
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=30) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=30;


$forschung->waffen2=7;
$forschung->save();

echo 'Waffen II wird erforscht!<br />';
} else echo 'Nicht genug Geld oder Energie!<br />';
}

if($do==4) { // hull1
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=10) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=10;

$forschung->hull1=4;
$forschung->save();

echo 'H&uuml;lle I wird erforscht!<br />';
} else echo 'Nicht genug Geld oder Energie!<br />';
}

if($do==5) { // hull2
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=30) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=10;


$forschung->hull2=7;
$forschung->save();

echo 'H&uuml;lle II wird erforscht!<br />';
} else echo 'Nicht genug Geld oder Energie!<br />';
}




if($do==6) { // fracht 1
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=10) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=10;


$forschung->fracht1=4;
$forschung->save();

echo 'Frachtraum I wird erforscht!<br />';
} else echo 'Nicht genug Geld oder Energie!<br />';
}



if($do==20) { // waffen
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=10) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=10;

$forschung->schilde1=4;
$forschung->save();

echo 'Schilde I wird erforscht!<br />';
} else echo 'Nicht genug Geld oder Energie!<br />';
}

if($do==21) { // waffen
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=30) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=30;

$forschung->schilde2=7;
$forschung->save();

echo 'Schilde II wird erforscht!<br />';
} else echo 'Nicht genug Geld oder Energie!<br />';
}

if($do==24) { // waffen
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=25) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=25;


$forschung->torpedo1=7;
$forschung->save();

echo 'Torpedo I wird erforscht!<br />';
} else echo 'Nicht genug Geld oder Energie!<br />';
}

if($do==23) { // doppel
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=15) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=15;

$forschung->doppel=6;
$forschung->save();

echo 'Doppelbeschleungier wird erforscht!<br />';
} else echo 'Nicht genug Geld oder Energie!<br />';
}


if($do==8) { // terra1
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=20) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=20;

$forschung->terra1=4;
$forschung->save();

echo 'Terraforming I wird erforscht<br />';
} else echo 'Nicht genug Iso-Chips(20) oder Energie!<br />';
}

if($do==9) { // terra2
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=30) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=30;

$forschung->terra2=4;
$forschung->save();

echo 'Terraforming II wird erforscht<br />';
} else echo 'Nicht genug Geld oder Energie!<br />';
}

if($do==10) { //Miranda
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=35) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=35;

$forschung->miranda=11;
$forschung->save();

echo 'Schiff(Miranda) wird erforscht<br />';
} else echo 'Nicht genug ISO-Chips(35) oder Energie!<br />';
}

if($do==11 && $forschung->miranda==1) { //Miranda
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=60) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=60;

$forschung->consti=11;
$forschung->save();

echo 'Schiff(Constitution) wird erforscht<br />';
} else echo 'Nicht genug ISO-Chips(60) oder Energie!<br />';
}

if($do==25) { //Miranda
if($aktPlanet->energie>=75 && $aktPlanet->frachtraum->isochips>=20) {
$aktPlanet->energie-=75;
$aktPlanet->frachtraum->isochips-=20;

$forschung->horchposten=6;
$forschung->save();

echo 'Station(Horchposten) wird erforscht<br />';
} else echo 'Nicht genug ISO-Chips(20) oder Energie!<br />';
}

$aktPlanet->frachtraum->save();
mysql_query("UPDATE planeten SET energie='".$aktPlanet->energie."' WHERE id='".$aktPlanet->id."'");

echo 'ISO-Chips: ',$aktPlanet->frachtraum->isochips,'<br />Energie: ',$aktPlanet->energie,'<br />';
echo '<h3>Forschung</h3><table class="bordered2"><tr><td>Forschungsmodul</td><td>Kosten</td><td>Effekt</td></tr>';


if($forschung->waffen==0) {
echo '<tr><td>Waffen I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Phaserst&auml;rke um 1</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="2"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->waffen>1) {
echo '<tr><td>Waffen I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Phaserst&auml;rke um 1</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->waffen-1,' Runden)</span></td></tr>'; }
if($forschung->waffen==1) {
echo '<tr><td>Waffen I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Phaserst&auml;rke um 1</td><td><span style="color:green;">erforscht</span></td></tr>'; }



if($forschung->waffen==1 && $forschung->waffen2==0) {
echo '<tr><td>Waffen II</td><td>30 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Phasererhitzung um 10</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="3"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->waffen==1 && $forschung->waffen2>1) {
echo '<tr><td>Waffen II</td><td>30 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Phasererhitzung um 10</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->waffen2-1,' Runden)</span></td></tr>'; }
if($forschung->waffen==1 && $forschung->waffen2==1) {
echo '<tr><td>Waffen II</td><td>30 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Phasererhitzung um 10</td><td><span style="color:green;">erforscht</span></td></tr>'; }

if($forschung->hull1==0) {
echo '<tr><td>H&uuml;lle I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die H&uuml;llenst&auml;rke um 2</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="4"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->hull1>1) {
echo '<tr><td>H&uuml;lle I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die H&uuml;llenst&auml;rke um 2</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->hull1-1,' Runden)</span></td></tr>'; }
if($forschung->hull1==1) {
echo '<tr><td>H&uuml;lle I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die H&uuml;llenst&auml;rke um 2</td><td><span style="color:green;">erforscht</span></td></tr>'; }

if($forschung->hull1==1 && $forschung->hull2==0) {
echo '<tr><td>H&uuml;lle II</td><td>30 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die H&uuml;llenst&auml;rke um 6</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="5"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->hull1==1 && $forschung->hull2>1) {
echo '<tr><td>H&uuml;lle II</td><td>30 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die H&uuml;llenst&auml;rke um 6</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->hull2-1,' Runden)</span></td></tr>'; }
if($forschung->hull1==1 && $forschung->hull2==1) {
echo '<tr><td>H&uuml;lle II</td><td>30 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die H&uuml;llenst&auml;rke um 6</td><td><span style="color:green;">erforscht</span></td></tr>'; }


if($forschung->fracht1==0) {
echo '<tr><td>fracht I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Lagerkapazit&auml;t umd 50</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="6"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->fracht1>1) {
echo '<tr><td>fracht I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Lagerkapazit&auml;t umd 50</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->fracht1-1,' Runden)</span></td></tr>'; }
if($forschung->fracht1==1) {
echo '<tr><td>fracht I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Lagerkapazit&auml;t umd 50</td><td><span style="color:green;">erforscht</span></td></tr>'; }

//terra1
if($forschung->terra1==0) {
echo '<tr><td>Terraforming I</td><td>20 Iso-Chips<br />75 Energie<br />Bauzeit: 3</td><td>Brennt einen Wald ab und wandelt ihn in eine Wiese um!</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="8"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->terra1>1) {
echo '<tr><td>Terraforming I</td><td>20 Iso-Chips<br />75 Energie<br />Bauzeit: 3</td><td>Brennt einen Wald ab und wandelt ihn in eine Wiese um!</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->terra1-1,' Runden)</span></td></tr>'; }
if($forschung->terra1==1) {
echo '<tr><td>Terraforming I</td><td>20 Iso-Chips<br />75 Energie<br />Bauzeit: 3</td><td>Brennt einen Wald ab und wandelt ihn in eine Wiese um!</td><td><span style="color:green;">erforscht</span></td></tr>'; }
//terra2
if($forschung->terra1==1 && $forschung->terra2==0) {
echo '<tr><td>Terraforming II</td><td>30 Iso-Chips<br />75 Energie<br />Bauzeit: 3</td><td>Sprengt einen Berg. Regen f&uuml;hrt dazu, dass sich ein Wasserbecken bildet</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="9"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->terra1==1 && $forschung->terra2>1) {
echo '<tr><td>Terraforming II</td><td>30 Iso-Chips<br />75 Energie<br />Bauzeit: 3</td><td>Sprengt einen Berg. Regen f&uuml;hrt dazu, dass sich ein Wasserbecken bildet</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->terra2-1,' Runden)</span></td></tr>'; }
if($forschung->terra1==1 && $forschung->terra2==1) {
echo '<tr><td>Terraforming II</td><td>30 Iso-Chips<br />75 Energie<br />Bauzeit: 3</td><td>Sprengt einen Berg. Regen f&uuml;hrt dazu, dass sich ein Wasserbecken bildet</td><td><span style="color:green;">erforscht</span></td></tr>'; }
//horchposten
if($forschung->horchposten==0) {
echo '<tr><td>Horchposten<br /><img src="images/horchposten.png" border="0" /></td><td>20 ISO-Chips<br />75 Energie<br />Bauzeit: 5</td><td>Wenn erforscht, kann Horchposten gebaut werden!</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="25"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->horchposten>1) {
echo '<tr><td>Horchposten<br /><img src="images/horchposten.png" border="0" /></td><td>20 ISO-Chips<br />75 Energie<br />Bauzeit: 5</td><td>Wenn erforscht, kann Horchposten gebaut werden!</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->horchposten-1,' Runden)</span></td></tr>'; }
if($forschung->horchposten==1) {
echo '<tr><td>Horchposten<br /><img src="images/horchposten.png" border="0" /></td><td>20 ISO-Chips<br />75 Energie<br />Bauzeit: 5</td><td>Wenn erforscht, kann Horchposten gebaut werden!</td><td><span style="color:green;">erforscht</span></td></tr>'; }

//miranda
//if($forschung->miranda==0) {
//echo '<tr><td>Miranda<br /><img src="images/miranda.png" border="0" /></td><td>35 ISO-Chips<br />75 Energie<br />Bauzeit: 10</td><td>Wenn erforscht, kann die Miranda Klasse gebaut werden!</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="10"><td><input type="submit" value="forschen"></td></form></tr>'; }
//if($forschung->miranda>1) {
//echo '<tr><td>Miranda<br /><img src="images/miranda.png" border="0" /></td><td>35 ISO-Chips<br />75 Energie<br />Bauzeit: 10</td><td>Wenn erforscht, kann die Miranda Klasse gebaut werden!</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->miranda-1,' Runden)</span></td></tr>'; }
if($forschung->miranda==1) {
echo '<tr><td>Miranda<br /><img src="images/miranda.png" border="0" /></td><td>35 ISO-Chips<br />75 Energie<br />Bauzeit: 10</td><td>Wenn erforscht, kann die Miranda Klasse gebaut werden!</td><td><span style="color:green;">erforscht</span></td></tr>'; }
//miranda
//if($forschung->consti==0 && $forschung->miranda==1) {
//echo '<tr><td>Constitution<br /><img src="images/constitution.png" border="0" /></td><td>60 ISO-Chips<br />75 Energie<br />Bauzeit: 10</td><td>Wenn erforscht, kann die Constitution Klasse gebaut werden!</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="11"><td><input type="submit" value="forschen"></td></form></tr>'; }
//if($forschung->consti>1 && $forschung->miranda==1) {
//echo '<tr><td>Constitution<br /><img src="images/constitution.png" border="0" /></td><td>60 ISO-Chips<br />75 Energie<br />Bauzeit: 10</td><td>Wenn erforscht, kann die Constitution Klasse gebaut werden!</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->consti-1,' Runden)</span></td></tr>'; }
if($forschung->consti==1 && $forschung->miranda==1) {
echo '<tr><td>Constitution<br /><img src="images/constitution.png" border="0" /></td><td>60 ISO-Chips<br />75 Energie<br />Bauzeit: 10</td><td>Wenn erforscht, kann die Constitution Klasse gebaut werden!</td><td><span style="color:green;">erforscht</span></td></tr>'; }

if($forschung->schilde1==0) {
echo '<tr><td>Schilde I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Schildst&auml;rke um 2</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="20"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->schilde1>1) {
echo '<tr><td>Schilde I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Schildst&auml;rke um 2</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->schilde1-1,' Runden)</span></td></tr>'; }
if($forschung->schilde1==1) {
echo '<tr><td>Schilde I</td><td>10 Isochips<br />75 Energie<br />Bauzeit: 3</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Schildst&auml;rke um 2</td><td><span style="color:green;">erforscht</span></td></tr>'; }

if($forschung->schilde1==1 && $forschung->schilde2==0) {
echo '<tr><td>Schilde II</td><td>30 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Schildst&auml;rke um 4</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="21"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->schilde1==1 && $forschung->schilde2>1) {
echo '<tr><td>Schilde II</td><td>30 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Schildst&auml;rke um 4</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->schilde2-1,' Runden)</span></td></tr>'; }
if($forschung->schilde1==1 && $forschung->schilde2==1) {
echo '<tr><td>Schilde II</td><td>30 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Modul f&uuml;r Schiffe: erh&ouml;ht dauerhaft die Schildst&auml;rke um 4</td><td><span style="color:green;">erforscht</span></td></tr>'; }

if($forschung->doppel==0) {
echo '<tr><td>Doppelbeschleuniger</td><td>15 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Erm&ouml;glicht Bau von Doppelbeschleuniger -20 E/+2 Antimaterie</td><form action="flab.php?pid=',$pid,'&fid=',$fid,'" method="post"><input type="hidden" name="do" value="23"><td><input type="submit" value="forschen"></td></form></tr>'; }
if($forschung->doppel>1) {
echo '<tr><td>Doppelbeschleuniger</td><td>15 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Erm&ouml;glicht Bau von Doppelbeschleuniger -20 E/+2 Antimaterie</td><td><span style="color:yellow;">wird erforscht... (noch ',$forschung->doppel-1,' Runden)</span></td></tr>'; }
if($forschung->doppel==1) {
echo '<tr><td>Doppelbeschleuniger</td><td>15 Isochips<br />75 Energie<br />Bauzeit: 6</td><td>Erm&ouml;glicht Bau von Doppelbeschleuniger -20 E/+2 Antimaterie</td><td><span style="color:green;">erforscht</span></td></tr>'; }


echo '</table><br /><hr />';
echo 'Dieses Forschungslabor verbraucht pro Tick <span style="color:red;">2</span> Antimaterie,<span style="color:red;">10</span> Baustoff,<span style="color:red;">15</span> Deuterium und <span style="color:red;">1</span> Dilithium und produziert dabei <span style="color:green;">1</span> Isochip<br /><hr />';
echo '<br />Das Geb&auml;de ist ';
if($aktPlanet->feld[$fid]->aktiv==1) echo '<font color="green"><b>aktiviert</b></font>.&nbsp;&nbsp;<a href="flab.php?pid=',$pid,'&fid=',$fid,'&do=cyk">deaktivieren?</a><br />';
if($aktPlanet->feld[$fid]->aktiv==0) echo '<font color="red"><b>deaktiviert</b></font>.&nbsp;&nbsp;<a href="flab.php?pid=',$pid,'&fid=',$fid,'&do=cyk">aktivieren?</a><br />';
echo 'Wenn du das Forschungslabor abreisst erh&auml;lst du 100 Baustoff und 75 Duranium zur&uuml;ck!<br />';
echo '<br /><form action="destroy.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(1)"><input type="hidden" name="del" value="10"><input type="submit" value="abreissen"></form>';
}

echo '<br /><a href="planet.php?pid=',$pid,'">zur&uuml;ck zum Planeten</a>';

include("foot.php");
?>

