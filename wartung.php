<?php

//Test auf Tick
include_once("connect.php");
$tm=mysql_query("SELECT * FROM `ticklog` WHERE id=(SELECT max(id) FROM `ticklog`)") or die(mysql_error());
while($tm2=mysql_fetch_array($tm)) {
if($tm2["status"]==1) { 
header ("Location: http://www.keinerspieltmitmir.de/de/maintick.php");
exit;
}
}

mysql_close($db);
//endetest
session_start();


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="de">
<head>
 <script type="text/javascript">

function frage(zahl) {
  if (zahl==1) return confirm("Willst du das Geb&auml;ude wirklich abreissen?");
  if (zahl==2) return confirm("Willst du den Bau wirklich abbrechen?");
  if (zahl==3) return confirm("Willst du deine Kolonie wirklich sprengen?");
  if (zahl==4) return confirm("Willst du dein Schiff wirklich zerst&ouml;ren?");
}


function start() {
	countdown();
	time();
	window.setInterval("time()", 1000);
}

function time() {
	var now = new Date();
	hours = now.getHours();
	minutes = now.getMinutes();
	seconds = now.getSeconds();

	thetime = (hours < 10) ? "0" + hours + ":" : hours + ":";
	thetime += (minutes < 10) ? "0" + minutes + ":" : minutes + ":";
	thetime += (seconds < 10) ? "0" + seconds : seconds;

	element = document.getElementById("time");
	element.innerHTML = thetime;
}

function goback(){
history.back();
}


      // Ziel-Datum in MEZ
      var jahr=2008, monat=12, tag=1, stunde=18, minute=0, sekunde=0;
      var zielDatum=new Date(jahr,monat-1,tag,stunde,minute,sekunde);

      function countdown() {
        startDatum=new Date(); // Aktuelles Datum

        // Countdown berechnen und anzeigen, bis Ziel-Datum erreicht ist
        if(startDatum<zielDatum)  {

          var jahre=0, monate=0, tage=0, stunden=0, minuten=0, sekunden=0;

          // Jahre
          while(startDatum<zielDatum) {
            jahre++;
            startDatum.setFullYear(startDatum.getFullYear()+1);
          }
          startDatum.setFullYear(startDatum.getFullYear()-1);
          jahre--;

          // Monate
          while(startDatum<zielDatum) {
            monate++;
            startDatum.setMonth(startDatum.getMonth()+1);
          }
          startDatum.setMonth(startDatum.getMonth()-1);
          monate--;

          // Tage
          while(startDatum.getTime()+(24*60*60*1000)<zielDatum) {
            tage++;
            startDatum.setTime(startDatum.getTime()+(24*60*60*1000));
          }

          // Stunden
          stunden=Math.floor((zielDatum-startDatum)/(60*60*1000));
          startDatum.setTime(startDatum.getTime()+stunden*60*60*1000);

          // Minuten
          minuten=Math.floor((zielDatum-startDatum)/(60*1000));
          startDatum.setTime(startDatum.getTime()+minuten*60*1000);

          // Sekunden
          sekunden=Math.floor((zielDatum-startDatum)/1000);

          // Anzeige formatieren
          (jahre!=1)?jahre=jahre+" Jahre, ":jahre=jahre+" Jahr,  ";
          (monate!=1)?monate=monate+" Monate, ":monate=monate+" Monat,  ";
          (tage!=1)?tage=tage+" Tage, ":tage=tage+" Tag,  ";
          (stunden!=1)?stunden=stunden+" Stunden, ":stunden=stunden+" Stunde,  ";
          (minuten!=1)?minuten=minuten+" Minuten und ":minuten=minuten+" Minute  und  ";
          if(sekunden<10) sekunden="0"+sekunden;
          (sekunden!=1)?sekunden=sekunden+" Sekunden":sekunden=sekunden+" Sekunde";

          document.countdownform.countdowninput.value=
              jahre+monate+tage+stunden+minuten+sekunden;

          setTimeout('countdown()',200);
        }
        // Anderenfalls alles auf Null setzen
        else document.countdownform.countdowninput.value=
            "0 Jahre,  0 Monate,  0 Tage,  0 Stunden,  0 Minuten  und  00 Sekunden";
      }



</script>
<script type="text/javascript" src="tooltips.js"></script>
  <title>Star Trek - Galaxy Adventures II</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>


<?php


include("navlogged.php");
include("klassen.php");
?>
<h2>Wartungsarbeiten</h2><br />
Das Spiel ist zur Zeit <b><font color="red">offline</font></b><br /><br />Grund:<br />

<form name="countdownform"><input type="text" name="countdowninput" size="80"/></form>

<?php 

/*
$abfrage=mysql_query("SELECT * FROM gamestatus WHERE id=(SELECT max(id) FROM gamestatus)");
while($b=mysql_fetch_array($abfrage)) 
echo $b["beschreibung"];
*/
include("foot.php");
?>
