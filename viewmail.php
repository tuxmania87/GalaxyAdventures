<?php

include("head.php");
include("navlogged.php");
include("klassen.php");
//CHEATSCHUTZ ANFANG
$selfid = $_SESSION["Id"];

$verbindung = get_verbindung();

$betray = false;
if (!ctype_digit($_GET["brief"]))
    $betray = true;
if ($_SESSION["Id"] <= 0)
    $betray = true;
if (!$betray) {
    
}
if (!ctype_digit($_GET["brief"]))
    die();

//CHEATSCHUTZ ENDE


$id = 1;
$postid = $_GET["brief"];
$postquery = mysqli_query($verbindung,"SELECT * FROM mail WHERE id='$postid'"); //id einsetzen
while ($post = mysqli_fetch_array($postquery)) {    //Abfrage der accountdaten
    $abs = new Account($post["absender"]);
    if ($_SESSION["Id"] == $post["absender"] || $_SESSION["Id"] == $post["empfaenger"]) {//Pruefen ob cookievariable = empfaenger!
        echo '<table class="invitetable">';
        echo '<tr><th>Empfangsdatum</th><td>', gerdatum($post["datum"]), '</td></tr>';
        echo '<tr><th>Absender</th><td>', $abs->nickname, '</td></tr>';
        echo '<tr><th>Betreff</th><td>', $post["betreff"], '</td></tr>';
        echo '</table>';
        echo '<br />';

        echo '<table class="invitetable">';
        echo '<tr><th>Nachricht:</td></tr>';
        echo '<tr><td style="width:400px;">', nl2br($post["inhalt"]), '</td></tr></table>';
        mysqli_query($verbindung,"UPDATE mail SET neu=0 WHERE id='$postid' AND empfaenger='$selfid'");
        echo '<br />';
        
        $bu = new Button("newmail.php?to=". $post["absender"]."&subject=Re:%20". $post["betreff"],"<span style=\"color:green;\">antworten</span>");
        $bu->printme();
        echo "<br /><br />";
        $bu = new Button("mail.php","zurück zum Posteingang");
        $bu->printme();
        
    } else
        echo 'Das ist nicht deine Post!';
}
//che

include("foot.php");
?>
