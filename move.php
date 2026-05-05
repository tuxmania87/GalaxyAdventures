<?php
// Test auf Tick
include_once 'connect.php';

$verbindung = get_verbindung();

$tm = mysqli_query($verbindung, 'SELECT * FROM `ticklog` WHERE id=(SELECT max(id) FROM `ticklog`)') or exit($verbindung->error);
while ($tm2 = mysqli_fetch_array($tm)) {
    if ($tm2['status'] == 1) {
        header('Location: http://keinerspieltmitmir.de/de/maintick.php');
        exit;
    }
}

// endetest
session_start();
$beta = 0;
$tm = mysqli_query($verbindung, "SELECT beta FROM account WHERE id='".intval(\$_SESSION['Id'])."'");
$tm = mysqli_fetch_array($tm);
$beta = $tm[0];

$tm = mysqli_query($verbindung, 'SELECT * FROM `gamestatus` WHERE id=(SELECT max(id) FROM `gamestatus`)') or exit($verbindung->error);
while ($tm2 = mysqli_fetch_array($tm)) {
    if ($tm2['status'] == 'offline' && $beta == 0) {
        header('Location: http://www.keinerspieltmitmir.de/de/wartung.php');
        exit;
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="de">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="thickbox.css" />
        <script type="text/javascript" src="jquery.js"></script>
        <script type="text/javascript" src="ajax.js"></script>
        <script type="text/javascript" src="thickbox.js"></script>	

        <script type="text/javascript">

            function frage(zahl) {
                if (zahl==1) return confirm("Willst du das Geb&auml;ude wirklich abreissen?");
                if (zahl==2) return confirm("Willst du den Bau wirklich abbrechen?");
                if (zahl==3) return confirm("Willst du deine Kolonie wirklich sprengen?");
                if (zahl==4) return confirm("Willst du dein Schiff wirklich zerst&ouml;ren?");
            }

            function sendchat() {
                var text = "chat_send.php?hash=" + escape(document.getElementById("sendchatform").value);
                //alert(text);
                //alert(document.forms.form1.sendchatform.value);
                document.forms.form1.reset();
                setRequest(text);
                setRequest("chat_query.php");
            }
	

            function start() {
                time();
                setRequest("chat_query.php");
                window.setInterval("time()", 1000);
                window.setInterval("setRequest(\"chat_query.php\")", 500);
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
            var jahr=2008, monat=10, tag=1, stunde=18, minute=0, sekunde=0;
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
<?php
$gnu = false;
$ssid = $_SESSION['Id'];
$pa = mysqli_query($verbindung, "SELECT * FROM mail WHERE popup='1' AND empfaenger='$ssid'");
while ($pr = mysqli_fetch_array($pa)) {
    $gnu = true;
}

if ($gnu) {
    ?>
            <script type="text/javascript">
                $(document).ready(function(){
                    tb_show("Neue Nachricht","popuppost.php?height=40&width=400","images/cart.jpg");
                });
            </script>

    <?php
}
?>

        <script type="text/javascript" src="tooltips.js"></script>
        <title>Star Trek - Galaxy Adventures II</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>

<?php
include 'navlogged.php';
include 'klassen.php';

$ich = new Account($_SESSION['Id']);

if (isset($_GET['pid']) && ctype_digit($_GET['pid'])) {
    $pid = $_GET['pid'];
    $t = new Planeten($pid);
    if ($t->besitzer->id != 2 || $t->position->x > 200 || $t->position->y > 200 || $t->typ != 'm') {
        exit;
    }
    // alle schiffe verschieben
    mysqli_query($verbindung, "UPDATE schiffe SET orbit=1,system='".$t->position->system->id."',x='".$t->position->x."',y='".$t->position->y."' WHERE besitzer='".intval(\$_SESSION['Id'])."'");
    // planetenoberfl�che kopieren
    $oldpl = mysqli_query($verbindung, "SELECT id FROM planeten WHERE besitzer='".intval(\$_SESSION['Id'])."'");
    $oldpl = mysqli_fetch_array($oldpl);
    $oldpl = $oldpl[0];
    $old = new Planeten($oldpl);
    // $old->feld=$t->feld;
    // var_dump($old->feld);
    for ($i = 1; $i <= 50; ++$i) {
        $old->feld[$i]->pid = $pid;
        // $t->feld[$i]=$old->feld[$i];
        $old->feld[$i]->save();
    }
    $t->frachtraum = $old->frachtraum;
    $t->frachtraum->id2 = $pid;
    $t->frachtraum->save();
    mysqli_query($verbindung, "UPDATE planeten SET energie='".$old->maxenergie."',maxenergie='".$old->maxenergie."',name='".$old->name."',heimat=1,besitzer='".intval(\$_SESSION['Id'])."' WHERE id='".$t->id."'");
    $old->sprengen();
    echo '<meta http-equiv="refresh" content="0;url=main.php">';
}

if ($ich->level <= 3) {
    exit;
}
if (mysqli_num_rows(mysqli_query($verbindung, "SELECT * FROM systeme,planeten WHERE planeten.system=systeme.id AND systeme.x>=1000 AND systeme.y>=1000 AND planeten.besitzer='".intval(\$_SESSION['Id'])."'")) == 0) {
    exit;
}

echo '<h3>Umsiedlung</h3>';

echo 'Da du jetzt &uuml;ber mehr Erfahrung verfügst, wirst du umgesiedelt. Bitte wähle
    eines der folgenden Systeme aus und du wirst direkt umgesiedelt:<br /><br />';

echo '<table class="bordered2"><tr><td>IMG</td><td>gelgen im System:</td><td>Position im System</td></tr>';
$abfrage = mysqli_query($verbindung, "SELECT planeten.id FROM systeme,planeten WHERE planeten.system=systeme.id AND systeme.x<=200 AND systeme.y<=200 AND planeten.besitzer='2' AND planeten.typ='m'");
while ($row = mysqli_fetch_array($abfrage)) {
    $planet = new Planeten($row[0]);
    echo '<tr><td><a target="_blank" href="map.php?system=', $planet->position->system->id, '"><img src="images/systems/', $planet->position->system->bild, '" border="0" /></a></td><td>', $planet->position->system->x, '/', $planet->position->system->y, ' (', $planet->position->system->name, ' [', $planet->position->system->id, '] )</td><td>', $planet->position->x, '/', $planet->position->y, '</td><td><a href="move.php?pid=', $planet->id, '">ausw&auml;hlen</a></td></tr>';
}
echo '</table>';

include 'foot.php';
?>
