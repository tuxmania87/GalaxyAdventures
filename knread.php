<?php

include("head.php");
include("navlogged.php");
include("klassen.php");
echo '<div style="min-width:700px;max-width:950px;">';
//CHEATSCHUTZ ANFANG
$self = new Account($_SESSION["Id"]);

$betray = false;
$testid = $_GET["sid"];
if (!isset($testid))
    $testid = $_GET["pid"];
if (!ctype_digit($_GET["seite"]))
    $_GET["seite"] = 1;
$tmp = mysql_query("SELECT besitzer FROM schiffe WHERE id='$testid'");
while ($testtmp = mysql_fetch_array($tmp))
    if ($_SESSION["Id"] != $testtmp["besitzer"])
        die("Fehler: Besitzer-ID Fehler");

$ich = new Account($_SESSION["Id"]);

//CHEATSCHUTZ ENDE

$seite = $_GET["seite"];
if ($seite == '')
    $seite = 1;
$zahl = 1;


$channel =$_GET["channel"];
if(!ctype_digit($channel)) 
    die("fehlerhafte Channel-ID");

$ch = new Channel($channel);

//check ACL
if(!$ch->public && $ch->founder->id != $self->id) {
    $q = mysql_query("select uid from channelabo where uid=".$self->id." and cid=".$ch->id." and status=1");
    if(mysql_num_rows($q) == 0) {
        die("Du bist nicht autorisiert diesen Kanal zu betrachten.");
        
    }
}

echo '<h2>'.$ch->caption.'</h2>';

echo '<span style="font-size:medium;">Dieser Kanal ist ';

if($ch->public) {
    echo '<span style="color:green;font-weight:bold;">öffentlich</span>';
} else {
    echo '<span style="color:red;font-weight:bold;">privat</span>';
}
echo '</span><br />';

if($ch->founder->id == $_SESSION["Id"]) {
    echo "<br />Da du der Gründer dieses Kanals bist, kannst du die kontrollieren, wer in diesem Kanal lesen/schreiben darf.<br />";
    $bu = new Button("knacl.php?cid=".$ch->id,"Zugriffe kontrollieren");
    $bu->printme();
    echo "<br /><br />";
}


$abfrage = mysql_query("SELECT * FROM kn where channel=".$ch->id." ORDER BY id DESC");
while ($t = mysql_fetch_array($abfrage)) {
    unset($postdate);
    if ($zahl >= $seite * 10 - 9 AND $zahl <= $seite * 10) {
        $editid = null;
//*** EDITPRUFUNG
        $editvar = mysql_query("SELECT * FROM kn_log WHERE channel='$ch->id' AND pid='" . $t["id"] . "' ORDER BY id ASC LIMIT 1");
        if (mysql_num_rows($editvar) > 0) {
            while ($erow = mysql_fetch_array($editvar)) {
                $posterid = $erow["autor"];
                $postdate = $erow["datum"];
            }
            $editid = $t["autor"];
        } else
            $posterid = $t["autor"];


// ****** AUTOREN
        $tid = $posterid;
        $blub = new Account($tid);
// ****** AVATAR
        $avatar = $blub->bild;
        if ($avatar == '')
            $avatar = 'avatar/siedler.gif';
        $explodevar = explode("-", $t["bezug"]);
        $postdate = isset($postdate) ? $postdate : $t["datum"];
        echo '<table class="invitetable" style="text-align:center;" width="100%"><tr><th><a href="newmail.php?to=', $blub->id, '"><img src="images/misc/kontakt.png" border="0" onmouseover="Tip(\'<b>Nachricht an Spieler</b>\')" onmouseout="UnTip()" /></a>', $ich->moderator == 1 || $ich->id == $posterid ? '<a href="knedit.php?channel=' . $channel . '&pid=' . $t["id"] . '"><img src="images/misc/bearbeiten.png" border="0" onmouseover="Tip(\'<b>Nachricht editieren</b>\')" onmouseout="UnTip()" /></a>' : '&nbsp;&nbsp;&nbsp;&nbsp;', '<a href="zusammenhang.php?channel=', $channel, '&pid=', $t["id"], '"><img src="images/misc/zusammenhang.png" border="0" onmouseover="Tip(\'<b>Zusammenhang darstellen</b>\')" onmouseout="UnTip()" /></a><a href="knwrite.php?channel=', $channel, '&pid=', $t["id"], '"><img src="images/misc/botschaft.png" border="0" onmouseover="Tip(\'<b>Auf diese Nachricht antworten / Auf diese Nachricht beziehen</b>\')" onmouseout="UnTip()" /></a>&nbsp;&nbsp;&nbsp;<a href="knwrite.php?channel=', $channel, '"><img src="images/misc/write.png" border="0" onmouseover="Tip(\'<b>neuen Beitrag ( ohne Zusammenhang ) verfassen</b>\')" onmouseout="UnTip()" /></a></th><th width="55%"><a href="userinfo.php?id=', $t["autor"], '">', $blub->nickname, '</a></th><th><span style="color:silver;">Bezug auf: ', $explodevar[1] == 0 ? '-' : $t["bezug"], '</th><th><span style="color:red;">ID ',$t["id"], '</th><th>', gerdatum($postdate), '</th></tr></table>';
        echo '<table class="invitetable" width="100%"><tr><td width="80px"><center><img src="', $avatar, '" border="0" /></center></td><td>';
        if ($posterid == 1)
            echo '<font color="#00C0FF">';
        echo nl2br(pruefetext($t["text"]));
// *** MOD VIEW
        if ($ich->moderator == 1) {
            $editvar = mysql_query("SELECT * FROM kn_log WHERE channel='$channel' AND pid='" . $t["id"] . "' ORDER BY id DESC");
            while ($erow = mysql_fetch_array($editvar)) {
                $editusr = new Account($erow["autor"]);
                echo '<br /><br /><span style="color:yellow;font-weight:bold;">Version vom ', gerdatum($erow["datum"]), '</span>  editiert von  ', $editusr->nickname, '<br />';
                echo nl2br(pruefetext($erow["text"]));
            }
        }
// ** ENDE MODVIEW
        if (isset($editid)) {
            $eusr = new Account($editid);
            echo '<br /><br />[ <span style="color:red;">editiert von ', $eusr->nickname, ' ]';
        }
        if ($posterid == 1)
            echo '</font>';
        echo '</td></tr></table><br/>';
    }
    $zahl++;
}
echo 'Seite: ';
//Seiten
$pq = ceil($zahl / 10);
for ($o = 1; $o <= $pq; $o++) {
    echo $o == 1 ? '' : ',', '<a href="knread.php?channel=', $channel, '&seite=', $o, '">', $seite == $o ? '<span style="color:red;">' : '', $o, '</span></a>';
}
//endeseite

echo '</div>';
include("foot.php");
?>
