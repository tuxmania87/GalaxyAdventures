<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';
include_once 'connect.php';

    if (isset($fromschiff)) {
        $from = new Schiffe($fromschiff);
    }
    if (isset($fromplanet)) {
        $from = new Planeten($fromplanet);
    }

    if (isset($toschiff)) {
        $to = new Schiffe($toschiff);
    }
    if (isset($toplanet)) {
        $to = new Planeten($toplanet);
    }

    if ($from->position->x == $to->position->x && $from->position->y == $to->position->y && ($from->position->orbit == $to->position->orbit || $to->typ != 's' || $from->typ != 's') && $from->position->system->id == $to->position->system->id) {
        if ($_POST['do'] == 1) {
            $amount = ceil($_POST['zahl']);
            echo '!!',$amount,'!!';
            if ($from->energie < $amount) {
                $amount = $from->energie;
                echo 'Nicht genug Energie vorhanden: Wert ge&auml;ndert auf ',$amount,'<br />';
            }
            if ($to->energie + $amount > $to->maxenergie) {
                $amount = $to->maxenergie - $to->energie;
                echo 'Ziel hat nicht genug Platz: Wert angepasst auf ',$amount,'<br />';
            }
            if ($amount > 0) {
                $to->energie += $amount;
                $from->energie -= $amount;
                if ($to->typ == 's') {
                    mysqli_query($verbindung, "UPDATE schiffe SET energie='".$to->energie."' WHERE id='".$to->id."'");
                } else {
                    mysqli_query($verbindung, "UPDATE planeten SET energie='".$to->energie."' WHERE id='".$to->id."'");
                }
                if ($from->typ == 's') {
                    mysqli_query($verbindung, "UPDATE schiffe SET energie='".$from->energie."' WHERE id='".$from->id."'");
                } else {
                    mysqli_query($verbindung, "UPDATE planeten SET energie='".$from->energie."' WHERE id='".$from->id."'");
                }

                echo 'Transfer erfolgreich: Es wurden ',$amount,' Energieeinheiten &uuml;bertragen!<br />';
            } else {
                echo 'Betr&auml;ge die kleiner als 1 sind, werden nicht akzeptiert!';
            }
        }

        echo '<h3>Energietransfer - Wert korrigiert sich automatisch</h3>';
        echo '<form action="energie.php?';
        if ($from->typ == 's') {
            echo 'fs=',$from->id;
        } else {
            echo 'fp=',$from->id;
        }
        if ($to->typ == 's') {
            echo '&ts=',$to->id;
        } else {
            echo '&tp=',$to->id;
        }

        echo '" method="post"><input type="hidden" name="do" value="1"><input type="text" name="zahl"><br /><input type="submit" value="Energie senden"></form>';
    }
    if ($to->besitzer->id == $_SESSION['Id'] && $to->typ == 's') {
        echo '<br /><a href="schiffe.php?sid=',$to->id,'">vor zum Zielschiff</a><br />';
    }
    if ($to->besitzer->id == $_SESSION['Id'] && $to->typ != 's') {
        echo '<br /><a href="planet.php?pid=',$to->id,'">vor zum Zielplaneten</a><br />';
    }
    if ($from->typ == 's') {
        echo '<br /><a href="schiffe.php?sid=',$from->id,'">zur&uuml;ck zum Schiff</a>';
    } else {
        echo '<br /><a href="planet.php?pid=',$from->id,'">zur&uuml;ck zum Planeten</a>';
    }
}
include 'foot.php';
