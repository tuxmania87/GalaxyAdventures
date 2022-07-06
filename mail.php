<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

$verbindung = get_verbindung();

//Mailverwaltung
$id = $_SESSION["Id"];    //später cookie regelung!
$delpost = $_POST["delpost"];
if ($delpost != '') {
    mysqli_query($verbindung,"UPDATE mail SET del=1 WHERE id='$delpost' AND empfaenger='$id'");
}
$seite = $_GET["seite"];
if ($seite == '')
    $seite = 1;
$zaehler = 0;


echo '<h2>Posteingang</h2><br /><div class="default"><table class="invitetable" style="text-align:center;">';
echo '<tr><th>Empfangsdatum</th><th>Absender</th><th>Betreff (klicken um Nachricht zu lesen)</th><th>lesen</th><th>l&ouml;schen</th></tr>';
$neuPostZaehler = 0;
$postquery = mysqli_query($verbindung,"SELECT * FROM mail WHERE empfaenger='$id' AND del='0' ORDER BY datum DESC"); //id einsetzen
while ($post = mysqli_fetch_array($postquery)) {    //Abfrage der accountdaten
    $abs = new Account($post["absender"]);
    $zaehler++;
    if ($zaehler >= 1 + ($seite - 1) * 20 && $zaehler <= $seite * 20) {

        if ($post["neu"] == 1) {    //neue post
            echo '<tr><td><span style="font-weight:bold">', gerdatum($post["datum"]), '</span></td><td><span style="font-weight:bold">', $abs->nickname;
            echo '</span></td><td><span style="font-weight:bold"><a class="default" href="viewmail.php?brief=', $post["id"], '">', $post["betreff"], '</a></span></td>';
            echo '<td><span style="font-weight:bold">';

            $bu = new Button("viewmail.php?brief=" . $post["id"], "lesen");
            $bu->printme();

            echo '</span></td><form action="mail.php" method="post"><input type="hidden" name="delpost" value="', $post["id"], '"><td>';

            $bu = new Button("", "löschen");
            $bu->printme();

            echo '</td></form></tr>';
            $neuPostZaehler++; //Zaehlen der neuen Post
        }    //Ende if abfrage
        else {   //keine neue Post ( nicht markiert )
            echo '<tr><td>', gerdatum($post["datum"]), '</td><td>', $abs->nickname;
            echo '</td><td><a class="general" href="viewmail.php?brief=', $post["id"], '">', $post["betreff"], '</a></td>';
            echo '<td><span style="font-weight:bold">';

            $bu = new Button("viewmail.php?brief=" . $post["id"], "lesen");
            $bu->printme();

            echo '</span></td><form action="mail.php" method="post"><input type="hidden" name="delpost" value="', $post["id"], '"><td>';
            
            $bu = new Button("", "löschen");
            $bu->printme();
            
            echo '</td></form></tr>';
        }   //Ende else block
    }
}
echo '</table></div>';
$einfaerben = false;
for ($o = 1; $o <= ceil($zaehler / 20); $o++) {
    if ($zaehler >= 1 + ($o - 1) * 20) {
//echo '!!zaheler: ',$zaehler,'  !!! ergebnis:',1+($o-1)*20,'<br />';
        if ($seite == $o)
            $einfaerben = true;
        echo $o == $seite ? '<a href="mail.php?seite=' . $o . '"><span style="color:red;">' . $o . '</span></a>' : '<a href="mail.php?seite=' . $o . '">' . $o . '</span>', $o == ceil($zaehler / 20) ? '' : ',';
    }
}
include("foot.php");
?>
