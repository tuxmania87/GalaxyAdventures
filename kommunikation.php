<?php
include 'head.php';
include 'navlogged.php';
include 'klassen.php';
$ich = new Account($_SESSION['Id']);
include_once 'auth.php';
$userId = requireLogin();
{
    $id = $_SESSION['Id'];

    if (isset($_GET['accept']) && ctype_digit($_GET['accept'])) {
        mysqli_query($verbindung, 'update channelabo set status=1 where status=2 and cid='.intval($_GET['accept']).' and uid='.$id);
    }

    if (isset($_GET['decline']) && ctype_digit($_GET['decline'])) {
        mysqli_query($verbindung, 'delete from channelabo where status=2 and cid='.intval($_GET['accept']).' and uid='.$id);
    }

    ?><h3>Kommunikation</h3>
    <?php
    $but = new Button('newmail.php', 'neue Nachricht verfassen');
    $but->printme();
    echo '<br />';
    $check = mysqli_query($verbindung, "SELECT * FROM mail WHERE empfaenger='$id' AND neu=1 AND del=0");
    $but = new Button('mail.php', 'Posteingang '.(mysqli_num_rows($check) > 0 ? '<span style="color:yellow;">neue Nachrichten!</span>' : ''));
    $but->printme();
    echo '<br />';
    $but = new Button('sentmail.php', 'gesendete Nachrichten');
    $but->printme();
    echo '<br /><br />';
    $but = new Button('logbuch.php', 'Logbuch');
    $but->printme();
    echo '<br />';
    $but = new Button('konto.php', 'Warenb&ouml;rse');
    $but->printme();
    echo '<br />';
    $but = new Button('sebay.php', 'Schiffsversteigerung');
    $but->printme();
    echo '<br /><br />';

    $q = mysqli_query($verbindung, 'select cid from channelabo where status=2 and uid='.intval($_SESSION['Id']));

    if (mysqli_num_rows($q) > 0) {
        echo '<table class="invitetable">';
    }

    while ($r = mysqli_fetch_array($q)) {
        $t_cid = $r['cid'];
        $ch = new Channel($t_cid);
        echo '<tr><td>Einladung für <span style="font-weight:bold;">'.$ch->caption.'</td><td>eingeladen von '.$ch->founder->nickname.'</span></td>';
        echo '<td>';
        $bu = new Button('kommunikation.php?accept='.$ch->id, '<span style="color:green;">annehmen</span>');
        $bu->printme();
        echo '</td>';
        echo '<td>';
        $bu = new Button('kommunikation.php?decline='.$ch->id, '<span style="color:red;">ablehnen</span>');
        $bu->printme();
        echo '</td></tr>';
    }

    if (mysqli_num_rows($q) > 0) {
        echo '</table><br />';
    }

    ?>
    <table class="liste">
        <tr><th>ID</th><th>Kanal</th><th>Nachrichten</th><th>RPG</th><th>A</th><th>E</th><th>Gründer</th></tr>
    <?php

    $list = Channel::getList();

    for ($i = 0; $i < count($list); ++$i) {
        $q = mysqli_query($verbindung, 'select id from kn where channel='.$list[$i]->id);
        $anzahl = mysqli_num_rows($q);

        echo '<tr><td>'.$list[$i]->id.'</td><td><a href="knread.php?channel='.$list[$i]->id.'">'.$list[$i]->caption.'</a></td><td>'.$anzahl.'</td><td>'.($list[$i]->rpg == 1 ? 'ja' : 'nein').'</td>';
        echo '<td><a href="knread.php?channel='.$list[$i]->id.'"><img src="images/misc/read.png" border="0" onmouseover="Tip(\'<b>Nachrichten lesen</b>\')" onmouseout="UnTip()" /></a></td><td><a href="knwrite.php?channel='.$list[$i]->id.'"><img src="images/misc/write.png" border="0" onmouseover="Tip(\'<b>Nachricht erstellen</b>\')" onmouseout="UnTip()" /></a></td><td>'.$list[$i]->founder->nickname.'</td></tr>';
    }

    ?>
    </table>

    <br />
    <?php
    $but = new Button('allianz.php', 'Allianzverwaltung');
    $but->printme();
    echo '<br />';
    $but = new Button('vertrag.php', 'Diplomatie/Vertr&auml;ge');
    $but->printme();
    echo '<br /><br />';
    $but = new Button('map.php?x=180&y=110.php', 'Karte');
    $but->printme();
    echo '<br />';
    $but = new Button('minimap/minimap.png', 'Galaxis als Karte');
    $but->printme();
    echo '<br /><br />';
    $but = new Button('player.php', 'Liste der Spieler');
    $but->printme();
    echo '<br /><br /><hr /><br />';
    $but = new Button('best.php', 'Bestenliste');
    $but->printme();
    echo '<br />';
    $but = new Button('statistik.php', 'Statistiken');
    $but->printme();
    echo '<br /><br /><hr /><br />';
    $but = new Button('irc/', 'IRC CHAT(java)');
    $but->printme();
    echo '<br />';
    $but = new Button('', 'Forum');
    $but->printme();
    echo '<br />';
}
include 'foot.php';
?>
