<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

function text2num($text) {
    switch ($text) {
        case "KN": return 1;
            break;
        case "ARCHIV": return 2;
            break;
        case "HILFE": return 3;
            break;
        case "HANDEL": return 4;
            break;
        case "ALLIANZ": return 5;
            break;
    }
}

function showkn($channel, $pid, $vor) {
    $ich = new Account($_SESSION["Id"]);
    if ($pid == 0 || $channel == 0)
        return;

    $dec = null;

    $vorfeld = explode("-", $vor);

    $editid = null;
    $posterid = null;
//*** EDITPRUFUNG
    $editvar = mysqli_query($verbindung, "SELECT * FROM kn_log WHERE channel='$channel' AND pid='$pid' ORDER BY id ASC LIMIT 1");
    if (mysqli_num_rows($editvar) > 0) {
        while ($erow = mysqli_fetch_array($editvar))
            $posterid = $erow["autor"];
        $editid = -2;
    }




    $abfrage = mysqli_query($verbindung, "SELECT * FROM kn WHERE channel=".$channel." and bezug='" . $knid . "-" . $pid . "' AND bezug != '$vor'");
    if (mysqli_num_rows($abfrage) > 0) {
        while ($t = mysqli_fetch_array($abfrage)) {
            if (!(text2num($vorfeld[0]) == 1 && $vorfeld[1] == $t["id"])) {
                $usr = new Account($t["autor"]);
                echo '<a href="zusammenhang.php?channel='.$channel.'&pid=', $t["id"], '"><span style="color:red;font-weight:bold;">zur (anderen) Antwort von ', $usr->nickname, ' auf:</span></a><br />';
            }
        }
    }


    $abfrage = mysqli_query($verbindung, "SELECT * FROM kn where channel=".$channel."  AND id=" . $pid . " ORDER BY id DESC");
    while ($t = mysqli_fetch_array($abfrage)) {
        if ($editid == -2)
            $editid = $t["autor"];
        if (!isset($posterid))
            $posterid = $t["autor"];
        $tid = $posterid;
        $blub = new Account($posterid);
        $avatar = $blub->bild;
        if ($avatar == '')
            $avatar = 'siedler.gif';
        $explodevar = explode("-", $t["bezug"]);
        $pre = $explodevar[1];
        echo '<table class="bordered" width="100%"><tr><td width="55%"><a href="userinfo.php?id=', $t["autor"], '">', $blub->nickname, '</a></td><td><span style="color:silver;">Bezug auf: ', $explodevar[1] == 0 ? '-' : $t["bezug"], '</td><td><span style="color:red;">Eintrag: ', $knid, '-', $t["id"], '</td><td>', gerdatum($t["datum"]), '</td></tr></table>';
        echo '<table class="bordered2" width="100%"><tr><td width="80px"><center><img src="', $avatar, '" border="0" /></center></td><td>';
        if ($t["autor"] == 1)
            echo '<font color="#00C0FF">';
        echo nl2br(pruefetext($t["text"]));

// *** MOD VIEW
        if ($ich->moderator == 1) {
            $editvar = mysqli_query($verbindung, "SELECT * FROM kn_log WHERE channel='$channel' AND pid='" . $t["id"] . "' ORDER BY id DESC");
            while ($erow = mysqli_fetch_array($editvar)) {
                $editusr = new Account($erow["autor"]);
                echo '<br /><br /><span style="color:yellow;font-weight:bold;">Version vom :', gerdatum($erow["datum"]), '</span> editiert von ', $editusr->nickname, '<br />';
                echo nl2br(pruefetext($erow["text"]));
            }
        }
// ** ENDE MODVIEW
        if (isset($editid)) {
            $eusr = new Account($editid);
            echo '<br /><br />[ <span style="color:red;">editiert von ', $eusr->nickname, ' ]';
        }

        if ($t["autor"] == 1)
            echo '</font>';
        echo '</td></tr></table>';
    }
    echo '<br/>';

    switch ($explodevar[0]) {
        case "KN": $neuchannel = 1;
            break;
        case "ARCHIV": $neuchannel = 2;
            break;
        case "HILFE": $neuchannel = 3;
            break;
        case "HANDEL": $neuchannel = 4;
            break;
        case "ALLIANZ": $neuchannel = 5;
            break;
        default: $neuchannel = 0;
            break;
    }
    showkn($neuchannel, $pre, $knid . "-" . $pid);
}

$channel = $_GET["channel"];

echo '<a href="knread.php?channel=', $channel, '">zur&uuml;ck</a><br /><br />';
echo '<div style="min-width:700px;max-width:950px;">';

$pid = $_GET["pid"];
showkn($channel, $pid, null);

echo '<a href="knread.php?channel=', $channel, '">zur&uuml;ck</a>';
echo '</div>';
?>
