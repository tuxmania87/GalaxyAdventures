<?php
include("head.php");
include("navlogged.php");
include("klassen.php");
$seite = $_GET["seite"];
if ($seite == '')
    $seite = 1;
$zaehler = 0;
?>
<h3>Tabelle der Mitspieler</h3>
<table class="invitetable"><tr>
        <th>ID</th><th>Spielername</th><th>Message</th></tr>
    <?php
    $abfrage = mysql_query("SELECT * FROM `account` WHERE id != 9 ORDER BY id ASC");
    while ($user = mysql_fetch_array($abfrage)) {
        $acc = new Account($user["id"]);
        $zaehler++;
        if ($zaehler >= 1 + ($seite - 1) * 25 && $zaehler <= $seite * 25) {
            echo '<tr><td>', $user["id"], '</td><td><a href="userinfo.php?id=', $user["id"], '">', $acc->nickname, '</a>&nbsp;&nbsp;';
            if (isonline($user["aktion"]))
                echo '<span style="color:green;font-weight:bold;">Online</span>';
            echo' </td><td>', $user["id"] == 2 || $user["id"] == 9 ? '-' : '<a href="newmail.php?to=' . $user["id"] . '">Nachricht senden</a>', '</td></tr>';
        }
    }
    echo '</table>';
    $einfaerben = false;
    for ($o = 1; $o <= ceil($zaehler / 25); $o++) {
        if ($zaehler >= 1 + ($o - 1) * 25) {
//echo '!!zaheler: ',$zaehler,'  !!! ergebnis:',1+($o-1)*20,'<br />';
            if ($seite == $o)
                $einfaerben = true;
            echo $o == $seite ? '<a href="player.php?seite=' . $o . '"><span style="color:red;">' . $o . '</span></a>' : '<a href="player.php?seite=' . $o . '">' . $o . '</span>', $o == ceil($zaehler / 25) ? '' : ', ' ,'</a>';
        }
    }
    include("foot.php");
    ?>
