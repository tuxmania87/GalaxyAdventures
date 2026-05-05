<?php
include 'head.php';
include 'navlogged.php';
include 'klassen.php';
include_once 'connect.php';

$verbindung = get_verbindung();
include_once 'auth.php';
$ich = getLoggedInAccount();
$allianz = \$ich->allianz->id;

$do = $_POST['do'];

if ($do == 1) { // Allianz erstellen
    $selfid = $_SESSION['Id'];
    $name = pruefetext($_POST['allianzname']);
    $tag = pruefetext($_POST['allianztag']);
    $info = $_POST['allianzbeschreibung'];
    $hit = false;
    $counter = 0;
    $abf = mysqli_query($verbindung, 'SELECT * FROM allianz');
    while ($t = mysqli_fetch_array($abf)) {
        if (strtolower($t['name']) == strtolower($name)) {
            $hit = true;
        }
        if ($t['id'] >= $counter) {
            $counter = $t['id'];
        }
    }
    ++$counter;
    if (!$hit) {
        mysqli_query($verbindung, "INSERT INTO allianz (id,name,tag,info,leiter) VALUES ('$counter','$name','$tag','$info','$selfid')");
        mysqli_query($verbindung, "UPDATE account SET allianz='$counter' WHERE id='$selfid'");
    } else {
        echo 'Allianz existiert bereits!<br />';
    }
    $allianz = $counter;
}

if ($do == 3) { // text aendern
    $neuertext = $_POST['info'];
    mysqli_query($verbindung, "UPDATE allianz SET info='$neuertext' WHERE id='$allianz'");
}

if ($do == 4) { // inviten
    $inviteid = $_POST['inviteid'];
    $aname = $ich->allianz->name;
    $aleiter = $ich->allianz->leiter->nickname;
    $idleiter = $ich->allianz->leiter->id;
    $datum = date('Y-m-d H:i:s');
    $subject = "Einladung in die Allianz $aname!";
    $message = "---automatische Nachricht---\n\n $aleiter, Leiter der Allianz: $aname, hat dich in seine Allianz eingeladen\nWenn du beitreten m&ouml;chtest, klicke bitte auf den untenstehenden Knopf, ansonsten l&ouml;sche diese Nachricht.\n";
    $message .= '<form action="allianz.php" method="post"><input type="hidden" name="ally" value="';
    $message .= $allianz;
    $message .= '"><input type="hidden" name="do" value="5"><input type="submit" value="beitreten!"></form>';
    mysqli_query($verbindung, "INSERT INTO mail (datum,neu,absender,empfaenger,betreff,inhalt) VALUES ('$datum','1','$idleiter','$inviteid','$subject','$message')");
    mysqli_query($verbindung, "UPDATE account SET neu=1 WHERE id='$inviteid'");
    echo 'Spieler eingeladen...!';
}

if ($do == 5 && $allianz == 0) {
    $toally = $_POST['ally'];
    $op = mysqli_query($verbindung, "SELECT * FROM allianz WHERE id='$toally'");
    while ($p = mysqli_fetch_array($op)) {
        $toallyname = $p['name'];
    }
    $selfid = $_SESSION['Id'];
    $allianz = $toally;
    if ($toally != '') {
        mysqli_query($verbindung, "UPDATE account SET allianz='$toally' WHERE id='$selfid'");
    } else {
        $allianz = 0;
    }
    $tmpnam = "Einladung in die Allianz $toallyname!";
    mysqli_query($verbindung, "DELETE FROM mail WHERE betreff='$tmpnam'") or exit($verbindung->error);
}

if ($do == 6) { // leave allianz
    $selfid = $_SESSION['Id'];
    mysqli_query($verbindung, "UPDATE account SET allianz=0 WHERE id='$selfid'");
    $allianz = 0;
}

if ($do == 7) { // allianz aufloesen
    mysqli_query($verbindung, "UPDATE account SET allianz=0 WHERE allianz='$allianz'");
    mysqli_query($verbindung, "DELETE FROM allianz WHERE id='$allianz'");
    $allianz = 0;
}

if ($do == 9) { // text aendern
    $neueinfo = $_POST['infointern'];
    mysqli_query($verbindung, "UPDATE allianz SET intern='$neueinfo' WHERE id='$allianz'");
}

if ($_GET['do'] == 10 && $allianz > 0) {
    $kick = ceil($_GET['user']);
    $ttvar = mysqli_query($verbindung, "SELECT leiter FROM allianz WHERE id='$allianz'") or exit($verbindung->error);
    $ttvar2 = mysqli_fetch_array($ttvar);
    $aleiter = $ttvar2['leiter'];
    if ($_SESSION['Id'] == $aleiter) { // kicken
        mysqli_query($verbindung, "UPDATE account SET allianz=0 WHERE id='$kick'");
    }
}

echo '<h2>Allianzverwaltung</h2>';
if ($allianz == 0) {
    echo 'Du bist in keiner Allianz!<br />Du hast grunds&auml;tzlich 2 M&ouml;glichkeiten:<br /><br />1. Du suchst dir aus der Liste eine Allianz aus informierst dich durch klicken, was ihre Ziele sind, und nimmst dann Kontakt mit dem Leiter auf.<br /><br />2. M&ouml;glichkeit: Du gr&uuml;ndest eine Allianz!<br /><br /><h2>Allianzen</h2>';
    echo '<table class="invitetable" style="text-align:center;"><tr><th>Allianzname</th><th>Allianzk&uuml;rzel</th><th>Allianzleiter</th></tr>';
    $abfrage = mysqli_query($verbindung, 'SELECT * FROM allianz ORDER by id');
    while ($allianz = mysqli_fetch_array($abfrage)) {
        $aleader = new Account($allianz['leiter']);
        echo '<tr><td><a href="allianzinfo.php?id=', $allianz['id'], '">', $allianz['name'], '</a></td><td>', $allianz['tag'], '</td><td><a href="newmail.php?to=', $allianz['leiter'], '">', $aleader->nickname, '</a></td></tr>';
    }
    echo '</table><br />';
    ?>
    <br />
    <h3>2. M&ouml;glichkeit - Allianz gr&uuml;nden:</h3>
    <form action="allianz.php" method="post">
        <table>
            <tr><td>Name der Allianz</td><td><input type="text" name="allianzname"></td></tr>
            <tr><td>Allianzk&uuml;rzel</td><td><input type="text" name="allianztag"></td></tr>
            <tr><td>Beschreibung der Allianz</td><td><textarea name="allianzbeschreibung" rows="10" cols="50">Allianztext</textarea></td></tr>
        </table>
        <input type="hidden" name="do" value="1">
        <input type="submit" value="Allianz gr&uuml;nden">
    </form>
    <?php
} else {
    $abfrage = mysqli_query($verbindung, "SELECT * FROM allianz WHERE id='$allianz'");
    while ($ally = mysqli_fetch_array($abfrage)) {
        $aktally = new Allianz($allianz);
        $leiter = $ally['leiter'];
        $aktleiter = new Account($aktally->leiter);
        echo '<h3>Willkommen in der Allianz: ', $ally['name'], ' (Allianz-ID: ', $ally['id'], ')</h3>';
        echo '<table class="invitetable">';
        echo '<tr><th>Leiter:</th><td>', $aktleiter->nickname, '</td></tr>';
        // allianzinfo
        if ($ally['leiter'] == $_SESSION['Id']) {
            if ($_GET['do'] == 2) {
                echo '<form action="allianz.php" method="post"><input type="hidden" name="do" value="3"><tr><td>Allianzbeschreibung:</td><td><textarea name="info" rows="10" cols="50">', $ally['info'], '</textarea><br /><input type="submit" value="eintragen..."></td></tr></form>';
            } else {
                echo '<tr><th>Allianzbeschreibung:</th><td>', nl2br(pruefetext($ally['info'])), '<br /><br /><a href="allianz.php?do=2"><span style="color:yellow;">Beschreibung &auml;ndern</span></a></td></tr>';
            }
        } else {
            echo '<tr><th>Allianzbeschreibung:</th><td>', nl2br(pruefetext($ally['info'])), '</td></tr>';
        }
        // interne Infos
        if ($ally['leiter'] == $_SESSION['Id']) {
            if ($_GET['do'] == 8) {
                echo '<form action="allianz.php" method="post"><input type="hidden" name="do" value="9"><tr><td>interne News:</td><td><textarea name="infointern" rows="10" cols="50">', $ally['intern'], '</textarea><br /><input type="submit" value="eintragen..."></td></tr></form>';
            } else {
                echo '<tr><th>interne News:</th><td>', nl2br(pruefetext($ally['intern'])), '<br /><br /><a href="allianz.php?do=8"><span style="color:yellow;">News &auml;ndern</span></a></td></tr>';
            }
        } else {
            echo '<tr><th>interne News:</th><td>', nl2br(pruefetext($ally['intern'])), '</td></tr>';
        }
        // -->
        echo '</table><br />';
        echo '<br /><h3>Mitglieder</h3><table class="invitetable" style="text-align:center;"><tr><tH>Spieler</th><th>->[]-></th></tr>';

        $abfr = mysqli_query($verbindung, "SELECT * FROM account WHERE allianz='$allianz'");
        while ($user = mysqli_fetch_array($abfr)) {
            $usr = new Account($user['id']);
            echo '<tr><td>', $usr->nickname, '</td><td>';
            if ($_SESSION['Id'] == $leiter && $user['id'] != $_SESSION['Id']) {
                $bu = new Button('allianz.php?do=10&user='.$user['id'], 'entfernen');
                $bu->printme();
            } else {
                echo '-';
            }
            echo '</td></tr>';
        }
        echo '</table><br />';
        if ($ally['leiter'] == $_SESSION['Id']) {
            echo '<br /><h3>Spieler einladen!</h3><form action="allianz.php" method="post"><input type="hidden" name="do" value="4">Spieler-ID: <input type="text" size="3" name="inviteid">&nbsp;&nbsp;';
            $bu = new Button('', 'einladen');
            $bu->printme();
            echo '</form><br /><br />';
        }
        if ($ally['leiter'] != $_SESSION['Id']) {
            echo '<form action="allianz.php" method="post"><input type="hidden" name="do" value="6">';
            $bu = new Button('', 'Allianz verlassen');
            $bu->printme();
            echo '</form>';
        } else {
            echo '<form action="allianz.php" method="post"><input type="hidden" name="do" value="7">';
            $bu = new Button('', 'Allianz auflösen');
            $bu->printme();
            echo '</form>';
        }
        echo '<br />';
        echo '<table class="invitetable" style="text-align:center;"><tr><th>Allianzname</th><th>Allianzk&uuml;rzel</th><th>Allianzleiter</th></tr>';
        $abfrage = mysqli_query($verbindung, 'SELECT * FROM allianz ORDER by id');
        while ($allianz = mysqli_fetch_array($abfrage)) {
            $aktally = new Allianz($allianz['id']);
            $aktleiter = new Account($aktally->leiter);
            echo '<tr><td><a href="allianzinfo.php?id=', $allianz['id'], '">', $allianz['name'], '</a></td><td>', $allianz['tag'], '</td><td><a href="newmail.php?to=', $allianz['leiter'], '">', $aktleiter->nickname, '</a></td></tr>';
        }
        echo '</table><br />';
    }
}

include 'foot.php';
?>
