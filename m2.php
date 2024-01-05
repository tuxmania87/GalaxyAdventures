<?php

include 'klassen.php';
include_once 'connect.php';

$verbindung = get_verbindung();

// CHEATSCHUTZ 1
$betray = false;
if (!ctype_digit($_GET['sid'])) {
    $betray = true;
}
if (!ctype_digit($_GET['tid'])) {
    $betray = true;
}

// if($betray) die();

$sid = $_GET['sid'];
$tid = $_GET['tid'];

$schiff = new Schiffe($sid);

$test1 = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE x='".$schiff->position->x."' AND y='".$schiff->position->y."' AND `system`='".$schiff->position->system->id."' AND id='$tid'");
if (mysqli_num_rows($test1) == 1) {
    $target = new Schiffe($tid);
} else {
    $target = new Planeten($tid);
}

// CHEATSCHUTZ 2
if ($schiff->besitzer->id != $_SESSION['Id']) {
    $betray = true;
}
// if($betray) die();

echo '<h3>Nachricht ',mysqli_num_rows($test1) == 1 ? 'der' : 'des Planeten',' ',$target->name,' (',$target->id,') aus Sektor ',$target->position->x,'|',$target->position->y,'</h3>';
echo '<div class="box" style="width:500px;">',nl2br(pruefetext($target->nachricht)),'</div><br />';

?>
</body>
</html>
