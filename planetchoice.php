<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

//CHEATSCHUTZ ANFANG

$verbindung = get_verbindung();
$betray = false;
if ($_SESSION["Id"] <= 0)
    $betray = true;
if ($betray) {
    echo 'Du bist nicht <a href="login.php">eingeloggt</a> oder du versucht auf fremde Accounts zuzugreifen...';
} else {

//CHEATSCHUTZ ENDE
    if ($_POST["do"] == 5 && ctype_digit($_GET["pid"])) {  //Selbstzerst�rung
        $pid = $_GET["pid"];
        $pl = new Planeten($pid);
        if ($pl->besitzer->id != $_SESSION["Id"])
            echo 'Du versuchst einen fremden Planeten zu zerst&ouml;ren!'; else {
            mysqli_query($verbindung, "UPDATE planeten SET besitzer=13 WHERE id='$pid'");
            include("clear_lite.php");
            echo 'Planet wurde freigegeben';
        }
    }
    
    ?>
    <h3>Planetenauswahl</h3>
    <table class="liste"><tr><th>Display</th><th>Name</th><th>Sektor</th><th>System</th><th>Energie</th><th>Schilde</th><th>Lagerkapazit&auml;t</th></tr>
        <?php
        $id = $_SESSION["Id"];
        $abfrage = mysqli_query($verbindung, "SELECT * FROM `planeten` WHERE besitzer='$id'");
        while ($planet = mysqli_fetch_array($abfrage)) {
            
            $planet = new Planeten($planet["id"]);
            echo '<tr><td><a href="planet.php?pid=', $planet->id, '"><img src="images/misc/', $planet->bild, '" border="0"></a></td><td>', $planet->name, '(', $planet->id, ')</td><td>', $planet->position->x, '/', $planet->position->y, '</td><td>', $planet->position->system->name, '-System (', $planet->position->system->id, ') ', $planet->position->system->x, '/', $planet->position->system->y, ' </td><td>', $planet->energie, '/', $planet->maxenergie, '</td><td>', $planet->schilde, '/', $planet->maxschilde, '</td><td>', floor(($planet->frachtraum->gesamt() / $planet->frachtraum->max) * 100), ' %</td></tr>';
            //die("test");
        }
        echo '</table>';
    }
    include("foot.php");
    ?>
