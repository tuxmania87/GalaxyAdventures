<?php

include("head.php");
include("navlogged.php");
include_once("connect.php");

//CHEATSCHUTZ ANFANG

$pid = $_GET["pid"];
$fid = $_GET["fid"];

$betray = false;
if (!ctype_digit($pid))
    $betray = true;
if (!ctype_digit($fid))
    $betray = true;
$tmp = mysql_query("SELECT besitzer FROM schiffe WHERE id='$pid'");
while ($testtmp = mysql_fetch_array($tmp))
    if ($_SESSION["Id"] != $testtmp["besitzer"])
        $betray = true;

if ($betray) {
    echo 'Du bist nicht eingeloggt oder du versucht auf fremde Accounts zuzugreifen...';
} else {

//CHEATSCHUTZ ENDE
    $aktPlanet = new schiff($pid);
//FELDNUMMER 
    $feldnummer = "feld" . $fid;
    $aktFeld = mysql_query("SELECT $feldnummer FROM planet2 WHERE pid='$pid'");
    $aktFeld = mysql_fetch_array($aktFeld);
    $aktFeld = $aktFeld[0];

    splitfeld($aktFeld, $a, $b, $c, $d, $e);
//ENDE FELD
//aktiveren
    if ($_GET["do"] == 'cyk') {
        $continue = true;
        if ($e > 0 && $continue) {
            $newvar1 = $a . "-" . $b . "-" . $c . "-" . $d . "-0";
            $e = 0;
            $continue = false;
        }
        if ($e == 0 && $continue) {
            $newvar1 = $a . "-" . $b . "-" . $c . "-" . $d . "-1";
            $e = 1;
            $continue = false;
        }
        mysql_query("UPDATE planet2 SET $feldnummer='$newvar1' WHERE pid='$pid'");
    }


    if ($_POST["produktion"] == 'photonen') {
        $e = 1;
        $newvar = $a . "-" . $b . "-" . $c . "-" . $d . "-1";
        mysql_query("UPDATE planet2 SET $feldnummer='$newvar' WHERE pid='$pid'");
    }

    if ($_POST["produktion"] == 'quanten') {
        $e = 2;
        $newvar = $a . "-" . $b . "-" . $c . "-" . $d . "-2";
        mysql_query("UPDATE planet2 SET $feldnummer='$newvar' WHERE pid='$pid'");
    }

    $aktPlanet->setData();

    $do = $_POST["do"];


    echo '<h3>Torpedoproduktion</h3>';
    echo '<br />Produktion: <b>';
    if ($e == 0)
        echo "keine Produktion";
    if ($e == 1)
        echo "Photonentorpedos";
    if ($e == 2)
        echo "Quantentorpedos";

    echo '</b><br /><br />Du kannst hier den Produktionszyklus einstellen. Die Produktion wird solange fortgesetzt bis keine Rohstoffe mehr vorhanden sind oder man die Fabrik abschaltet.<br /><br />';
    echo '<table class="bordered">';
    echo '<tr><form action="torpedo.php?pid=', $pid, '&fid=', $fid, '" method="post"><input type="hidden" name="produktion" value="photonen" /><td><img src="photonen.png" border="0" /></td><td>Photonentorpedos</td><td>8 Schaden</td><td>-3 Duranium<br />-5 Baustoffe<br />-2 Antimaterie<br />-2 Deuterium<br />-4 Energie</td><td><input type="submit" value="Auf Photonentorpedos umstellen" /></td></form></tr>';
    echo '<tr><form action="torpedo.php?pid=', $pid, '&fid=', $fid, '" method="post"><input type="hidden" name="produktion" value="quanten" /><td><img src="quanten.png" border="0" /></td><td>Quantentorpedos</td><td>12 Schaden</td><td>-3 Tritanium<br />-4 Sorium<br />-2 Antimaterie<br />-4 Baustoff<br />-2 Deuterium<br />-4 Energie</td><td><input type="submit" value="Auf Quantentorpedos umstellen" /></td></form></tr>';
    echo '</table><br />';
    echo '<br />Das Geb&auml;ude ist ';
    if ($e > 0)
        echo '<font color="green"><b>aktiviert</b></font>.&nbsp;&nbsp;<a href="torpedo.php?pid=', $pid, '&fid=', $fid, '&do=cyk">deaktivieren?</a><br />';
    if ($e == 0)
        echo '<font color="red"><b>deaktiviert</b></font>.&nbsp;&nbsp;<a href="torpedo.php?pid=', $pid, '&fid=', $fid, '&do=cyk">aktivieren?</a><br />';
    echo '<br /><a href="planet.php?pid=', $pid, '">zur&uuml;ck zum Planeten</a>';

    echo '<br /><hr />';
    echo 'Wenn du die Torpedofabrik abreisst erh&auml;lst du 125 Baustoff und 75 Duranium zur&uuml;ck!<br />';
    echo '<br /><form action="destroy.php?pid=', $pid, '&fid=', $fid, '" method="post" onSubmit="return frage(1)"><input type="hidden" name="del" value="27"><input type="submit" value="abreissen"></form>';
}


include("foot.php");
?>
