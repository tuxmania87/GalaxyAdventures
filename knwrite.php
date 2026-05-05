<?php
include("head.php");
include("navlogged.php");
include("klassen.php");
//CHEATSCHUTZ ANFANG


$betray = false;
if ($_SESSION["Id"] <= 0)
    die("Fehler: ID not valid");
$ich = new Account($_SESSION["Id"]);

//CHEATSCHUTZ ENDE

$channel = $_GET["channel"];
$pid = ctype_digit($_GET["pid"]) ? $_GET["pid"] : 0;


//if($queryvar=='') { $queryvarx='skn WHERE 1=1'; $knid2="KN"; }


if ($_POST["do"] == 1) {
    $bezug = $knid2 . "-" . $pid;
    $datum = date("Y-m-d H:i:s");
    $id = $_SESSION["Id"];
    $text = $_POST["text"];
    changeit($text);
    if ($id > 0) {
        mysqli_query($verbindung, "INSERT INTO kn (datum,autor,text,bezug,channel) VALUES ('$datum','$id','$text','$bezug','$channel')") or die(mysqli_error($verbindung));
        echo '<meta http-equiv="Refresh" CONTENT="0;URL=knread.php?channel=', $channel, '">';
    } else
        echo 'nicht eingeloggt';
    
}

/*
  if($channel==1) echo '<h3>Eintragen ins Kommunikationsnetzwerk ( Bitte nur <acronym title="Rollenspiel - mehr dazu im Forum">RPG</acronym> Texte)</h3>';
  if($channel==2) echo '<h3>Eintragen in die historischen Dokumente ( Bitte nur <acronym title="Rollenspiel - mehr dazu im Forum">RPG</acronym> Texte)</h3>';
  if($channel==4) echo '<h3>Eintragen in das Handelsnetzwerk ( Bitte nur <acronym title="Rollenspiel - mehr dazu im Forum">RPG</acronym> Texte)</h3>';
  if($channel==3) echo '<h3>Fragen zum Spiel</h3>';
  if($channel==5) echo '<h3>Eintragen in Allianzkanal</h3>';
  if($channel==6) echo '<h3>Eintragen in das rekrutierungsnetzwerk ( Bitte nur <acronym title="Rollenspiel - mehr dazu im Forum">RPG</acronym> Texte)</h3>';
 */

$list = Channel::getList();

echo '<h3>Eintragen ins Netzwerk</h3>';

echo 'erlaubte Formatierungen im Text: <a href="gacode.htm" target="_blank"><span style="color:yellow;font-weight:bold;">GA-Code</span></a><br /><br />';

echo '<form action="knwrite.php?channel=', $channel, '&pid=', $pid, '" method="post">';
echo 'Eintragen in: ';


    echo '<select name="netz">';
    for ($i = 0; $i < sizeof($list); $i++) {
        echo '<option value="' . $list[$i]->id . '" ';
        if ($list[$i]->id == $channel)
            echo 'selected="true"';
        echo '>' . $list[$i]->caption . ' (' . $list[$i]->id . ')</option>';
    }

    echo '</select><br /><br />';


if ($pid > 0) {
    echo 'Du beziehst dich auf:<br />';
    $abfrage = mysqli_query($verbindung, "SELECT * FROM kn where channel=".$channel." AND id=" . $pid . " ORDER BY id DESC");
    while ($t = mysqli_fetch_array($abfrage)) {
        $tid = $t["autor"];
        $blub = new Account($t["autor"]);
        $avatar = $blub->bild;
        if ($avatar == '')
            $avatar = 'siedler.gif';
        echo '<table class="bordered" width="100%"><tr><td width="70%"><a href="userinfo.php?id=', $t["autor"], '">', $blub->nickname, '</a></td><td>', gerdatum($t["datum"]), '</td></tr></table>';
        echo '<table class="bordered2" width="100%"><tr><td width="80px"><center><img src="', $avatar, '" border="0" /></center></td><td>';
        if ($t["autor"] == 1)
            echo '<font color="#00C0FF">';
        echo nl2br($t["text"]);
        if ($t["autor"] == 1)
            echo '</font>';
        echo '</td></tr></table><br/>';
    }
}

if ($_POST["do"] == 2)
    echo '<br /><div style="width:400px;padding:4px;border:1px solid darkred;">', nl2br(pruefetext($_POST["text"])), '</div><br />';
?>


<input type="hidden" name="do" value="2">
<textarea name="text" rows="10" cols="50"><?php echo $_POST["text"]; ?></textarea><br /><br />
<input type="radio" name="do" value="2" <?php if ($_POST["do"] != 2) echo 'checked="true"'; ?> > Vorschau<br />
<?php if ($_POST["do"] == 2) echo '<input type="radio" name="do" value="1" checked="true"> Senden<br />'; ?>
<br />
<input type="submit" value="Anfrage senden...">
</form>

<?php
include("foot.php");
?>
