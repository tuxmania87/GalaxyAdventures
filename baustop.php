<?php

include("head.php");
include("navlogged.php");
include("klassen.php");
//CHEATSCHUTZ ANFANG


$betray=false;
$testid=$_GET["sid"];
if(!isset($testid)) $testid=$_GET["pid"];
if(!ctype_digit($_GET["pid"]) || !ctype_digit($_GET["fid"])) die("Fehler: ID ung&uuml;tig");


//CHEATSCHUTZ ENDE

$pid=$_GET["pid"];
$fid=$_GET["fid"];

$planet=new Planeten($pid);
if($planet->besitzer->id!=$_SESSION["Id"]) die("Fehler: Besitzer-ID ung&uuml;tig");


if($_POST["del"]==1)  //Baufabrik abreissen
if($planet->feld[$fid]->was==1 && $planet->feld[$fid]->bauzeit>0) {
if($planet->frachtraum->gesamt()+20>$planet->frachtraum->max) if($planet->frachtraum->max-$planet->frachtraum->baustoff-$planet->frachtraum->duranium-$planet->frachtraum->erz-$planet->frachtraum->deuterium>=0) $amount=$planet->frachtraum->max-$planet->frachtraum->baustoff-$planet->frachtraum->duranium-$planet->frachtraum->erz-$planet->frachtraum->deuterium;
else $amount=0; else $amount=20;
$planet->frachtraum->baustoff+=$amount;
}

if($_POST["del"]==2)  //Lager abreissen
if($planet->feld[$fid]->was==2 && $planet->feld[$fid]->bauzeit>0) {
if($planet->frachtraum->gesamt()+10>$planet->frachtraum->max) $amount=0; else $amount=10;
$planet->frachtraum->baustoff+=$amount;
}

if($_POST["del"]==3)  //Solarstation abreissen
if($planet->feld[$fid]->was==3 && $planet->feld[$fid]->bauzeit>0) {
if($planet->frachtraum->gesamt()+20>$planet->frachtraum->max) $amount=0; else $amount=20;
$planet->frachtraum->baustoff+=$amount;
}

if($_POST["del"]==4)  //Werft
if($planet->feld[$fid]->was==4 && $planet->feld[$fid]->bauzeit>0) {
if($planet->frachtraum->gesamt()+150>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=100; $amountB=50; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;
}

if($_POST["del"]==5)  //Plasma
if($planet->feld[$fid]->was==5 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+255>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=130; $amountB=125; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;

}

if($_POST["del"]==6 || $_POST["del"]==16)  //Schildturm
if(($planet->feld[$fid]->was==6 || $planet->feld[$fid]->was==16) && b>0) {

if($planet->frachtraum->gesamt()+210>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=130; $amountB=80; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;

}

if($_POST["del"]==7)  //Wasserwerk
if($planet->feld[$fid]->was==7 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+40>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=30; $amountB=10; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;

}

if($_POST["del"]==8)  //Mine
if($planet->feld[$fid]->was==8 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+20>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=20; $amountB=0; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;

}

if($_POST["del"]==9)  //frachtraum->duranium
if($planet->feld[$fid]->was==9 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+30>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=30; $amountB=0; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;



}

if($_POST["del"]==10)  //Forschungslab
if($planet->feld[$fid]->was==10 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+350>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=200; $amountB=150; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;



}

if($_POST["del"]==11)  //terra1
if($planet->feld[$fid]->was==11 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+200>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=200; $amountB=0; }
$planet->frachtraum->deuterium+=$amountA;
$planet->frachtraum->duranium+=$amountB;



}


if($_POST["del"]==12)  //Flager
if($planet->feld[$fid]->was==12 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+15>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=10; $amountB=5; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;



}

if($_POST["del"]==13)  //hitzekraftwerk
if($planet->feld[$fid]->was==13 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+60>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=40; $amountB=20; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;



}

if($_POST["del"]==14)  //hitzekraftwerk
if($planet->feld[$fid]->was==14 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+400>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=300; $amountB=100; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;



}

if($_POST["del"]==15)  //terra1
if($planet->feld[$fid]->was==15 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+200>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=200; $amountB=0; }
$planet->frachtraum->deuterium+=$amountA;
$planet->frachtraum->duranium+=$amountB;



}

if($_POST["del"]==17)  //Plasma
if($planet->feld[$fid]->was==17 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+300>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=130; $amountB=125; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;



}


if($_POST["del"]==21)  //Plasma
if($planet->feld[$fid]->was==21 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+350>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=200; $amountB=150; $amountC=00; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;
$planet->frachtraum->tritanium+=$amountC;



}

if($_POST["del"]==22)  //Trit
if($planet->feld[$fid]->was==22 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+400>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=250; $amountB=150; $amountC=0; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;
$planet->frachtraum->tritanium+=$amountC;



}

if($_POST["del"]==23)  //Teilchen
if($planet->feld[$fid]->was==23 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+350>$planet->frachtraum->max) { $amountA=0; $amountB=0; $amountC=0; } else { $amountA=200; $amountB=150; $amountC=0; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;
$planet->frachtraum->tritanium+=$amountC;



}

if($_POST["del"]==24)  //Fusion
if($planet->feld[$fid]->was==24 && $planet->feld[$fid]->bauzeit>0) {

if($planet->frachtraum->gesamt()+140>$planet->frachtraum->max) { $amountA=0; $amountB=0; } else { $amountA=100; $amountB=40; $amountC=0; }
$planet->frachtraum->baustoff+=$amountA;
$planet->frachtraum->duranium+=$amountB;
$planet->frachtraum->tritanium+=$amountC;



}

if($_POST["del"]>0) {
$planet->feld[$fid]->was=0;
$planet->feld[$fid]->bauzeit=0;
$planet->feld[$fid]->save();
$planet->frachtraum->save();
echo '<meta http-equiv="refresh" content="0; URL=planet.php?pid=',$planet->id,'" />';
}

if($planet->feld[$fid]->was==1 && $planet->feld[$fid]->bauzeit>0) { // Baufabrik stoppen
echo 'Wenn du den Bau der Baustofffabrik stoppst erh&auml;lst du 20 Baustoff zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="1"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==2 && $planet->feld[$fid]->bauzeit>0) { // Lager stoppen
echo 'Wenn du den Bau des Lagers stoppst erh&auml;lst du 10 Baustoff zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="2"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==3 && $planet->feld[$fid]->bauzeit>0) { // Solarstation stoppen
echo 'Wenn du den Bau der Solarstation stoppst erh&auml;lst du 20 Baustoff zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="3"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==4 && $planet->feld[$fid]->bauzeit>0) { // Werft stoppen
echo 'Wenn du den Bau der Werft stoppst erh&auml;lst du 100 Baustoff und 50 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="4"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==5 && $planet->feld[$fid]->bauzeit>0) { // Plasmakanonne stoppen
echo 'Wenn du den Bau der Plasmakanonne stoppst erh&auml;lst du 130 Baustoff und 125 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="5"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==6 && $planet->feld[$fid]->bauzeit>0) { // Schildturm stoppen
echo 'Wenn du den Bau des Schildturm stoppst erh&auml;lst du 130 Baustoff und 80 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="6"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==7 && $planet->feld[$fid]->bauzeit>0) { // Wasserwerk stoppen
echo 'Wenn du den Bau des Wasserwerks stoppst erh&auml;lst du 30 Baustoff und 10 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="7"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==8 && $planet->feld[$fid]->bauzeit>0) { // Mine stoppen
echo 'Wenn du den Bau der Mine stoppst erh&auml;lst du 20 Baustoff zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="8"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==9 && $planet->feld[$fid]->bauzeit>0) { // frachtraum->duraniumanlage stoppen
echo 'Wenn du den Bau der frachtraum->duraniumanlage stoppst erh&auml;lst du 30 Baustoff zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="9"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==10 && $planet->feld[$fid]->bauzeit>0) { // Forschungsanlage stoppen
echo 'Wenn du den Bau des Forschungslabor stoppst erh&auml;lst du 200 Baustoff und 150 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="10"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==11 && $planet->feld[$fid]->bauzeit>0) { // Terraform I stoppen
echo 'Wenn du das Terraforming stoppst erh&auml;lst du 200 Deuterium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="11"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==12 && $planet->feld[$fid]->bauzeit>0) { // flager
echo 'Wenn du den Bau der Lagerh&ouml;hle stoppst erh&auml;lst du 10 Baustoff und 5 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="12"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==13 && $planet->feld[$fid]->bauzeit>0) { //lavawerk
echo 'Wenn du den Bau des Hitzekraftwerks stoppst erh&auml;lst du 40 Baustoff und 20 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="13"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==14 && $planet->feld[$fid]->bauzeit>0) { // Soriumfabrik
echo 'Wenn du den Bau der Soriumfabrik stoppst erh&auml;lst du 300 Baustoff und 150 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="14"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==15 && $planet->feld[$fid]->bauzeit>0) { // Terraform II stoppen
echo 'Wenn du das Terraforming stoppst erh&auml;lst du 200 Deuterium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="15"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==16 && $planet->feld[$fid]->bauzeit>0) { // Schildturm stoppen
echo 'Wenn du den Bau des Schildturm stoppst erh&auml;lst du 130 Baustoff und 80 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="6"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==17 && $planet->feld[$fid]->bauzeit>0) { // Plasmakanonne stoppen
echo 'Wenn du den Bau der Plasmakanonne stoppst erh&auml;lst du 130 Baustoff und 125 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="17"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==18 && $planet->feld[$fid]->bauzeit>0) { // Terraform II stoppen
echo 'Wenn du das Terraforming stoppst erh&auml;lst du 300 Deuterium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="18"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==21 && $planet->feld[$fid]->bauzeit>0) { // Plasmakanonne stoppen
echo 'Wenn du den Bau des Teilchenbeschleunigers stoppst erh&auml;lst du 200 Baustoff und 150 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="21"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==22 && $planet->feld[$fid]->bauzeit>0) { //Trit
echo 'Wenn du den Bau der Tritaniumanlage stoppst erh&auml;lst du 250 Baustoff und 150 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="22"><input type="submit" value="Bau stoppen"></form>';
}

if($planet->feld[$fid]->was==23 && $planet->feld[$fid]->bauzeit>0) { //Trit
echo 'Wenn du den Bau des Teilchenbeschleunigers stoppst erh&auml;lst du 200 Baustoff und 150 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="23"><input type="submit" value="Bau stoppen"></form>';
}
if($planet->feld[$fid]->was==24 && $planet->feld[$fid]->bauzeit>0) { //Trit
echo 'Wenn du den Bau des Fusionsreaktors stoppst erh&auml;lst du 100 Baustoff und 40 frachtraum->duranium zur&uuml;ck!<br />';
echo '<br /><form action="baustop.php?pid=',$pid,'&fid=',$fid,'" method="post" onSubmit="return frage(2)"><input type="hidden" name="del" value="24"><input type="submit" value="Bau stoppen"></form>';
}

echo '<br /><a href="planet.php?pid=',$pid,'">zur&uuml;ck zum Planeten</a>';



include("foot.php");
?>
