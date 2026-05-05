<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';
$verbindung = get_verbindung();

function bool2string($a)
{
    return $a ? 'ja' : 'nein';
}
if ($_SESSION['Id'] > 900) exit('Kein Zugriff.');
{
    if (isset($_POST['orderx']) && !ctype_digit($_POST['orderx'])) {
        exit('Betrugsversuch');
    }
    if (isset($_POST['ordery']) && !ctype_digit($_POST['ordery'])) {
        exit('Betrugsversuch');
    }
    if (isset($_POST['system']) && !ctype_digit($_POST['system'])) {
        exit('Betrugsversuch');
    }

    if (isset($_POST['panzahl'])) {
        $ox = $_POST['orderx'];
        $oy = $_POST['ordery'];
        $osys = $_POST['system'];
        $an = $_POST['panzahl'];
        $oid = $_POST['porderid'];

        $k = new Bauplan_Schiffe($oid);

        for ($i = 0; $i < $an; ++$i) {
            mysqli_query($verbindung, "insert into schiffe (besitzer,x,y,system,klasse,typ,hull,warpkern,name) values ('".intval(\$_SESSION['Id'])."','".$ox."','".$oy."','".$osys."','".$k->klasse."','s','".$k->maxhull."','".$k->maxwarpkern."','noname')") or exit($verbindung->error);
        }
    }

    echo '<form action="order.php" method="post">
        <table class="liste" style="text-align:center;">
        <tr><th>Anzahl</th><td><input type="text" name="panzahl" size="2" /></td></tr>
        <tr><th>X</th><td><input type="text" name="orderx" size="2" /></td></tr>
        <tr><th>Y</th><td><input type="text" name="ordery" size="2" /></td></tr>
        <tr><th>System</th><td><input type="text" name="system" size="2" value="0" /></td></tr></table><input type="submit" value="beantragen" /><br /><br />';

    $l = Bauplan_Schiffe::getList();
    echo '<table class="invitetable" style="text-align:center;">';
    echo '<tr><th>ID</th><th>Name</th><th>Bild</th><th>Hülle</th><th>Schilde</th><th>Phaser</th><th>Gondeln</th><th>Lager</th><th>EPS</th><th>Reaktor</th><th>Warpkern</th><th>Flugkosten</th><th>LRS</th><th>baubar von Spielern</th></tr>';
    for ($i = 0; $i < count($l); ++$i) {
        echo '<tr><td><input type="radio" name="porderid" value="'.$l[$i]->id.'" /></td><td>'.$l[$i]->klasse.'</td><td><img src="'.$l[$i]->bild.'" border="0" /></td><td>'.$l[$i]->maxhull.'</td><td>'.$l[$i]->maxschilde.'</td><td>'.$l[$i]->laser.' ('.$l[$i]->maxphaser.')</td><td>'.$l[$i]->maxgondeln.'</td><td>'.$l[$i]->lager.'</td><td>'.$l[$i]->maxenergie.'</td><td>'.$l[$i]->energieoutput.'</td>';
        echo '<td>'.$l[$i]->maxwarpkern.'</td><td>'.$l[$i]->flugkosten.'</td><td>'.$l[$i]->lrs.'</td><td>'.bool2string($l[$i]->siedler).'</td></tr>';
    }
    echo '</table></form>';
}
include 'foot.php';
