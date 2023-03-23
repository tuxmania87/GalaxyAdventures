<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

$ich = new Account($_SESSION["Id"]);

$verbindung = get_verbindung();

//questpruefeung : Sammeln ( Typ 2)

print "SELECT erfolge.anzahl,quests.zusatz,quests.max,erfolge.id FROM erfolge,quests WHERE uid='" . $_SESSION["Id"] . "' AND quests.id=erfolge.qid AND quests.typ=2 AND erledigt='0' LIMIT 1";

$abfrage = mysqli_query($verbindung, "SELECT erfolge.anzahl,quests.zusatz,quests.max,erfolge.id FROM erfolge,quests WHERE uid='" . $_SESSION["Id"] . "' AND quests.id=erfolge.qid AND quests.typ='2' AND erledigt='0' LIMIT 1");
while ($row = mysqli_fetch_assoc($abfrage)) {
	$max = $row["max"];
	$saveid = $row["id"];
	$art = $row["zusatz"];
	$anzahl = $row["anzahl"];
}

if ($saveid > 0) {
	/*
	$testab = mysqli_query($verbindung, "SELECT SUM(" . $art . ") FROM schiffe WHERE besitzer='" . $ich->id . "'");
	$testaa = mysqli_query($verbindung, "SELECT SUM(" . $art . ") FROM planeten WHERE besitzer='" . $ich->id . "'");
	$testab = mysqli_fetch_array($testab);
	$testaa = mysqli_fetch_array($testaa);
	*/

	$testsum = 0;

	$testab = mysqli_query($verbindung, "select id from schiffe where besitzer = '".$ich->id."'");
	while($row = mysqli_fetch_assoc($testab)) {
		$ship_id = $row["id"];

		$t_s = new Schiffe($ship_id);
		for($i=0; $i<sizeof($t_s->frachtraum->fracht);$i++) {
			if(strtolower($t_s->frachtraum->fracht[$i]->name) == $art) {
				$testsum += $t_s->frachtraum->fracht[$i]->anzahl;
			}
		}
	}


	$testab = mysqli_query($verbindung, "select id from planeten where besitzer = '".$ich->id."'");
	while($row = mysqli_fetch_assoc($testab)) {
		$planet_id = $row["id"];

		$t_s = new Planeten($planet_id);
		for($i=0; $i<sizeof($t_s->frachtraum->fracht);$i++) {
			if(strtolower($t_s->frachtraum->fracht[$i]->name) == $art) {
				$testsum += $t_s->frachtraum->fracht[$i]->anzahl;
			}
		}
	}

	$summe = $testsum;;
	if ($summe < 0) $summe = 0;
	if ($summe > $max) {
		$summe = $max;
		$neuq = new Quest($saveid);
		$neuq->anzahl = $summe;
		$neuq->done();
	}
	mysqli_query($verbindung, "UPDATE erfolge SET anzahl='" . $summe . "' WHERE id='" . $saveid . "'");
}
//reseten der abschlusswerte
unset($max);
unset($saveid);
unset($art);
unset($anzahl);



//questpruefeung : Buildings ( Typ 4)
$abfrage = mysqli_query($verbindung, "SELECT erfolge.anzahl,quests.zusatz,quests.max,erfolge.id FROM erfolge,quests WHERE uid='" . $_SESSION["Id"] . "' AND quests.id=erfolge.qid AND quests.typ=4 AND erledigt='0'");
while ($row = mysqli_fetch_assoc($abfrage)) {
	$menge = $row["max"];
	$saveid = $row["id"];
	$gebaude = $row["zusatz"];
	$anzahl = $row["anzahl"];
}
if ($saveid > 0) {
	$bcount = 0;
	$sumfeld = array();
	$abfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE besitzer='" . $_SESSION["Id"] . "'");
	while ($row = mysqli_fetch_array($abfrage)) {
		$gefunden = false;
		$planet = new Planeten($row["id"]);
		for ($i = 1; $i <= 50; $i++) if ($planet->feld[$i]->was == $gebaude && $planet->feld[$i]->bauzeit == 0) {
			$gefunden = true;
			$bcount++;
		}
		$sumfeld[] = $gefunden ? '1' : '0';
	}
	$count = 0;
	for ($i = 0; $i < sizeof($sumfeld); $i++) $count++;
	//mysqli_query("UPDATE erfolge SET anzahl='$count' WHERE id='$saveid'");


	//if($count>=$menge && $bcount>=$anzahl+$menge) mysqli_query("UPDATE erfolge SET anzahl='$menge',erledigt=1 WHERE erledigt=0 AND id='$saveid'");
	if ($bcount >= $menge) mysqli_query($verbindung, "UPDATE erfolge SET anzahl='$menge',erledigt=1 WHERE erledigt=0 AND id='$saveid'");
}
unset($max);
unset($saveid);
unset($art);
unset($anzahl);



//questpruefeung : Items ( Typ 6)
$abfrage = mysqli_query($verbindung, "SELECT erfolge.anzahl,quests.zusatz,quests.max,erfolge.id FROM erfolge,quests WHERE uid='" . $_SESSION["Id"] . "' AND quests.id=erfolge.qid AND quests.typ='4' AND erledigt='0'");
while ($row = mysqli_fetch_assoc($abfrage)) {
	$menge = $row["max"];
	$saveid = $row["id"];
	$gebaude = $row["zusatz"];
	$anzahl = $row["anzahl"];
}
if ($saveid > 0) {
	$bcount = 0;
	$sumfeld = array();
	$abfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE besitzer='" . $_SESSION["Id"] . "'");
	while ($row = mysqli_fetch_array($abfrage)) {
		$gefunden = false;
		$planet = new Planeten($row["id"]);
		for ($i = 1; $i <= 50; $i++) if ($planet->feld[$i]->was == $gebaude && $planet->feld[$i]->bauzeit == 0) {
			$gefunden = true;
			$bcount++;
		}
		$sumfeld[] = $gefunden ? '1' : '0';
	}
	$count = 0;
	for ($i = 0; $i < sizeof($sumfeld); $i++) $count++;
	//mysqli_query("UPDATE erfolge SET anzahl='$count' WHERE id='$saveid'");


	if ($count >= $menge && $bcount >= $anzahl + $menge) mysqli_query($verbindung, "UPDATE erfolge SET anzahl='$menge',erledigt=1 WHERE erledigt=0 AND id='$saveid'");
}

unset($max);
unset($saveid);
unset($art);
unset($anzahl);


//levelup berechnung
$questcounter = 0;
$questcounter2 = 0;
$questcounter3 = 0;
$questcounter4 = 0;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=1")) == 1)
	$questcounter++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=2")) == 1)
	$questcounter++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=5")) == 1)
	$questcounter++;
//2
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=6")) == 1)
	$questcounter2++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=7")) == 1)
	$questcounter2++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=8")) == 1)
	$questcounter2++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=11")) == 1)
	$questcounter2++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=13")) == 1)
	$questcounter2++;
//3
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=20")) == 1)
	$questcounter3++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=21")) == 1)
	$questcounter3++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=22")) == 1)
	$questcounter3++;
//4
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=14")) == 1)
	$questcounter4++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=15")) == 1)
	$questcounter4++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=16")) == 1)
	$questcounter4++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=17")) == 1)
	$questcounter4++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=18")) == 1)
	$questcounter4++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=23")) == 1)
	$questcounter4++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=24")) == 1)
	$questcounter4++;
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='" . $ich->id . "' AND erledigt=2 AND qid=26")) == 1)
	$questcounter4++;
if ($_GET["levelup"] == 1 && $questcounter == 3 && $ich->level == 1) {
	$ich->level++;
	mysqli_query($verbindung, "UPDATE account SET level=2 WHERE id='" . $ich->id . "'");
	echo 'Level aufgestiegen!<br />';
	mysqli_query($verbindung, "UPDATE planeten SET energie=maxenergie WHERE besitzer='" . $ich->id . "'");
	$planetenid = mysqli_query($verbindung,"SELECT id FROM planeten WHERE besitzer='" . $ich->id . "' AND heimat=1");
	$planetenid = mysqli_fetch_array($planetenid);
	$planetenid = $planetenid[0];
	$myplanet = new Planeten($planetenid);
	//frachter
	$lastid = checkforlastid('schiffe') + 1;
	$klasse = 'Erzfrachter';
	//echo "INSERT INTO schiffe (id,typ,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT '$lastid','s','noname','".$myplanet->position->x."','".$myplanet->position->y."','".$myplanet->position->system->id."','".$ich->id."',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'',lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'1' FROM bauplan WHERE klasse='$klasse'";
	mysqli_query($verbindung,"INSERT INTO schiffe (id,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT '$lastid','noname','" . $myplanet->position->x . "','" . $myplanet->position->y . "','" . $myplanet->position->system->id . "','" . $ich->id . "',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'s',lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'1' FROM bauplan WHERE klasse='$klasse'");
	mysqli_query($verbindung,"INSERT INTO schiffsmodule (sid,c1,c2) VALUES ('$lastid','-1','-1')");
	//tanker
	$lastid = checkforlastid('schiffe') + 1;
	$klasse = 'Tanker';
	mysqli_query($verbindung,"INSERT INTO schiffe (id,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT '$lastid','noname','" . $myplanet->position->x . "','" . $myplanet->position->y . "','" . $myplanet->position->system->id . "','" . $ich->id . "',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'s',lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'1' FROM bauplan WHERE klasse='$klasse'");
	mysqli_query($verbindung,"INSERT INTO schiffsmodule (sid,c1) VALUES ('$lastid','-1')");
}

if ($_GET["levelup"] == 2 && $questcounter2 == 5 && $ich->level == 2) {
	$ich->level++;
	mysqli_query($verbindung,"UPDATE account SET level=3 WHERE id='" . $ich->id . "'");
	echo 'Level aufgestiegen!<br />';
	mysqli_query($verbindung,"UPDATE planeten SET energie=maxenergie WHERE besitzer='" . $ich->id . "'");
	$planetenid = mysqli_query($verbindung,"SELECT id FROM planeten WHERE besitzer='" . $ich->id . "' AND heimat=1");
	$planetenid = mysqli_fetch_array($planetenid);
	$planetenid = $planetenid[0];
	$myplanet = new Planeten($planetenid);
	//frachter
	$lastid = checkforlastid('schiffe') + 1;
	$klasse = 'Erzfrachter';
	//echo "INSERT INTO schiffe (id,typ,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT '$lastid','s','noname','".$myplanet->position->x."','".$myplanet->position->y."','".$myplanet->position->system->id."','".$ich->id."',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'',lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'1' FROM bauplan WHERE klasse='$klasse'";
	mysqli_query($verbindung,"INSERT INTO schiffe (id,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT '$lastid','noname','" . $myplanet->position->x . "','" . $myplanet->position->y . "','" . $myplanet->position->system->id . "','" . $ich->id . "',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'s',lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'1' FROM bauplan WHERE klasse='$klasse'");
	mysqli_query($verbindung,"INSERT INTO schiffsmodule (sid,c1,c2) VALUES ('$lastid','-1','-1')");
	//tanker
	$lastid = checkforlastid('schiffe') + 1;
	$klasse = 'Tanker';
	mysqli_query($verbindung,"INSERT INTO schiffe (id,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT '$lastid','noname','" . $myplanet->position->x . "','" . $myplanet->position->y . "','" . $myplanet->position->system->id . "','" . $ich->id . "',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'s',lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'1' FROM bauplan WHERE klasse='$klasse'");
	mysqli_query($verbindung,"INSERT INTO schiffsmodule (sid,c1) VALUES ('$lastid','-1')");
}
if ($_GET["levelup"] == 3 && $questcounter3 == 3 && $ich->level == 3) {
	$ich->level = 4;
	mysqli_query($verbindung,"UPDATE account SET level=4 WHERE id='" . $ich->id . "'");
	echo 'Level aufgestiegen!<br />';
	mysqli_query($verbindung,"UPDATE planeten SET energie=maxenergie WHERE besitzer='" . $ich->id . "'");
	$planetenid = mysqli_query($verbindung,"SELECT id FROM planeten WHERE besitzer='" . $ich->id . "' AND heimat=1");
	$planetenid = mysqli_fetch_array($planetenid);
	$planetenid = $planetenid[0];
	$myplanet = new Planeten($planetenid);
	//frachter
	$lastid = checkforlastid('schiffe') + 1;
	$klasse = 'Sonde';
	//echo "INSERT INTO schiffe (id,typ,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT '$lastid','s','noname','".$myplanet->position->x."','".$myplanet->position->y."','".$myplanet->position->system->id."','".$ich->id."',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'',lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'1' FROM bauplan WHERE klasse='$klasse'";
	mysqli_query($verbindung,"INSERT INTO schiffe (id,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT '$lastid','noname','" . $myplanet->position->x . "','" . $myplanet->position->y . "','" . $myplanet->position->system->id . "','" . $ich->id . "',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'s',lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'1' FROM bauplan WHERE klasse='$klasse'");
}

if ($_GET["levelup"] == 4 && $questcounter4 == 8 && $ich->level == 4) {
	$ich->level = 5;
	mysqli_query($verbindung,"UPDATE account SET level=5 WHERE id='" . $ich->id . "'");
	echo 'Level aufgestiegen!<br />';
	mysqli_query($verbindung,"UPDATE planeten SET energie=maxenergie WHERE besitzer='" . $ich->id . "'");
	$planetenid = mysqli_query("SELECT id FROM planeten WHERE besitzer='" . $ich->id . "' AND heimat=1");
	$planetenid = mysqli_fetch_array($planetenid);
	$planetenid = $planetenid[0];
	$myplanet = new Planeten($planetenid);
	//frachter
	$lastid = checkforlastid('schiffe') + 1;
	$klasse = 'Sonde';
	//echo "INSERT INTO schiffe (id,typ,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT '$lastid','s','noname','".$myplanet->position->x."','".$myplanet->position->y."','".$myplanet->position->system->id."','".$ich->id."',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'',lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'1' FROM bauplan WHERE klasse='$klasse'";
	mysqli_query($verbindung,"INSERT INTO schiffe (id,name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,orbit) SELECT '$lastid','noname','" . $myplanet->position->x . "','" . $myplanet->position->y . "','" . $myplanet->position->system->id . "','" . $ich->id . "',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,'s',lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,'1' FROM bauplan WHERE klasse='$klasse'");
}

//Levelup
echo '<div class="smallbox"><h2>Kolonistenlevel: ', $ich->level, '</h2>';
if ($ich->level == 1) {	//Fortschrittsnezige

	if ($questcounter == 3) echo '<span style="font-weight:bold;color:silver;">';
	echo 'erledigte Quests: ', $questcounter, '/3';
	if ($questcounter == 3) echo ' (fertig)</span><br /><br /><a href="showquest.php?levelup=1"><img width="32px" src="plus.png" border="0" /><font color="#7CEF0A"><b>! Level aufsteigen !</b></font><img src="plus.png" width="32px" border="0" /></a><br />';
	if ($questcounter == 3) echo '<br /><span class="uberschrift">Levelbelohnung:</span><br /><br />- volle Energie auf Planeten<br />- einen Tanker ( <img src="images/tanker.png" border="0" /> )<br />- einen Frachter ( <img src="images/frachter.png" border="0" /> )!<br />';
}
if ($ich->level == 2) {	//Fortschrittsnezige

	if ($questcounter2 == 5) echo '<span style="font-weight:bold;color:silver;">';
	echo 'erledigte Quests: ', $questcounter2, '/5';
	if ($questcounter2 == 5) echo ' (fertig)</span><br /><br /><a href="showquest.php?levelup=2"><img width="32px" src="plus.png" border="0" /><font color="#7CEF0A"><b>! Level aufsteigen !</b></font><img src="plus.png" width="32px" border="0" /></a><br />';
	if ($questcounter2 == 5) echo '<br /><span class="uberschrift">Levelbelohnung:</span><br /><br />- volle Energie auf Planeten<br />- einen Tanker ( <img src="images/tanker.png" border="0" /> )<br />- einen Frachter ( <img src="images/frachter.png" border="0" /> )!<br />';
}
if ($ich->level == 3) {	//Fortschrittsnezige

	if ($questcounter3 == 3) echo '<span style="font-weight:bold;color:silver;">';
	echo 'erledigte Quests: ', $questcounter3, '/3';
	if ($questcounter3 == 3) echo ' (fertig)</span><br /><br /><a href="showquest.php?levelup=3"><img width="32px" src="plus.png" border="0" /><font color="#7CEF0A"><b>! Level aufsteigen !</b></font><img src="plus.png" width="32px" border="0" /></a><br />';
	if ($questcounter3 == 3) echo '<br /><span class="uberschrift">Levelbelohnung:</span><br /><br />- volle Energie auf Planeten<br />- eine Sonde ( <img src="test.gif" border="0" /> )!<br />';
}
if ($ich->level == 4) {	//Fortschrittsnezige

	if ($questcounter4 == 8) echo '<span style="font-weight:bold;color:silver;">';
	echo 'erledigte Quests: ', $questcounter4, '/8';
	if ($questcounter4 == 8) echo ' (fertig)</span><br /><br /><a href="showquest.php?levelup=4"><img width="32px" src="plus.png" border="0" /><font color="#7CEF0A"><b>! Level aufsteigen !</b></font><img src="plus.png" width="32px" border="0" /></a><br />';
	if ($questcounter4 == 8) echo '<br /><span class="uberschrift">Levelbelohnung:</span><br /><br />- volle Energie auf Planeten<br />- eine Sonde ( <img src="test.gif" border="0" /> )!<br />';
}
echo '</div><br /><h2><u>Quest &Uuml;bersicht</u></h2><br />';
echo '<h3><a href="quest.php?sid=0">Link: prim&auml;re Level Quests</a></h3><br />';
echo '<div class="smallbox"><h3>offene Quests</h3>';

$abfrage = mysqli_query($verbindung,"SELECT erfolge.id,quests.titel FROM erfolge,quests WHERE uid='" . $_SESSION["Id"] . "' AND erledigt='0' AND erfolge.qid=quests.id");
while ($row = mysqli_fetch_assoc($abfrage)) {
	echo '<a href="questdetail.php?id=', $row["id"], '&width=400" class="thickbox" title="Quest">- ', $row["titel"], '</a><br />';
}
echo '</div><br /><div class="smallbox"><h3>erledigte Quests </h3> ';

$abfrage = mysqli_query($verbindung,"SELECT erfolge.id,quests.titel FROM erfolge,quests WHERE uid='" . $_SESSION["Id"] . "' AND erledigt='1' AND erfolge.qid=quests.id");
if (mysqli_num_rows($abfrage) > 0) echo ' <span style="color:yellow;font-weight:bold;">  ACHTUNG: Quests m&uuml;ssen noch abgegeben werden!</span><br />';
while ($row = mysqli_fetch_assoc($abfrage)) {
	echo '<br /><a href="questdetail.php?id=', $row["id"], '&width=400" class="thickbox" title="Quest">- ', $row["titel"], '</a>';
}

echo '</div><br /><br />';
include("foot.php");
