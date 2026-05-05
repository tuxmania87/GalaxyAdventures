<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");
//CHEATSCHUTZ ANFANG


$betray=false;
$testid=$_GET["sid"];
if(!isset($testid)) $testid=$_GET["pid"];
$tmp=mysqli_query($verbindung, "SELECT besitzer FROM schiffe WHERE id='$testid'");
while($testtmp=mysqli_fetch_array($tmp))
if($_COOKIE["Id"] != $testtmp["besitzer"]) $betray=true;

if($betray && $testid > 0 ) { echo 'Du bist nicht eingeloggt oder du versucht auf fremde Accounts zuzugreifen...'; } else {

//CHEATSCHUTZ ENDE

$do=$_POST["do"];
$pid=$_GET["pid"];
$aktPlanet=new Planet;
$aktPlanet->getData($pid);

if($do==2) {	//werft bauen!
if($aktPlanet->rohstoffa>=200 && $aktPlanet->rohstoffb>=40) { $aktPlanet->werft=-8; $aktPlanet->rohstoffa-=200; $aktPlanet->rohstoffb-=40;
echo 'Werft in Bau! Bauzeit: 8.<br />'; $aktPlanet->setData($aktPlanet->id);
} else echo 'Nicht genug Rohstoffe vorhanden!<br />';
}

if($do==3) { //namen aendern
$newname=pruefetext($_POST["newname"]);
$aktPlanet->name=$newname;
$aktPlanet->setData($aktPlanet->id);
$aktPlanet->getData($aktPlanet->id);
}

if($do==4) {	//flab bauen!
if($aktPlanet->rohstoffa>=500 && $aktPlanet->rohstoffb>=60) { $aktPlanet->forschung=-10; $aktPlanet->rohstoffa-=500; $aktPlanet->rohstoffb-=60;
echo 'Forschungslabor in Bau! Bauzeit: 10.<br />'; $aktPlanet->setData($aktPlanet->id);
} else echo 'Nicht genug Rohstoffe vorhanden!<br />';
}

if($do==5) {	//schildturm bauen!
if($aktPlanet->rohstoffa>=100 && $aktPlanet->rohstoffb>=50) { $aktPlanet->sturm=-4; $aktPlanet->rohstoffa-=100; $aktPlanet->rohstoffb-=50;
echo 'Schildturm in Bau! Bauzeit: 4.<br />'; $aktPlanet->setData($aktPlanet->id);
} else echo 'Nicht genug Rohstoffe vorhanden!<br />';
}

if($do==6) { // schilde aufladen
$amount=$_POST["samount"];
if($amount > $aktPlanet->energie) { $amount = $aktPlanet->energie; echo 'Hinweis: Geforderter Betrag nicht verf&uuml;gbar. Werte werden an Maximalenergie angepasst<br />'; }
if($amount > ($aktPlanet->maxschilde-$aktPlanet->schilde)) { $amount = $aktPlanet->maxschilde-$aktPlanet->schilde; echo 'Hinweis: Schilde k&ouml;nnen nicht &uuml;berladen werden. Werte werden an Maximalwert angepasst!'; }
if($amount < 0) { $amount = 0; echo 'Fehler: Negativer Betrag kann nicht aufgeladen werden!'; }
$aktPlanet->energie-=$amount;
$aktPlanet->schilde+=$amount;
$aktPlanet->setData($sid);
}

if($_GET["do"]==7)  // schilde aktivieren
if($aktPlanet->schildstatus==0)
if($aktPlanet->energie>0)
{
$aktPlanet->schildstatus=1;
$aktPlanet->energie--;
}
else
echo 'Fehler: Energie reicht nicht aus um Schilde zu aktivieren!';
else $aktPlanet->schildstatus=0;

if($do==10) { // lager ausbauen
if($aktPlanet->rohstoffa>=100 && $aktPlanet->rohstoffb>=60) {
$aktPlanet->rohstoffa-=100;
$aktPlanet->rohstoffb-=60;
$aktPlanet->lagerbau=3;
} else echo 'Nicht genug Rohstoffe!<br />';
}

$aktPlanet->setData($aktPlanet->id);
$aktPlanet->getData($aktPlanet->id);


$gesamtA=0;
$gesamtB=0;
//bodyteil
?>



<?php
$id=$_COOKIE["Id"];
echo '<h3>Planetname :',$aktPlanet->name,'</h3>Energie: ',$aktPlanet->energie,'/',$aktPlanet->maxenergie,' (+',$aktPlanet->energieoutput,')<br />';
echo '<table border="1"><tr><td>Nr.</td><td>Modulname</td><td>Erzeugt A</td><td>Erzeugt B</td><td>..</td></tr>';
//erforscht?
$abfrageForschung=mysqli_query($verbindung, "SELECT * FROM erforscht WHERE besitzer='$id'");
	while($erforscht=mysqli_fetch_array($abfrageForschung))
		{
		$zusatz=$erforscht["addrmodul"];
		}
//abfrage
for($i=1;$i<=(3+$zusatz);$i++) {
$tmpvar = "modul";
$tmpvar .= $i;
echo '<tr><td>Modul ',$i,'</td><td>';
$checked=false;
$abfrage=mysqli_query($verbindung, "SELECT * FROM `$tmpvar` WHERE pid='$pid'");
while($planet=mysqli_fetch_array($abfrage))
	{
$tmptyp=$planet["modultyp"];
$checked=true;
//modulausgabe  
if($tmptyp[3]=='A') 
echo 'Rohstoff A-Fabrik ';
if($tmptyp[3]=='B')
echo 'Rohstoff B-Fabrik ';
if($planet["bauzeit"] > 0) echo '(Bauzeit: ',$planet["bauzeit"],')';
echo '</td><td>';
// }
$istufe=($tmptyp[5]=='')?$tmptyp[4]:$tmptyp[4].$tmptyp[5];
$imodul=$tmptyp[0].$tmptyp[1].$tmptyp[2].$tmptyp[3];
if($tmptyp[3]=='A' && $planet["bauzeit"] == 0) { echo output($imodul,$istufe); $gesamtA+=output($imodul,$istufe); }
if($tmptyp[3]=='A' && $planet["bauzeit"] > 0) { echo output($imodul,$istufe-1); $gesamtA+=output($imodul,$istufe-1); }
echo '</td><td>';
if($tmptyp[3]=='B' && $planet["bauzeit"] == 0) { echo output($imodul,$istufe); $gesamtB+=output($imodul,$istufe); }
if($tmptyp[3]=='B' && $planet["bauzeit"] > 0) { echo output($imodul,$istufe-1); $gesamtB+=output($imodul,$istufe-1); }

	}
if(!$checked)
	{ //nichts installiert
	echo 'kein Modul installiert!</td><td>-</td><td>-</td>';
	}
echo '</td><td><a href="build.php?pid=',$pid,'&modul=',$i,'">Modul installieren</a></td></tr>';
}
echo '<tr><td></td><td></td><td><span style="color:green;font-weight:bold;">',$gesamtA,'</span></td><td><span style="color:green;font-weight:bold;">',$gesamtB,'</span></td><td></td></tr></table>';

if($aktPlanet->werft>0) {
$gesamtE=0;
//Orbitmodule
echo '<br /><table border="1"><tr><td>Nr.</td><td>Modulname</td><td>Erzeugt Energie</td><td>..</td></tr>';
//erforscht?
$abfrageForschung=mysqli_query($verbindung, "SELECT * FROM erforscht WHERE besitzer='$id'");
	while($erforscht=mysqli_fetch_array($abfrageForschung))
		{
		$zusatz=$erforscht["addomodul"];
		}
//abfrage
for($i=1;$i<=(2+$zusatz);$i++) {
$tmpvar = "orbit";
$tmpvar .= $i;
echo '<tr><td>Orbitmodul ',$i,'</td><td>';
$checked=false;
$abfrage=mysqli_query($verbindung, "SELECT * FROM `$tmpvar` WHERE pid='$pid'");
while($planet=mysqli_fetch_array($abfrage))
	{
$tmptyp=$planet["modultyp"];
$checked=true;
//modulausgabe  
echo 'Solarsatellit';
if($planet["bauzeit"] > 0) echo '(Bauzeit: ',$planet["bauzeit"],')';
echo '</td><td>';
// }
$istufe=($tmptyp[6]=='')?$tmptyp[5]:$tmptyp[5].$tmptyp[6];
$imodul=$tmptyp[0].$tmptyp[1].$tmptyp[2].$tmptyp[3].$tmptyp[4];
if($planet["bauzeit"] == 0) { echo output($imodul,$istufe); $gesamtE+=output($imodul,$istufe); }
else echo '-';
	}
if(!$checked)
	{ //nichts installiert
	echo 'kein Modul installiert!</td><td>-</td>';
	}
echo '</td><td><a href="buildorbit.php?pid=',$pid,'&modul=',$i,'">Modul installieren</a></td></tr>';
}
echo '<tr><td></td><td></td><td><span style="color:green;font-weight:bold;">',$gesamtE,'</span></td><td></td></tr></table>';

//Ende Orbitmodule
}

echo '<table class="bordered">';

//Lagerausbau
if($aktPlanet->lagerbau==0) {
echo '<form action="planet.php?pid=',$aktPlanet->id,'" method="post"><input type="hidden" name="do" value="10"><tr><td>Lager</td><td><h3>Baukosten</h3><ul><li>100 Rohstoff A</li><li>60 Rohstoff B</li><li>Bauzeit: 3</li></ul></td><td>Effekte:<br /><span style="color:green;">+7000 Lagerplatz</span><br /><span style="color:green;">+200 Energielager</span><br /><input type="submit" value="Lager ausbauen"></td></tr></form>';
}

if($aktPlanet->lagerbau>0) {
echo '<tr><td>Lager</td><td> - </td><td><span style="color:yellow;">wird ausgebaut... (noch ',$aktPlanet->lagerbau,' Runden)</td></tr></form>';
}

//Werftanzeige
if($aktPlanet->werft==0) {
echo '<form action="planet.php?pid=',$aktPlanet->id,'" method="post"><input type="hidden" name="do" value="2"><tr><td>keine Werft vorhanden!</td><td><h3>Baukosten</h3><ul><li>200 Rohstoff A</li><li>40 Rohstoff B</li><li>Bauzeit: 8</li></ul></td><td><input type="submit" value="werft bauen"></td></tr></form>';
}
if($aktPlanet->werft<0) {
echo '<tr><td><span style="color:yellow;">Werft wird gebaut: Fertig in: ',$aktPlanet->werft*-1,' Runden</a></td><td>-</td><td>-</td></tr>';
}
if($aktPlanet->werft>0) {
echo '<tr><td><span style="font-weight:bold;">Werft</td><td><span style="color:green;">bereit</span></td><td><a href="createship.php?pid=',$aktPlanet->id,'">Schiffe bauen</a><br /><a href="modules.php?pid=',$aktPlanet->id,'">Module einbauen</a></td></tr>';
}
//werftanzeige ende
echo '<br />';
//Forschungslab
if($aktPlanet->forschung==0) {
echo '<form action="planet.php?pid=',$aktPlanet->id,'" method="post"><input type="hidden" name="do" value="4"><tr><td>kein Forschungslabor vorhanden!</td><td><h3>Baukosten</h3><ul><li>500 Rohstoff A</li><li>60 Rohstoff B</li><li>Bauzeit: 10</li></ul></td><td><input type="submit" value="Forschungslabor bauen"></td></tr></form>';
}
if($aktPlanet->forschung<0) {
echo '<tr><td><span style="color:yellow;">Forschungslabor wird gebaut: Fertig in: ',$aktPlanet->forschung*-1,' Runden</a></td><td>-</td><td>-</td></tr>';
}
if($aktPlanet->forschung>0) {
echo '<tr><td><span style="font-weight:bold;">Forschungslabor</td><td><span style="color:green;">bereit</span></td><td><a href="flab.php?pid=',$aktPlanet->id,'">zum Labor</a></td></tr>';
}
//Forschung
echo '<br />';
//Schildturm
if($aktPlanet->sturm==0) {
echo '<form action="planet.php?pid=',$aktPlanet->id,'" method="post"><input type="hidden" name="do" value="5"><tr><td>kein Schildturm vorhanden!</td><td><h3>Baukosten</h3><ul><li>100 Rohstoff A</li><li>50 Rohstoff B</li><li>Bauzeit: 4</li></ul></td><td><input type="submit" value="Schildturm bauen"></td></tr></form>';
}
if($aktPlanet->sturm<0) {
echo '<tr><td><span style="color:yellow;">Schildturm wird gebaut: Fertig in: ',$aktPlanet->sturm*-1,' Runden</a></td><td>-</td><td>-</td></tr>';
}
if($aktPlanet->sturm>0) {
echo '<form action="planet.php?pid=',$aktPlanet->id,'" method="post"><input type="hidden" name="do" value="6"><tr><td><span style="font-weight:bold;">Schilde</td><td>';
echo $aktPlanet->schildstatus==0?'<span>':'<span style="color:yellow;">';
echo $aktPlanet->schilde,'/',$aktPlanet->maxschilde,'</span> (<a href="planet.php?pid=',$aktPlanet->id,'&do=7">';
echo $aktPlanet->schildstatus==0?'aktivieren':'deaktivieren';
echo ')</td><td><input type="text" size="6" name="samount" /><br /><input type="submit" value="aufladen" /></td></tr></form>';
}
//Ende Schuldturm


echo '</table><br />Rohstoff A: ',$aktPlanet->rohstoffa,'<br />Rohstoff B: ',$aktPlanet->rohstoffb,'<br />Deuterium: ',$aktPlanet->deuterium;
echo '<br /><br />belegt ',$aktPlanet->rohstoffa+$aktPlanet->rohstoffb+$aktPlanet->deuterium,' von ',$aktPlanet->lager,' ( ',floor((($aktPlanet->rohstoffa+$aktPlanet->rohstoffb+$aktPlanet->deuterium)/$aktPlanet->lager)*100),'% )<hr /><form action="planet.php?pid=',$aktPlanet->id,'" method="POST" ><input name="newname" type="text" value="',$aktPlanet->name,'" /><input type="hidden" name="do" value="3" />&nbsp;<input type="submit" value="Namen aendern"></form>';


}
include("foot.php");
?>
