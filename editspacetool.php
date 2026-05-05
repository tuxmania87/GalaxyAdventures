<?php
session_start();
include_once("klassen.php");

function bool2string($b) {
    return $b ? "ja" : "nein";
}
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <h3>Bitte Instrument w&auml;hlen:</h3>
        <table class="liste"><tr><th>Bild</th><th>Name</th><th>passierbar</th><th>Einflugkosten</th><th>tödlich</th><th>Waffen/Schilde</th><th>Erz</th><th>Deuterium</th></tr>
            <?php
            $l = Weltraumfelder::getList();
            for ($i = 0; $i < count($l); $i++) {
                if ($l[$i]->id > 25 || $l[$i]->id < 11) {
                    echo '<tr><td><a style="text-decoration:none;color:white;" href="editspace.php?pinsel=' . $l[$i]->id . '&x=', $_GET["x"], '&y=', $_GET["y"], '">';
                    echo '<img src="images/' . $l[$i]->bild . '" border="0" /></a></td><td><a href="editspace.php?pinsel=' . $l[$i]->id . '&x=', $_GET["x"], '&y=', $_GET["y"], '">' . $l[$i]->name . '</a></td><td>' . bool2string($l[$i]->passierbar) . '</td><td>' . $l[$i]->einflugkosten . '</td><td>' . bool2string($l[$i]->deadly) . '</td><td>' . bool2string($l[$i]->hide) . '</td><td>' . $l[$i]->erz . '</td><td>' . $l[$i]->deut . '</td></tr>';
                }
            }

            $l = Systemfelder::getList();
            for ($i = 0; $i < count($l); $i++) {
                    echo '<tr><td><a style="text-decoration:none;color:white;" href="editspace.php?pinsel=s' . $l[$i]->id . '&x=', $_GET["x"], '&y=', $_GET["y"], '">';
                    echo '<img src="images/systems/' . $l[$i]->bild . '" border="0" /></a></td><td><a href="editspace.php?pinsel=s' . $l[$i]->id . '&x=', $_GET["x"], '&y=', $_GET["y"], '">' . $l[$i]->name . '</a></td</tr>';
               
            }
            ?>
        </table>
    </body>
</html>