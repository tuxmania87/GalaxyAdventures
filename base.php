<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';

$verbindung = get_verbindung();
$self = $_SESSION['Id'];
$job = $_GET['job'];

$sid = $_POST['sid'];
if (!isset($sid)) {
    $sid = $_GET['sid'];
}
$schiff = new Schiffe($sid);
$forschung = new Forschungen($_SESSION['Id']);

$feld = new Feld($schiff->position->x, $schiff->position->y, $schiff->position->system->id);
if ($feld->was == 'System') {
    exit('Bauen auf Systemfeldern nicht erlaubt!');
}

// nebel test
$nebel = false;
$nebelvar = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE (typ='b' OR typ='g') AND x='".$schiff->position->x."' AND y='".$schiff->position->y."'");
while ($testb = mysqli_fetch_array($nebelvar)) {
    $nebel = true;
}
$feindpl = false;
$nebelvar = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='m' AND x='".$schiff->position->x."' AND y='".$schiff->position->y."' AND orbit='".$schiff->position->orbit."' AND besitzer!='$self'");
while ($testb = mysqli_fetch_array($nebelvar)) {
    $feindpl = true;
}

if ($nebel) {
    echo 'Du kannst nicht im Nebel bauen!';
} elseif ($feindpl) {
    echo 'Du kannst nicht im Orbit von fremden Planeten bauen!';
} else {
    $accid = $_SESSION['Id'];


    

    if ($job == 1) { 	// Schiff 3 bauen
        if ($schiff->frachtraum->baustoff >= 600 && $schiff->frachtraum->duranium >= 250 && $schiff->frachtraum->sorium >= 10) {
            $schiff->frachtraum->baustoff -= 600;
            $schiff->frachtraum->duranium -= 250;
            $schiff->frachtraum->sorium -= 10;
            $schiff->frachtraum->save();

            $lastid = checkforlastid('schiffe') + 1;
            mysqli_query($verbindung, "INSERT INTO schiffe (maxdock,typ,klasse,skillbase,id,baustoff,duranium,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,system,energie,maxenergie,energieoutput,bild,lager,orbit,maxphaser) VALUES ('20','s','Raumstation','1','$lastid','0','0','100','200','200','140','140','12','noname','$accid','green','".$schiff->position->x."','".$schiff->position->y."','".$schiff->position->system->id."','150','150','20','images/siedlerbase.png','300','".$schiff->position->orbit."','80')") or exit($verbindung->error);
            echo 'Schiff gebaut!';
            $gebaut = true;
        } else {
            echo 'Nicht genug Rohstoffe!';
        }
    }

    if ($job == 10 && $forschung->horchposten == 1) { 	// Schiff 3 bauen
        if ($schiff->frachtraum->baustoff >= 200 && $schiff->frachtraum->duranium >= 150 && $schiff->frachtraum->deuterium >= 200) {
            $schiff->frachtraum->baustoff -= 200;
            $schiff->frachtraum->duranium -= 150;
            $schiff->frachtraum->deuterium -= 200;
            $schiff->frachtraum->save();

            $lastid = checkforlastid('schiffe') + 1;
            mysqli_query($verbindung, "INSERT INTO schiffe (klasse,skillbase,id,baustoff,duranium,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,system,energie,maxenergie,energieoutput,bild,lager,orbit) VALUES ('Horchposten','1','$lastid','1','$fid','100','40','40','30','30','0','noname','$accid','green','".$schiff->position->x."','".$schiff->position->y."','".$schiff->position->system->id."','10','10','5','images/horchposten.png','150','".$schiff->position->orbit."')") or exit($verbindung->error);
            echo 'Schiff gebaut!';
            $gebaut = true;
        } else {
            echo 'Nicht genug Rohstoffe!';
        }
    }

    if ($job == 2) { 	// Schiff 3 bauen
        mysqli_query($verbindung, "INSERT INTO schiffe (maxdock,maxphaser,typ,klasse,skillbase,baustoff,duranium,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,system,energie,maxenergie,energieoutput,bild,lager,orbit) VALUES ('20','50','s','Raumstation','1','0','0','100','400','400','500','500','22','noname','$accid','green','".$schiff->position->x."','".$schiff->position->y."','".$schiff->position->system->id."','500','500','50','images/klingbase.png','15000','".$schiff->position->orbit."')") or exit($verbindung->error);
        echo 'Schiff gebaut!';
        $gebaut = true;
    }

    if ($job == 3) { 	// Schiff 3 bauen
        mysqli_query($verbindung, "INSERT INTO schiffe (maxdock,maxphaser,typ,klasse,skillbase,baustoff,duranium,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,system,energie,maxenergie,energieoutput,bild,lager,orbit) VALUES ('20','50','s','Raumstation','1','0','0','100','400','400','500','500','22','noname','$accid','green','".$schiff->position->x."','".$schiff->position->y."','".$schiff->position->system->id."','500','500','50','images/fodbase.png','15000','".$schiff->position->orbit."')") or exit($verbindung->error);
        echo 'Schiff gebaut!';
        $gebaut = true;
    }

    if ($job == 4) { 	// Schiff 3 bauen
        mysqli_query($verbindung, "INSERT INTO schiffe (maxdock,maxphaser,typ,klasse,skillbase,baustoff,duranium,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,system,energie,maxenergie,energieoutput,bild,lager,orbit) VALUES ('20','50','s','Raumstation','1','0','0','100','400','400','500','500','22','noname','$accid','green','".$schiff->position->x."','".$schiff->position->y."','".$schiff->position->system->id."','500','500','50','images/rombase.png','15000','".$schiff->position->orbit."')") or exit($verbindung->error);
        echo 'Schiff gebaut!';
        $gebaut = true;
    }

    if ($job == 5) { 	// Schiff 3 bauen
        mysqli_query($verbindung, "INSERT INTO schiffe (maxdock,maxphaser,typ,klasse,skillbase,baustoff,duranium,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,system,energie,maxenergie,energieoutput,bild,lager,orbit) VALUES ('20','50','s','Raumstation','1','0','0','100','400','400','500','500','22','noname','$accid','green','".$schiff->position->x."','".$schiff->position->y."','".$schiff->position->system->id."','500','500','50','images/klingbase.png','15000','".$schiff->position->orbit."')") or exit($verbindung->error);
        echo 'Schiff gebaut!';
        $gebaut = true;
    }

    if ($job == 6) { 	// Schiff 3 bauen
        mysqli_query($verbindung, "INSERT INTO schiffe (maxdock,maxphaser,typ,klasse,skillbase,baustoff,duranium,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,system,energie,maxenergie,energieoutput,bild,lager,orbit) VALUES ('20','50','s','Raumstation','1','0','0','100','400','400','500','500','22','noname','$accid','green','".$schiff->position->x."','".$schiff->position->y."','".$schiff->position->system->id."','500','500','50','images/ferbase.png','15000','".$schiff->position->orbit."')") or exit($verbindung->error);
        echo 'Schiff gebaut!';
        $gebaut = true;
    }

    if ($job == 7) { 	// Schiff 3 bauen
        mysqli_query($verbindung, "INSERT INTO schiffe (maxdock,maxphaser,typ,klasse,skillbase,baustoff,duranium,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,system,energie,maxenergie,energieoutput,bild,lager,orbit) VALUES ('20','50','s','Raumstation','1','0','0','100','400','400','500','500','22','noname','$accid','green','".$schiff->position->x."','".$schiff->position->y."','".$schiff->position->system->id."','500','500','50','images/cardbase.png','15000','".$schiff->position->orbit."')") or exit($verbindung->error);
        echo 'Schiff gebaut!';
        $gebaut = true;
    }

    if ($job == 8) { 	// Schiff 3 bauen
        mysqli_query($verbindung, "INSERT INTO schiffe (maxdock,maxphaser,typ,klasse,skillbase,baustoff,duranium,deuterium,hull,maxhull,schilde,maxschilde,laser,name,besitzer,alarmstufe,x,y,system,energie,maxenergie,energieoutput,bild,lager,orbit) VALUES ('20','50','s','Raumstation','1','0','0','100','400','400','500','500','22','noname','$accid','green','".$schiff->position->x."','".$schiff->position->y."','".$schiff->position->system->id."','500','500','50','images/borgbase.gif','15000','".$schiff->position->orbit."')") or exit($verbindung->error);
        echo 'Schiff gebaut!';
        $gebaut = true;
    }

    echo '<h3>Raumstation bauen im Sektor ',$schiff->position->x,'|',$schiff->position->y,'  -   System: ',$schiff->position->system->name,' (',$schiff->position->system->x,'|',$schiff->position->system->y,')</h3>';
    echo $schiff->frachtraum->baustoff > 0 ? 'Baustoff: '.$schiff->frachtraum->baustoff.'<br />' : '';
    echo $schiff->frachtraum->duranium > 0 ? 'Duranium: '.$schiff->frachtraum->duranium.'<br />' : '';
    echo $schiff->frachtraum->erz > 0 ? 'Erz: '.$schiff->frachtraum->erz.'<br />' : '';
    echo $schiff->frachtraum->sorium > 0 ? 'Sorium: '.$schiff->frachtraum->sorium.'<br />' : '';
    echo $schiff->frachtraum->deuterium > 0 ? 'Deuterium: '.$schiff->frachtraum->deuterium.'<br />' : '';
    echo '<br /><table class="bordered"><tr><td>Schiffsname</td><td>Bild</td><td>Baukosten</td><td>Schilde</td><td>H&uuml;lle</td><td>Laserst&auml;rke</td><td>A-Module</td><td>B-Module</td><td>C-Module</td><td>Lagerraum</td><td>Bauzeit in Ticks</td><td>sonstiges</td><td></td></tr>';


    $query = "
        SELECT id from bauplan where skillbase = 1;
    ";

    $bases = [];
    $q = mysqli_query($verbindung, $query);
        while ($r = mysqli_fetch_array($q)) {
            $bases[] = new Bauplan_Schiffe($r['id']);
        }


    foreach($bases as $base) {
        echo '<td>'.$base->klasse.'</td>
            <td><img src="'.$base->bild.'" border="0"></td>
            <td>'.$base->kosten.'</td>
            <td>'.$base->maxschilde.'</td>
            <td>'.$base->maxhull.'</td>
            <td>'.$base->laser.'</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>'.$base->lager.'</td>
            <td>'.$base->bauzeit.'</td>
            <td>optimale Verteidigungsstation</td>
            <td><a href="base.php?sid=',$schiff->id,'&job='.$base->id.'">Basis bauen!</a></td></tr>';
    }

    if ($forschung->horchposten == 1) {
        echo '<td>Horchposten</td><td><img src="images/ships/horchposten.png" border="0"></td><td>200 Baustoff<br />150 Duranium<br />200 Deuterium</td><td>40</td><td>30</td><td>0</td><td>0</td><td>0</td><td>0</td><td>40</td><td>1</td><td>&Uuml;berwacht ein Gebiet von Radius 5 um die Station, also ein 10x10 Feld</td><td><a href="base.php?sid=',$schiff->id,'&job=10">Horchposten bauen!</a></td></tr>';
    }


    echo '</table>';
}
include 'foot.php';
