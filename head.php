<?php
//Test auf Tick
include_once("connect.php");
$verbindung = get_verbindung();
$tm = mysqli_query($verbindung, "SELECT * FROM `ticklog` WHERE id=(SELECT max(id) FROM `ticklog`)") or die(mysql_error());
while ($tm2 = mysqli_fetch_array($tm)) {
    if ($tm2["status"] == 1) {
        header("Location: http://www.keinerspieltmitmir.de/devga/maintick.php");
        exit;
    }
}


//endetest
session_start();
$beta = 0;
$tm = mysqli_query($verbindung, "SELECT beta FROM account WHERE id='" . $_SESSION["Id"] . "'");
$tm = mysqli_fetch_array($tm);
$beta = $tm[0];

$tm = mysqli_query($verbindung, "SELECT * FROM `gamestatus` WHERE id=(SELECT max(id) FROM `gamestatus`)") or die(mysql_error());
while ($tm2 = mysqli_fetch_array($tm)) {
    if ($tm2["status"] == 'offline' && $beta == 0) {
//header ("Location: http://www.keinerspieltmitmir.de/devga/wartung.php");
//exit;
    }
}

$ich_temp = mysqli_query($verbindung,"SELECT level FROM account WHERE id='" . $_SESSION["Id"] . "'");
$ich_temp = mysqli_fetch_array($ich_temp);
$ich_temp = $ich_temp[0];

if ($ich_temp >= 4) {
    $tm = mysqli_query($verbindung, "SELECT * FROM systeme,planeten WHERE planeten.system=systeme.id AND systeme.x>=1000 AND systeme.y>=1000 AND planeten.besitzer='" . $_SESSION["Id"] . "'") or die(mysql_error());
    while ($tm2 = mysqli_fetch_array($tm)) {
        header("Location: move.php");
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
                if (zahl==1) return confirm("Willst du das Gebäude wirklich abreissen?");
                if (zahl==2) return confirm("Willst du den Bau wirklich abbrechen?");
                if (zahl==3) return confirm("Willst du deine Kolonie wirklich sprengen?");
                if (zahl==4) return confirm("Willst du dein Schiff wirklich zerstören?");
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
<?php if (($_SERVER["SCRIPT_NAME"] == '/ga_source/main.php' || $_SERVER["SCRIPT_NAME"] == '/de/index.php') && (ctype_digit($_SESSION["Id"]) || $_SESSION["nick"] != '')) echo 'setRequest("chat_query.php");'; ?>
                       window.setInterval("time()", 1000);
<?php if (($_SERVER["SCRIPT_NAME"] == '/ga_source/main.php' || $_SERVER["SCRIPT_NAME"] == '/de/index.php') && (ctype_digit($_SESSION["Id"]) || $_SESSION["nick"] != '')) echo 'window.setInterval("setRequest(\"chat_query.php\")", 1200);'; ?>
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

                   function mapid2acc() {
                       alert(document.getElementById("idinput").value);
                   }
                   
                   function highlight(contid) {
                       document.getElementById("lcarl_" + contid).src='images/misc/lh.gif';
                       if(document.getElementById("lcarr_" + contid))
                          document.getElementById("lcarr_" + contid).src='images/misc/rh.gif';
                       if(document.getElementById("lcarh_" + contid))
                          document.getElementById("lcarh_" + contid).src='images/misc/rhelph.gif';
                   }
	  
                   function downlight(contid) {
                       document.getElementById("lcarl_" + contid).src='images/misc/l.gif';
                       if(document.getElementById("lcarr_" + contid))
                           document.getElementById("lcarr_" + contid).src='images/misc/r.gif';
                       if(document.getElementById("lcarh_" + contid))
                           document.getElementById("lcarh_" + contid).src='images/misc/rhelp.gif';
                   }
                   
                   function highlighthelp(contid) {
                       document.getElementById("lcarh_" + contid).src='images/misc/rhelphtt.gif';
                   }
                   
                   function downlighthelp(contid) {
                       document.getElementById("lcarh_" + contid).src='images/misc/rhelp.gif';
                   }

        </script>
        <?php
        $gnu = false;
        $ssid = $_SESSION["Id"];
        $pa = mysqli_query($verbindung, "SELECT * FROM mail WHERE popup='1' AND empfaenger='$ssid'");
        while ($pr = mysqli_fetch_array($pa))
            $gnu = true;

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


