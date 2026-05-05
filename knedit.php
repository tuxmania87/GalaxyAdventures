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

$editvar = mysqli_query($verbindung, "SELECT * FROM kn_log WHERE channel='$channel' AND pid='$pid' ORDER BY id DESC LIMIT 1");
if (mysqli_num_rows($editvar) > 0) {
    while ($erow = mysqli_fetch_array($editvar))
        $posterid = $erow["autor"];
    $editid = $t["autor"];
} else {
    $editvar2 = mysqli_query($verbindung, "SELECT autor FROM kn WHERE id='.$pid.' and channel='.$channel.'");
    $editvar2 = mysqli_fetch_array($editvar2);
    $posterid = $editvar2[0];
}


$ich = new Account($_SESSION["Id"]);
if ($ich->moderator == 0 && $ich->id != $posterid)
    die("unauthoriserter Zugriff!");

$channelset = $_POST["netz"];

if ($_POST["do"] == 1) {
    $bezug = $knid2 . "-" . $pid;
    $datum = date("Y-m-d H:i:s");
    $id = $_SESSION["Id"];
    $text = $_POST["text"];
    changeit($text);

    if ($id > 0) {
        mysqli_query($verbindung, "UPDATE kn SET datum='$datum',autor='" . $_SESSION["Id"] . "',text='$text',channel='$channel' WHERE id='$pid'") or die(mysqli_error($verbindung));
        echo '<meta http-equiv="Refresh" CONTENT="0;URL=knread.php?channel=', $channel, '">';
    } else
        echo 'nicht eingeloggt';
    
    
}

$abfrage = mysqli_query($verbindung, "SELECT * FROM kn where channel='". $channel ."' AND id='" . $pid . "'");
while ($row = mysqli_fetch_array($abfrage)) {
    $edittext = $row["text"];
}


echo '<h3>Editieren ins Netzwerk</h3>';

echo '<form action="knedit.php?channel=', $channel, '&pid=', $pid, '" method="post">';

if ($_POST["do"] == 2)
    echo '<br /><div style="width:400px;padding:4px;border:1px solid darkred;">', nl2br(pruefetext($_POST["text"])), '</div><br />';
?>


<input type="hidden" name="do" value="2">
<textarea name="text" rows="10" cols="50"><?php if (sizeof($_POST) > 0) echo $_POST["text"]; else echo $edittext; ?></textarea><br /><br />
<input type="radio" name="do" value="2" <?php if ($_POST["do"] != 2) echo 'checked="true"'; ?> > Vorschau<br />
<?php if ($_POST["do"] == 2) echo '<input type="radio" name="do" value="1" checked="true"> Senden<br />'; ?>
<br />
<input type="submit" value="Anfrage senden...">
</form>

<?php
include("foot.php");
?>
