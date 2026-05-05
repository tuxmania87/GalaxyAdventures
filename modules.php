<?php
include("head.php");
include("navlogged.php");
include("klassen.php");
$id=$_SESSION["Id"];
$pid=$_GET["pid"];
if(isset($pid)) $planet=new Planeten($pid);
$sid=$_POST["sid"];
if(!isset($_POST["sid"])) $sid=$_GET["sid"];
$einbau=$_POST["einbau"];
$ausbau=$_GET["ausbau"];
$modul=new smodul($sid);
$fklasse=new Forschungen($id);
$in=$_POST["in"];

if(isset($sid)) $schiff=new Schiffe($sid);

//CHEAT SCHUTZ
$betray=false;
if(($sid>0 && $schiff->besitzer->id!=$_SESSION["Id"]) || $planet->besitzer->id!=$_SESSION["Id"] || ($sid>0 && $schiff->typ!='s')) $betray=true;
if($betray) echo 'netter Versuch... ( Fehler: 2 )'; else {


//CHEAT SCHUTZ ENDE

//ausbauen
if($ausbau==100 && ( $modul->a1==100 || $modul->a2==100 ))  {
if($modul->a1==100) $modul->a1='-1'; else if($modul->a2==100) $modul->a2='-1';
$schiff->laser-=1; echo 'Modul ausgebaut!';
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET laser=laser-1 WHERE id='$schiff->id'");
}
if($ausbau==101 && $modul->a1==101 && $modul->a2==101) {
$modul->a1='-1'; $modul->a2='-1';
$schiff->maxphaser-=10; echo 'Modul ausgebaut!';
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET maxphaer=maxphaser-10 WHERE id='$schiff->id'");
}

if($ausbau==200 && ( $modul->d1==200 || $modul->d2==200 )) {
if($modul->d1==200) $modul->d1='-1'; else if($modul->d2==200) $modul->d2='-1';
$schiff->maxhull-=2; $schiff->hull=$schiff->hull>$schiff->maxhull?$schiff->maxhull:$schiff->hull; echo 'Modul ausgebaut!';
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET maxhull=maxhull-2,hull='".$schiff->hull."' WHERE id='$schiff->id'");
}
if($ausbau==210 && ( $modul->d1==210 || $modul->d2==210 )) {
if($modul->d1==210) $modul->d1='-1'; else if($modul->d2==210) $modul->d2='-1';
$schiff->maxschilde-=2; $schiff->schilde=$schiff->schilde>$schiff->maxschilde?$schiff->maxschilde:$schiff->schilde; echo 'Modul ausgebaut!';
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET maxschilde=maxschilde-2,schilde='$schiff->schilde' WHERE id='$schiff->id'");
}
if($ausbau==201 && $modul->d1==201 && $modul->d2==201) {
$modul->d1='-1'; $modul->d2='-1'; 
$schiff->maxhull-=5; $schiff->hull=$schiff->hull>$schiff->maxhull?$schiff->maxhull:$schiff->hull; echo 'Modul ausgebaut!';
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET maxhull=maxhull-5,hull='".$schiff->hull."' WHERE id='$schiff->id'");
}
if($ausbau==211 && $modul->d1==211 && $modul->d2==211) {
$modul->d1='-1'; $modul->d2='-1'; 
$schiff->maxschilde-=5; $schiff->schilde=$schiff->schilde>$schiff->maxschilde?$schiff->maxschilde:$schiff->schilde; echo 'Modul ausgebaut!';
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET maxschilde=maxschilde-5,schilde='$schiff->schilde' WHERE id='$schiff->id'");
}

if($ausbau==300 && ($modul->c1==300 || $modul->c2==300)) {
if($modul->c1==300) $modul->c1='-1'; else if($modul->c2==300) $modul->c2='-1';
$schiff->frachtraum->max-=50; echo 'Modul ausgebaut!';
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET lager=lager-50 WHERE id='$schiff->id'");
}
if($ausbau==301 && ($modul->c1==301 || $modul->c2==301)) {
if($modul->c1==301) $modul->c1='-1'; else if($modul->c2==301) $modul->c2='-1';
$schiff->maxgondeln-=10; echo 'Modul ausgebaut!';
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET maxgondeln=maxgondeln-10 WHERE id='$schiff->id'");
}


//modul 100 einbauen -> siehe Modulhandbuch
if($einbau==100)
if($planet->frachtraum->baustoff>=40 && $planet->frachtraum->duranium>=20 && ($modul->a1=='-1' || $modul->a2=='-1')) {
$schiff=new Schiffe($sid);

$planet->frachtraum->baustoff-=40; $planet->frachtraum->duranium-=20;
if($modul->a1=='-1') $modul->a1=$einbau; else if($modul->a2=='-1') $modul->a2=$einbau;
$schiff->laser+=1;
$planet->frachtraum->save(); 
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET laser=laser+1 WHERE id='$schiff->id'");
} else echo 'Nicht gen&uuml;gend Rohstoffe vorhanden!<br />';
//modul 101 einbauen -> siehe Modulhandbuch

if($einbau==101)
if($planet->frachtraum->baustoff>=60 && $planet->frachtraum->duranium>=30 && $modul->a1=='-1' && $modul->a2=='-1') {
$schiff=new Schiffe($sid);

$planet->frachtraum->baustoff-=60; $planet->frachtraum->duranium-=30;
$modul->a1=$einbau;
$modul->a2=$einbau;
$schiff->maxphaser+=10;
$planet->frachtraum->save(); 
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET maxphaser=maxphaser+10 WHERE id='$schiff->id'");
} else echo 'Nicht gen&uuml;gend Rohstoffe vorhanden!<br />';


//modul 200 einbauen -> siehe Modulhandbuch
if($einbau==200)
if($planet->frachtraum->baustoff>=40 && $planet->frachtraum->duranium>=120 && ($modul->d1=='-1' || $modul->d2=='-1')) {
$schiff=new Schiffe($sid);

$planet->frachtraum->baustoff-=40; $planet->frachtraum->duranium-=120;
$schiff->maxhull+=2;
$schiff->hull+=2;
if($modul->d1=='-1') $modul->d1=$einbau; else if($modul->d2=='-1') $modul->d2=$einbau;
$planet->frachtraum->save(); 
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET hull=hull+2,maxhull=maxhull+2 WHERE id='$schiff->id'");
} else echo 'Nicht gen&uuml;gend Rohstoffe vorhanden!<br />';

//Modul 210
if($einbau==210)
if($planet->frachtraum->baustoff>=40 && $planet->frachtraum->duranium>=120 && ($modul->d1=='-1' || $modul->d2=='-1')) {
$schiff=new Schiffe($sid);

$planet->frachtraum->baustoff-=40; $planet->frachtraum->duranium-=120;
$schiff->maxschilde+=2;
$schiff->schilde+=2;
if($modul->d1=='-1') $modul->d1=$einbau; else if($modul->d2=='-1') $modul->d2=$einbau;
$planet->frachtraum->save(); 
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET schilde=schilde+2,maxschilde=maxschilde+2 WHERE id='$schiff->id'");
} else echo 'Nicht gen&uuml;gend Rohstoffe vorhanden!<br />';


//modul 201 einbauen -> siehe Modulhandbuch
if($einbau==201)
if($planet->frachtraum->duranium>=40 && $planet->tritanium>=30 && $modul->d1=='-1' && $modul->d2=='-1') {
$schiff=new Schiffe($sid);

$planet->frachtraum->duranium-=40; $planet->tritanium-=30;
$modul->d1=$einbau;
$modul->d2=$einbau;
$schiff->maxhull+=5;
$schiff->hull+=5;
$planet->frachtraum->save(); 
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET hull=hull+5,maxhull=maxhull+5 WHERE id='$schiff->id'");
} else echo 'Nicht gen&uuml;gend Rohstoffe vorhanden!<br />';

//modul 201 einbauen -> siehe Modulhandbuch
if($einbau==211)
if($planet->frachtraum->baustoff>=80 && $planet->frachtraum->duranium>=40 && $planet->tritanium>=15 && $modul->d1=='-1' && $modul->d2=='-1') {
$schiff=new Schiffe($sid);

$planet->frachtraum->baustoff-=80; $planet->frachtraum->duranium-=40; $planet->tritanium-=15;
$modul->d1=$einbau;
$modul->d2=$einbau;
$schiff->maxschilde+=5;
$schiff->schilde+=5;
$planet->frachtraum->save(); 
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET schilde=schilde+5,maxschilde=maxschilde+5 WHERE id='$schiff->id'");
} else echo 'Nicht gen&uuml;gend Rohstoffe vorhanden!<br />';


//modul 300 einbauen -> siehe Modulhandbuch
if($einbau==300)
if($planet->frachtraum->baustoff>=40 && $planet->frachtraum->duranium>=20 && ($modul->c1=='-1' || $modul->c2=='-1')) {
$schiff=new Schiffe($sid);

$planet->frachtraum->baustoff-=40; $planet->frachtraum->duranium-=20;
if($modul->c1=='-1') $modul->c1=$einbau; else if($modul->c2=='-1') $modul->c2=$einbau;
$schiff->frachtraum->max+=50;
$planet->frachtraum->save();
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET lager=lager+50 WHERE id='$schiff->id'");
} else echo 'Nicht gen&uuml;gend Rohstoffe vorhanden!<br />';
//modul 301 einbauen -> siehe Modulhandbuch
if($einbau==301)
if($planet->frachtraum->baustoff>=20 && $planet->frachtraum->duranium>=10 && $planet->dili>=5 && ($modul->c1=='-1' || $modul->c2=='-1')) {
$schiff=new Schiffe($sid);

$planet->frachtraum->baustoff-=20; $planet->frachtraum->duranium-=10; $planet->dili-=5;
if($modul->c1=='-1') $modul->c1=$einbau; else if($modul->c2=='-1') $modul->c2=$einbau;
$schiff->maxgondeln+=10;
$planet->frachtraum->save(); 
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET maxgondeln=maxgondeln+10,maxhull=maxhull+2 WHERE id='$schiff->id'");
} else echo 'Nicht gen&uuml;gend Rohstoffe vorhanden!<br />';

if($einbau==302)
if($planet->frachtraum->baustoff>=10 && $planet->frachtraum->duranium>=5 && $planet->dili>=1 && $modul->$in=='-1') {
$schiff=new Schiffe($sid);

$planet->frachtraum->baustoff-=10; $planet->frachtraum->duranium-=5; $planet->dili--;
$in=$_POST["in"];
$modul->$in=$einbau;
$schiff->maxgondeln+=10;
$planet->frachtraum->save(); 
$modul->setData(); 
$schiff->setData(); 
} else echo 'Nicht gen&uuml;gend Rohstoffe vorhanden!<br />';

if($einbau==303)
if($planet->frachtraum->baustoff>=40 && $planet->frachtraum->duranium>=20 && $planet->deuterium>=30 && $modul->a1=='-1' && $modul->a2=='-1' && $modul->d1=='-1' && $modul->d2=='-1' ) {
$schiff=new Schiffe($sid);

$planet->frachtraum->baustoff-=200; $planet->frachtraum->duranium-=70;
$in=$_POST["in"];
$modul->a1=$einbau; $modul->a2=$einbau; $modul->d1=$einbau; $modul->d2=$einbau;
$schiff->maxenergie+=8;
$schiff->energieoutput+=1;
$planet->frachtraum->save(); 
$modul->setData(); 
mysqli_query($verbindung, "UPDATE schiffe SET energieoutput=energieoutput+1,maxenergie=maxenergie+8 WHERE id='$schiff->id'");
} else echo 'Nicht gen&uuml;gend Rohstoffe vorhanden!<br />';



if(!isset($sid)) { //schiff waehlen
echo '<h3>Schiff ausw&auml;hlen</h3><form action="modules.php?pid=',$planet->id,'" method="post"><select name="sid">';
$abfrage=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE besitzer='$id' AND ((x='".$planet->position->x."' AND y='".$planet->position->y."' AND system='".$planet->position->system->id."' AND typ='s' AND orbit=1) OR (skillbase=1 AND typ='s'))");
while($row=mysqli_fetch_array($abfrage)) {
$tshp=new Schiffe($abfrage["id"]);
echo '<option value="',$row["id"],'">',$row["name"],' Energie: ',$row["energie"],'/',$row["maxenergie"],'</option>';
}
echo '</select>';
echo '<input type="submit" value="ausw&auml;hlen..." /></form>';
} else {
//Menue

if($modul->a1!='0') { 		//Modul A1
echo '<h3> A - Modul ( Offensivmodul1 1 )</h3>';
echo '<table class="borderedwhite"><tr><td>A-Slot</td>',$modul->a2!=0?'<td>A-Slot</td>':'','</tr><tr><td>'; 
if($modul->a1=='-1') echo '<i>leer</i>'; 		//leer 0
if($modul->a1=='100') echo '<b>+1 Phaser</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=100">ausbauen?</a>';
if($modul->a1=='101') echo '<b>+10 Phasererhitzung</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=101">ausbauen?</a>';
echo '</td>';
if($modul->a2!='0') {
echo '<td>';
if($modul->a2=='-1') echo '<i>leer</i>'; 		//leer 0
if($modul->a2=='100') echo '<b>+1 Phaser</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=100">ausbauen?</a>';
if($modul->a2=='101') echo '<b>+10 Phasererhitzung</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=101">ausbauen?</a>';
echo '</td>';
}
echo '</tr></table><br />';
echo '<h4>Folgende Module kannst du hier einbauen</h4><table class="bordered2"><tr><td>Name</td><td>Baukosten</td><td>Effekt</td><td>Slotbelegung</td></tr>';
//100
if(($modul->a1=='-1' || $modul->a2=='-1') && $fklasse->waffen==1) echo '<tr><form action="modules.php?pid=',$pid,'" method="post"><input type="hidden" name="sid" value="',$sid,'"><input type="hidden" name="einbau" value="100">';
if(($modul->a1=='-1' || $modul->a2=='-1') && $fklasse->waffen==1) echo '<td>Waffen I</td><td>40 Baustoff<br />20 Duranium</td><td>+1 Phaserst&auml;rke</td><td>einen A-Slot<td><input type="submit" value="einbauen"></td></form></tr>';
//101
if($modul->a1=='-1' && $modul->a2=='-1' && $fklasse->waffen2==1) echo '<tr><form action="modules.php?pid=',$pid,'" method="post"><input type="hidden" name="sid" value="',$sid,'"><input type="hidden" name="einbau" value="101">';
if($modul->a1=='-1' && $modul->a2=='-1' && $fklasse->waffen2==1) echo '<td>Waffen II</td><td>60 Baustoff<br />30 Duranium</td><td>+10 Phasererhitzung</td><td>zwei A-Slot<td><input type="submit" value="einbauen"></td></form></tr>';
echo '</table>';

}


if($modul->d1!='0') { 		//Modul D
echo '<br /><br /><h3> D - Modul ( Defensivmodul )</h3>';
echo '<table class="borderedwhite"><tr><td>D-Slot</td>',$modul->d2!=0?'<td>D-Slot</td>':'','</tr><tr><td>'; 
if($modul->d1=='-1') echo '<i>leer</i>'; 		//leer 0
if($modul->d1=='200') echo '<b>+2 H&uuml;llenst&auml;rke</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=200">ausbauen?</a>';
if($modul->d1=='201') echo '<b>+5 H&uuml;llenst&auml;rke</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=201">ausbauen?</a>';
if($modul->d1=='210') echo '<b>+2 Schildst&auml;rke</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=210">ausbauen?</a>';
if($modul->d1=='211') echo '<b>+5 Schildst&auml;rke</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=211">ausbauen?</a>';
echo '</td>';
if($modul->d2!='0') {
echo '<td>';
if($modul->d2=='-1') echo '<i>leer</i>'; 		//leer 0
if($modul->d2=='200') echo '<b>+2 H&uuml;llenst&auml;rke</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=200">ausbauen?</a>';
if($modul->d2=='201') echo '<b>+5 H&uuml;llenst&auml;rke</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=201">ausbauen?</a>';
if($modul->d2=='210') echo '<b>+2 Schildst&auml;rke</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=210">ausbauen?</a>';
if($modul->d2=='211') echo '<b>+5 Schildst&auml;rke</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=211">ausbauen?</a>';
echo '</td>';
}
echo '</tr></table><br />';
echo '<h4>Folgende Module kannst du hier einbauen</h4><table class="bordered2"><tr><td>Name</td><td>Baukosten</td><td>Effekt</td><td>Slotbelegung</td></tr>';
//100
if(($modul->d1=='-1' || $modul->d2=='-1') && $fklasse->hull1==1) echo '<tr><form action="modules.php?pid=',$pid,'" method="post"><input type="hidden" name="sid" value="',$sid,'"><input type="hidden" name="einbau" value="200">';
if(($modul->d1=='-1' || $modul->d2=='-1') && $fklasse->hull1==1) echo '<td>H&uuml;lle I</td><td>40 Baustoff<br />120 Duranium</td><td>+2 H&uuml;llenst&auml;rke</td><td>einen D-Slot<td><input type="submit" value="einbauen"></td></form></tr>';
//210
if(($modul->d1=='-1' || $modul->d2=='-1') && $fklasse->schilde1==1) echo '<tr><form action="modules.php?pid=',$pid,'" method="post"><input type="hidden" name="sid" value="',$sid,'"><input type="hidden" name="einbau" value="210">';
if(($modul->d1=='-1' || $modul->d2=='-1') && $fklasse->schilde1==1) echo '<td>Schilde I</td><td>40 Baustoff<br />120 Duranium</td><td>+2 Schildst&auml;rke</td><td>einen D-Slot<td><input type="submit" value="einbauen"></td></form></tr>';
//101
if($modul->d1=='-1' && $modul->d2=='-1'&& $fklasse->hull2==1) echo '<tr><form action="modules.php?pid=',$pid,'" method="post"><input type="hidden" name="sid" value="',$sid,'"><input type="hidden" name="einbau" value="201">';
if($modul->d1=='-1' && $modul->d2=='-1'&& $fklasse->hull2==1) echo '<td>H&uuml;lle II</td><td>40 Duranium<br />30 Tritanium</td><td>+5 H&uuml;llenst&auml;rke</td><td>zwei D-Slot<td><input type="submit" value="einbauen"></td></form></tr>';
//101
if($modul->d1=='-1' && $modul->d2=='-1'&& $fklasse->schilde2==1) echo '<tr><form action="modules.php?pid=',$pid,'" method="post"><input type="hidden" name="sid" value="',$sid,'"><input type="hidden" name="einbau" value="211">';
if($modul->d1=='-1' && $modul->d2=='-1'&& $fklasse->schilde2==1) echo '<td>Schilde II</td><td>80 Baustoff<br />40 Duranium<br />15 Tritanium</td><td>+5 Schildst&auml;rke</td><td>zwei D-Slot<td><input type="submit" value="einbauen"></td></form></tr>';

echo '</table>';
}


if($modul->c1!='0') { 		//Modul C
echo '<br /><br /><h3> C - Modul ( neutrales Modul )</h3>';
echo '<table class="borderedwhite"><tr><td>C-Slot</td>',$modul->c2!=0?'<td>C-Slot</td>':'','</tr><tr><td>'; 
if($modul->c1=='-1') echo '<i>leer</i>'; 		//leer 0
if($modul->c1=='300') echo '<b>+50 Frachtraum</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=300">ausbauen?</a>';
if($modul->c1=='301') echo '<b>+10 Gondelerhitzung</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=301">ausbauen?</a>';
echo '</td>';
if($modul->c2!='0') {
echo '<td>';
if($modul->c2=='-1') echo '<i>leer</i>'; 		//leer 0
if($modul->c2=='300') echo '<b>+50 Frachtraum</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=300">ausbauen?</a>';
if($modul->c2=='301') echo '<b>+10 Gondelerhitzung</b> <a href="modules.php?sid=',$sid,'&pid=',$pid,'&ausbau=301">ausbauen?</a>';
echo '</td>';
}
echo '</tr></table><br />';
echo '<h4>Folgende Module kannst du hier einbauen</h4><table class="bordered2"><tr><td>Name</td><td>Baukosten</td><td>Effekt</td><td>Slotbelegung</td></tr>';
//100
if($modul->c1=='-1' || $modul->c2=='-1') echo '<tr><form action="modules.php?pid=',$pid,'" method="post"><input type="hidden" name="sid" value="',$sid,'"><input type="hidden" name="einbau" value="300">';
if($modul->c1=='-1' || $modul->c2=='-1') echo '<td>Frachtraum I</td><td>40 Baustoff<br />20 Duranium</td><td>+50 Frachtraum</td><td>einen C-Slot<td><input type="submit" value="einbauen"></td></form></tr>';
//101
if($modul->c1=='-1' || $modul->c2=='-1') echo '<tr><form action="modules.php?pid=',$pid,'" method="post"><input type="hidden" name="sid" value="',$sid,'"><input type="hidden" name="einbau" value="301">';
if($modul->c1=='-1' || $modul->c2=='-1') echo '<td>Antrieb I</td><td>20 Baustoff<br />10 Duranium<br />5 Dilithium</td><td>+10 Gondelerhitzung</td><td>einen C-Slot<td><input type="submit" value="einbauen"></td></form></tr>';
echo '</table>';
}




}

}
include("foot.php");
?>
