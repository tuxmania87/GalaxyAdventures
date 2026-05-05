
<?php

if($_GET["pwd"]=='b4efb1f8a6dbadb8bdba2e0092559965') {


include_once("connect.php");

function gesamt($objekt)
{
$inhalt=array("rohstoffa","rohstoffb","rohstoffc","rohstoffd","isochips","tritanium","dili","antimaterie","deuterium","npcborg","npcrom","npcfer","npcfod");
$inhaltcap=array("Baustoff","Duranium","Erz","Sorium","Isochips","Tritanium","Dilithium","Antimaterie","Deuterium","Vinkulum","Plasma","Latinuum","Earl Grey");
$inhaltcode=array("ra","rb","rc","rd","iso","trit","dili","anti","deut","nborg","nrom","nfer","nfod");
$gesamt=0;
for($p=0;$p<count($inhalt);$p++) $gesamt+=$objekt->$inhalt[$p];
return $gesamt;
}


//tick anmelden
$anfangminuten=date("i");
$anfangsek=date("s");
$anfang=$anfangminuten*60+$anfangsek;


$datum=date("Y-m-d H:i:s");
$ip=$_SERVER['REMOTE_ADDR'];
mysqli_query($verbindung, "INSERT INTO `ticklog` (datum,ip,status) VALUES ('$datum','$ip','1')");


mysqli_query($verbindung, "UPDATE account SET mitglied=mitglied+1");
mysqli_query($verbindung, "DELETE FROM schiffe WHERE besitzer=2 AND typ='s' AND hull=1");
mysqli_query($verbindung, "UPDATE schiffe SET hull=hull-1 WHERE besitzer=2 AND typ='s' AND hull > 1");




// }

//Output auf schiffen / energie
$abfrage=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='s'");
while($energie=mysqli_fetch_array($abfrage))
{
$tmpid=$energie["id"];
$out=$energie["energieoutput"];
$amount=$out;
if($energie["deuterium"]<$amount) $amount=$energie["deuterium"];
if($amount+$energie["energie"] > $energie["maxenergie"]) { $amount=$energie["maxenergie"]-$energie["energie"]; mysqli_query($verbindung, "UPDATE schiffe SET energie=maxenergie,deuterium=deuterium-'$amount' WHERE id='$tmpid'"); } else
mysqli_query($verbindung, "UPDATE schiffe SET energie=energie+'$amount',deuterium=deuterium-'$amount' WHERE id='$tmpid'");
}



//inaktiv
mysqli_query($verbindung, "UPDATE account SET inaktiv=inaktiv+1");


//forschung allgemein
mysqli_query($verbindung, "UPDATE forschung SET waffen2=waffen2-1 WHERE waffen2 > 1");
mysqli_query($verbindung, "UPDATE forschung SET hull1=hull1-1 WHERE hull1 > 1");
mysqli_query($verbindung, "UPDATE forschung SET antrieb1=antrieb1-1 WHERE antrieb1 > 1");
mysqli_query($verbindung, "UPDATE forschung SET krieg=krieg-1 WHERE krieg > 1");
mysqli_query($verbindung, "UPDATE forschung SET krieg2=krieg2-1 WHERE krieg2 > 1");
mysqli_query($verbindung, "UPDATE forschung SET krieg3=krieg3-1 WHERE krieg3 > 1");
mysqli_query($verbindung, "UPDATE forschung SET terra1=terra1-1 WHERE terra1 > 1");
mysqli_query($verbindung, "UPDATE forschung SET terra2=terra2-1 WHERE terra2 > 1");
mysqli_query($verbindung, "UPDATE forschung SET miranda=miranda-1 WHERE miranda > 1");
mysqli_query($verbindung, "UPDATE forschung SET consti=consti-1 WHERE consti > 1");

//schiffe ausbauen
mysqli_query($verbindung, "UPDATE schiffe SET rohstoffa=rohstoffa-1 WHERE typ='' AND rohstoffa>0");
mysqli_query($verbindung, "UPDATE schiffe SET typ='s',rohstoffb=0 WHERE typ='' AND rohstoffa=0");




//neue Planetenberechnung
$abfrage=mysqli_query($verbindung, "SELECT schiffe.id FROM schiffe, planet2 WHERE schiffe.besitzer !=2 AND schiffe.id = planet2.pid");
while($row=mysqli_fetch_array($abfrage)) {
$tpid=$row["id"];
$tplanet=new schiff();
$tplanet->getData($tpid);
$tfeld=new planetfeld;
$tfeld->getData($tpid);
for($i=1;$i<=50;$i++) {
$aktfeld='feld'.$i;
splitfeld($tfeld->$aktfeld,$a,$b,$c);

if($b==1) { //alle behandlung die fertig werden müssen
if($a==4 || $a==1 || $a==8 || $a==9 || $a==10 || $a==14 || $a==21 || $a==22 ) $tfeld->$aktfeld=$a.'-0-'.$c;
if($a==2) { //lager
$tplanet->lager+=500; $tplanet->maxenergie+=20; $tfeld->$aktfeld=$a.'-0-'.$c; }
if($a==5) { //plasma
$tplanet->laser+=30; $tfeld->$aktfeld=$a.'-0-'.$c; }
if($a==6) { //schilde
$tplanet->maxschilde+=400; $tfeld->$aktfeld=$a.'-0-'.$c; }
if($a==7) { //wasserwerk
$tplanet->maxenergie+=20; $tfeld->$aktfeld=$a.'-0-'.$c; }
if($a==3) { //solar
$tplanet->maxenergie+=20; $tfeld->$aktfeld=$a.'-0-'.$c; }
if($a==11) {
$tfeld->$aktfeld='0-0-g'; }
if($a==12) { //flager
$tplanet->lager+=200; $tfeld->$aktfeld=$a.'-0-'.$c; }
if($a==13) { //hitzekraftwerk
$tplanet->maxenergie+=20; $tfeld->$aktfeld=$a.'-0-'.$c; }
if($a==15) {
$tfeld->$aktfeld='0-0-w'; }
if($a==16) { //schilde
$tplanet->maxschilde+=300; $tfeld->$aktfeld=$a.'-0-'.$c; }
if($a==17) { //plasma
$tplanet->laser+=20; $tfeld->$aktfeld=$a.'-0-'.$c; }
if($a==18) { //plasma
$tfeld->$aktfeld='0-0-im'; }
if($a==19) { //plasma
$tfeld->$aktfeld='0-0-g'; }

}

if($b>1) { //um eins bauen
$b--; $pos=$a.'-'.$b.'-'.$c; $tfeld->$aktfeld=$pos; }

if($b==0) { //berechnen
if($a==3) $tplanet->energie+=15;
if($a==7) $tplanet->energie+=10;
if($a==13) $tplanet->energie+=10;
}

$tfeld->setData($tpid);
$tplanet->setData($tpid);
$tfeld->getData($tpid);
$tplanet->getData($tpid);

}
}

//neue Planetenberechnung 2
$abfrage=mysqli_query($verbindung, "SELECT schiffe.id FROM schiffe, planet2 WHERE schiffe.besitzer !=2 AND schiffe.id = planet2.pid");
while($row=mysqli_fetch_array($abfrage)) {
$tpid=$row["id"];
$tplanet=new schiff();
$tplanet->getData($tpid);
$tfeld=new planetfeld;
$tfeld->getData($tpid);
for($i=1;$i<=50;$i++) {
$amount=0;
$aktfeld='feld'.$i;
splitfeld($tfeld->$aktfeld,$a,$b,$c);
if($b==0) {
if($a==8 && $tplanet->energie>=4) { $amount=gesamt($tplanet)+6>$tplanet->lager?$tplanet->lager-gesamt($tplanet):6; if($amount>0) {  $tplanet->energie-=4; $tplanet->rohstoffc+=$amount; } }
if($a==1 && $tplanet->energie>=5) { $amount=gesamt($tplanet)+5>$tplanet->lager?$tplanet->lager-gesamt($tplanet):5; if($amount>0) {  $tplanet->energie-=5; $tplanet->rohstoffa+=$amount; } }
if($a==14 && $tplanet->energie>=10) { $amount=gesamt($tplanet)+1>$tplanet->lager?0:1; if($amount>0) {  $tplanet->energie-=10; $tplanet->rohstoffd+=$amount; } }
if($a==21 && $tplanet->energie>=10) { $amount=gesamt($tplanet)+1>$tplanet->lager?0:1; if($amount>0) {  $tplanet->energie-=10; $tplanet->antimaterie+=$amount; } }

}
$tfeld->setData($tpid);
$tplanet->setData($tpid);
$tfeld->getData($tpid);
$tplanet->getData($tpid);
}
}

//neue Planetenberechnung 3
$abfrage=mysqli_query($verbindung, "SELECT schiffe.id FROM schiffe, planet2 WHERE schiffe.besitzer !=2 AND schiffe.id = planet2.pid");
while($row=mysqli_fetch_array($abfrage)) {
$tpid=$row["id"];
$tplanet=new schiff();
$tplanet->getData($tpid);
$tfeld=new planetfeld;
$tfeld->getData($tpid);
for($i=1;$i<=50;$i++) {
$amount=0;
$aktfeld='feld'.$i;
splitfeld($tfeld->$aktfeld,$a,$b,$c);
if($b==0) {
if($a==9 && $tplanet->energie>=7 && $tplanet->rohstoffc>=5) { $tplanet->energie-=7; $tplanet->rohstoffb+=5; $tplanet->rohstoffc-=5; }}
$tfeld->setData($tpid);
$tplanet->setData($tpid);
$tfeld->getData($tpid);
$tplanet->getData($tpid);
}
}

//ISO berechnung
$abfrage=mysqli_query($verbindung, "SELECT schiffe.id FROM schiffe, planet2 WHERE schiffe.besitzer !=2 AND schiffe.id = planet2.pid");
while($row=mysqli_fetch_array($abfrage)) {
$tpid=$row["id"];
$tplanet=new schiff();
$tplanet->getData($tpid);
$tfeld=new planetfeld;
$tfeld->getData($tpid);
for($i=1;$i<=50;$i++) {
$amount=0;
$aktfeld='feld'.$i;
splitfeld($tfeld->$aktfeld,$a,$b,$c);
if($b==0) {
if($a==21 && $tplanet->energie>=10 && $tplanet->rohstoffa>=40 && $tplanet->rohstoffb>=30 && $tplanet->antimaterie>=4 && $tplanet->dili>=2) { $tplanet->energie-=10; $tplanet->rohstoffb-=30; $tplanet->rohstoffa-=40; $tplanet->antimaterie-=4; $tplanet->dili-=2; $tplanet->isochips+=1; }
if($a==22 && $tplanet->energie>=100 && $tplanet->rohstoffb>=5 && $tplanet->rohstoffc>=12) { $tplanet->energie-=100; $tplanet->rohstoffb-=5; $tplanet->rohstoffc-=12; $tplanet->tritanium+=1; }}
$tfeld->setData($tpid);
$tplanet->setData($tpid);
$tfeld->getData($tpid);
$tplanet->getData($tpid);
}
}


mysqli_query($verbindung, "UPDATE schiffe SET energie=maxenergie WHERE typ='m) AND energie>maxenergie");

//warenkzinsen
mysqli_query($verbindung, "UPDATE konto SET rohstoffa=ceil(rohstoffa*0.9),rohstoffb=ceil(rohstoffb*0.9),rohstoffc=ceil(rohstoffc*0.9),rohstoffd=ceil(rohstoffd*0.9),deuterium=ceil(deuterium*0.9)");

//mailerinnerungen
$rememberquery=mysqli_query($verbindung, "SELECT * FROM account WHERE inaktiv=56 AND id>9");
while($rem=mysqli_fetch_array($rememberquery)) {
$mail1=$rem["email"];
$name1=$rem["name"];
$message="Hallo $name1,\n\nDu bekommst diese Mail weil du seit 56 Ticks (7 Tagen) dich nicht mehr bei Galaxy Adventures gemeldet hast. Dies soll nur eine kleine Erinnerung sein, dass dein Account noch existiert ;). Solltest du kein Interesse mehr an Galaxy-Adventures 2 haben und weitere 56 Ticks verstreichen, so wird dein Account geloescht und alle deine Daten aus der Datenbank entfernt.\nIch wuensche dir viel Spass\n\ncremetorte";
mail($mail1,"Erinnerung - 7 Tage", $message,"From: GA-Team <" . (defined('GA_MAIL_FROM') ? GA_MAIL_FROM : 'noreply@example.com') . ">");
}

//loeschung
//mailerinnerungen
$rememberquery=mysqli_query($verbindung, "SELECT * FROM account WHERE inaktiv=112 AND id>9");
while($rem=mysqli_fetch_array($rememberquery)) {
$mail1=$rem["email"];
$name1=$rem["name"];
$id1=$rem["id"];
$message="Hallo $name1,\n\nDu bekommst diese Mail weil du seit 112 Ticks (14 Tagen) dich nicht mehr bei Galaxy Adventures gemeldet hast. \nDein Account wurde geloescht. Ich hoffe du hattest Spass in Galaxy-Adventures.\nIch wuensche dir viel Spass\n\ncremetorte";
mail($mail1,"Loeschung - 14 Tage", $message,"From: GA-Team <" . (defined('GA_MAIL_FROM') ? GA_MAIL_FROM : 'noreply@example.com') . ">");
mysqli_query($verbindung, "INSER INTO mail (empfaenger,absender,neu) VALUES ('1','2','1')");
//mysqli_query($verbindung, "DELETE FROM account WHERE id='$id1'");
}


//Tick abmelden
$endeminuten=date("i");
$endesek=date("s");
$ende=$endeminuten*60+$endesek;
$zeit=$ende-$anfang;
mysqli_query($verbindung, "UPDATE `ticklog` SET status=0,dauer='$zeit' WHERE datum='$datum'");
}
else
echo 'guter versuch..';

?>

