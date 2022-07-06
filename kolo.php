<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

$id=$_SESSION["Id"];
$sid=$_GET["sid"];
$schiff=new Schiffe($sid);

$ich=new Account($_SESSION["Id"]);

//CHEATSCHUTZ ANFANG
if($ich->level<=3) die("Du kannst erst mit Level 4 Kolonisieren!");

$betray=false;
$testid=$_GET["sid"];
if(!isset($testid)) $testid=$_GET["pid"];
if(!ctype_digit($_GET["sid"])) $betray=true;
if($schiff->typ!='s') $betray=true;
$tmp=mysql_query("SELECT besitzer FROM schiffe WHERE id='$testid'");
while($testtmp=mysql_fetch_array($tmp))
if($_SESSION["Id"] != $testtmp["besitzer"]) $betray=true;

if($betray) { echo 'Du bist nicht eingeloggt oder du versucht auf fremde Accounts zuzugreifen...'; } else {

//CHEATSCHUTZ ENDE

//ALT

$test=mysql_query("SELECT * FROM planeten WHERE besitzer=2 AND x='".$schiff->position->x."' AND y='".$schiff->position->y."' AND system='".$schiff->position->system->id."'");
while($t1=mysql_fetch_array($test))
{
if($schiff->skill->erz==1) {

$tpl=new Planeten($t1["id"]);

$check1=0;
$testplanet=mysql_query("SELECT COUNT(*) FROM planeten WHERE typ NOT LIKE '_m' AND besitzer='$id'");
$testmond=mysql_query("SELECT COUNT(*) FROM planeten WHERE typ LIKE '_m' AND besitzer='$id'");

$planetc=mysql_fetch_array($testplanet);
$mondc=mysql_fetch_array($testmond);

$planetc=$planetc[0];
$mondc=$mondc[0];

//Mondbegrenzung
$mond1 = mysql_num_rows(mysql_query("SELECT * FROM erfolge E,quests Q WHERE Q.id=E.qid AND E.erledigt=2 AND E.qid=38 AND E.uid='".$_SESSION["Id"]."'")) >= 1 ? true: false ;
$mond2 = mysql_num_rows(mysql_query("SELECT * FROM erfolge E,quests Q WHERE Q.id=E.qid AND E.erledigt=2 AND E.qid=40 AND E.uid='".$_SESSION["Id"]."'")) >= 1 ? true: false ;

$grenzemond = $mond1?1:0 + $mond2?1:0;


//Planetenlimit
if($ich->level==5) $grenzplanet=4;
$grenzplanet += ($mond1?1:0 + $mond2?1:0);

if($ich->level==4) $grenzplanet=3;


if($planetc >= $grenzplanet && $tpl->typ[1]!='m') die("Du kannst nur maximal ".$grenzplanet." Planeten besiedeln!");
if($mondc >= $grenzemond && $tpl->typ[1]=='m') die("Du kannst nur maximal ".$grenzemond." Monde besiedeln!");

// SCHUTZ ENDE

//echo ($t1["typ"]=='l') ,'  - ', $check1<=4 ,'  - ', $schiff->energie>=10 ,'  - ', $schiff->frachtraum->baustoff>=200 ,' -  ', $schiff->frachtraum->duranium>=100;

if($schiff->energie>=10 && $schiff->frachtraum->baustoff>=200 && $schiff->frachtraum->duranium>=100) { $pid=$t1["id"]; mysql_query("UPDATE planeten SET besitzer='$id',baustoff=200,duranium=100 WHERE id='$pid' AND besitzer=2"); $done=1; $schiff->frachtraum->baustoff-=200; $schiff->frachtraum->duranium-=100; $schiff->energie-=10; }
/*if($t1["typ"]=='l' && $check1<=4 && $schiff->energie>=10 && $schiff->frachtraum->baustoff>=200 && $schiff->frachtraum->duranium>=100) { $pid=$t1["id"]; mysql_query("UPDATE planeten SET besitzer='$id',baustoff=200,duranium=100 WHERE id='$pid'"); $done=1; $schiff->frachtraum->baustoff-=200; $schiff->frachtraum->duranium-=100; $schiff->energie-=10; }
if($t1["typ"]=='i' && $check1<=4 && $schiff->energie>=10 && $schiff->frachtraum->baustoff>=200 && $schiff->frachtraum->duranium>=100) { $pid=$t1["id"]; mysql_query("UPDATE planeten SET besitzer='$id',baustoff=200,duranium=100 WHERE id='$pid'"); $done=1; $schiff->frachtraum->baustoff-=200; $schiff->frachtraum->duranium-=100; $schiff->energie-=10; }
if($t1["typ"]=='z' && $check1<=4 && $schiff->energie>=10 && $schiff->frachtraum->baustoff>=200 && $schiff->frachtraum->duranium>=100) { $pid=$t1["id"]; mysql_query("UPDATE planeten SET besitzer='$id',baustoff=200,duranium=100 WHERE id='$pid'"); $done=1; $schiff->frachtraum->baustoff-=200; $schiff->frachtraum->duranium-=100; $schiff->energie-=10; }
*/

$schiff->frachtraum->save();
//Einbauen der basiskuppel
$basisfeld=array("4","5","13","18","26","27","29","31","33","34","39","40","43","45","46","48","49");
$basisfeld2=array("1","2","3","4","8","9","10","12","15","21","23","24","26","28","33","34","35","37","38","40","41","42","43","45","46","47","48","49","50");
$basisfeld3=array("3","4","10","14","15","18","22","24","26","27","31","34","37","43","44","45");
$basisfeld4=array("1","3","4","6","7","8","9","10","11","12","13","14","16","18","19","20","21","22","23","25","26","27","30","31","33","34","36","38","39","40","41","42","45","47","48","50");
$rand_wert=rand(0,sizeof($basisfeld));
$rand_wert2=rand(0,sizeof($basisfeld2));
$rand_wert3=rand(0,sizeof($basisfeld3));
$rand_wert4=rand(0,sizeof($basisfeld4));
if(isset($pid)) {
$plan=new Planeten($pid);

if($plan->typ=='m') $feld_wert=$basisfeld[$rand_wert];
if($plan->typ=='z') $feld_wert=$basisfeld2[$rand_wert2];
if($plan->typ=='l') $feld_wert=$basisfeld3[$rand_wert3];
if($plan->typ=='i') $feld_wert=$basisfeld4[$rand_wert4];
//echo '<br /><br />Vardump2: ',$feld_wert,'<br />'; var_dump($plan->feld[$feld_wert]);
if(strpos($plan->typ,"om")===false && strpos($plan->typ,"lm")===false && strpos($plan->typ,"mm")===false) {
$plan->feld[$feld_wert]->was=30;
$plan->feld[$feld_wert]->bauzeit=0;
$plan->feld[$feld_wert]->aktiv=1;
$plan->feld[$feld_wert]->hull=70;
$plan->feld[$feld_wert]->save();
}
}
//einbauen der basiskuppel ende
if($done==1) { echo 'Planet erfolgreich besiedelt! 200 Baustoff und 100 Duranium wurden auf Planeten übertragen'; } else { echo 'Du brauchst zum besiedeln 200 Baustoff,100 Duranium und mindestens 10 Energie'; }
echo '<br /><br /><a href="planetchoice.php">Planeten&uuml;bersicht</a>';
}
}


}
include("foot.php");
?>