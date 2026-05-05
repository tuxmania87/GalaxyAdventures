<?php

include("head.php");
include("navlogged.php");
include("klassen.php");
$account = new Account($_SESSION["Id"]);   //!!
//CHEATEST
$ich = new Account($_SESSION["Id"]);

$anfeld = array();
$tempstring = "";
$behandelt = false;
if ($_GET["to"] == 'Allianz' || $_GET["to"] == 'allianz' || $_POST["empfaenger"] == 'Allianz' || $_POST["empfaenger"] == 'allianz') {
    $abfrage = mysqli_query($verbindung, "SELECT id FROM account WHERE allianz='" . $ich->allianz->id . "' AND allianz>0");
    while ($row = mysqli_fetch_array($abfrage))
        $anfeld[] = $row[0];
    $behandelt = true;
}

if (isset($_GET["to"]) && !$behandelt) {
    $anfeld = explode(",", $_GET["to"]);
    for ($i = 0; $i < sizeof($anfeld); $i++) {
        if (!ctype_digit($anfeld[$i]) && $_GET["to"] != '-3')
            die("Empf&auml;nger enth&auml;llt Buchstaben oder Sonderzeichen");
    }
}
if (isset($_POST["empfaenger"]) && !$behandelt) {
    $anfeld = explode(",", $_POST["empfaenger"]);
    for ($i = 0; $i < sizeof($anfeld); $i++) {
        if (!ctype_digit($anfeld[$i]) && $_POST["empfaenger"] != '-3')
            die("Empf&auml;nger enth&auml;llt Buchstaben oder Sonderzeichen");
    }
}

//EENDE


if ($_POST["send"] == 1 && $_POST["empfaenger"] == '-3') {
    $datum = date("Y-m-d H:i:s");
    $empfaenger = pruefetext($_POST["empfaenger"]);
    $absender = $account->id;
    $betreff = pruefetext($_POST["betreff"]);
    $inhalt = pruefetext($_POST["inhalt"]);
    if ($betreff == '')
        $betreff = "kein betreff";

    $abfrage23 = mysqli_query($verbindung, "SELECT * FROM account");
    while ($tmp23 = mysqli_fetch_array($abfrage23)) {
        $ttid = $tmp23["id"];
        mysqli_query($verbindung, "INSERT INTO mail (
empfaenger ,
absender ,
betreff ,
inhalt ,
datum ,
neu ,
popup
)
VALUES ('$ttid', '$absender', '$betreff', '$inhalt', '$datum', '1', '1'
)");
    }
    echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=mail.php">';
}


if ($_POST["send"] == 1 && $_POST["empfaenger"] != '-3') {
    for ($i = 0; $i < sizeof($anfeld); $i++) {
        $datum = date("Y-m-d H:i:s");
        $empfaenger = $anfeld[$i];
        $absender = $account->id;
        $betreff = pruefetext($_POST["betreff"]);
        $inhalt = pruefetext($_POST["inhalt"]);
        if ($betreff == '')
            $betreff = "kein betreff";
        mysqli_query($verbindung, "INSERT INTO mail (
empfaenger ,
absender ,
betreff ,
inhalt ,
datum ,
neu ,
popup
)
VALUES ('$empfaenger', '$absender', '$betreff', '$inhalt', '$datum', '1', '1'
)");
    }
    echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=mail.php">';
}



$an = $_GET["to"];
//$subject=changeit($_GET["subject"]);
$subject = ($_GET["subject"]);

if ($_POST["send"] == 2) {

    $subject = $_POST["betreff"];
}
$an = new Account($anfeld[0]);

echo '<form action="newmail.php" method="post">';
echo '<table class="invitetable">';
echo '<tr><th>Absender</th><td>', $account->nickname, '</td></tr>';
if ($anfeld[0] == '')
    echo '<tr><th>Empf&auml;nger (ID)</th><td><input type="text" name="empfaenger" />   <a href="newmail.php?to=Allianz">an Allianz</a></td></tr>';
if ($anfeld[0] != '')
    echo '<tr><th>Empf&auml;nger</th><td>', $an->nickname;
for ($i = 1; $i < sizeof($anfeld); $i++) {
    $anx = new Account($anfeld[$i]);
    echo ',', $anx->nickname;
}
echo '</td></tr>';
if (sizeof($anfeld) >= 1 && $_POST["empfaenger"] != '-3') {
    echo '<input type="hidden" name="empfaenger" value="', $an->id;
    for ($i = 1; $i < sizeof($anfeld); $i++) {
        $anx = new Account($anfeld[$i]);
        echo ',', $anx->id;
    }
    echo '">';
}
if ($_POST["empfaenger"] == '-3')
    echo '<input type="hidden" name="empfaenger" value="-3" />';
echo '<tr><th>Betreff</th><td><input type="text" name="betreff" value="', $subject, '" /></td></tr>';
echo '</table><br />';

echo '<table class="invitetable">';
echo '<tr><th>Nachricht</th></tr>';
echo '<tr><td><div style="border:1px solid red;padding:10px;">', nl2br(pruefetext($_POST["inhalt"])), '</div><br /><textarea rows=15 cols=40 name="inhalt">', $_POST["inhalt"], '</textarea></td></tr></table>';
echo '<input type="radio" name="send" value="2" ', $POST["send"] == 2 ? '' : 'checked="checked"', '> Vorschau<br />';
if ($_POST["send"] == 2)
    echo '<input type="radio" name="send" value="1" checked="checked"> Senden<br />';
echo '<br />';

$bu = new Button("newmail.php","Formular zurücksetzen");
$bu->printme();

echo "<br /><br />";

$bu = new Button("","senden");
$bu->printme();


include("foot.php");
?>
