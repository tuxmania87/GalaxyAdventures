<?php
include("head.php");
include("navlogged.php");
include("klassen.php");
$id=$_SESSION["Id"];
$schiffe=array();
$anzahl=$_POST["anzahl"];



$check=false;

for($i=1;$i<=$anzahl;$i++) {
$tmp='schiff'.$i;
if(isset($_POST[$tmp])) { $check=true; $schiffe[]=$_POST[$tmp]; }
}
if(!$check) echo 'Mindestens 1 Schiff geh&ouml;hrt in eine Flotte!'; else {

$error=false;
for($i=0;$i<sizeof($schiffe);$i++) {
$test=new Schiffe($schiffe[$i]); 
if($i==0) { $x=$test->position->x; $y=$test->position->y; $orbit=$test->position->orbit; $system=$test->position->system->id; }
if($i>0) if($x!=$test->position->x || $y!=$test->position->y || $orbit!=$test->position->orbit || $system!=$test->position->system->id) $error=true;
}

if($error) { echo 'ausgew&auml;hlte Schiffe sind nicht in einem Sektor!<br /><br /><a href="schiffchoice.php">zur&uuml;ck zur Schiffs&uuml;bersicht!</a>'; }
else {
$lastid=checkforlastid('flotte')+1;
$fname=pruefetext($_POST["flottenname"]);
mysql_query("INSERT INTO flotte (id,name,besitzer) VALUES ('$lastid','$fname','$id')");
for($i=0;$i<sizeof($schiffe);$i++) {
$tid=$schiffe[$i];
mysql_query("UPDATE schiffe SET flotte='$lastid' WHERE id='$tid'") OR die(mysql_error());
echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=flotte.php?fid=0">';
}
}
}
echo '<a href="schiffchoice.php">zur&uuml;ck zur Schiffs&uuml;bersicht!</a>';




include("foot.php");
?>
