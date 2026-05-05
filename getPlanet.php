<?php

session_start();
include 'klassen.php';

$verindung = get_verbindung();

$id = $_SESSION['Id'];
$checked = false;
$pid = $_GET['pid'];
$planetx = new Planeten($pid);
$myAccount = new Account($_SESSION['Id']);
echo $id;
$abfrage = mysqli_query($verindung, "SELECT * FROM `planeten` WHERE besitzer='$id' AND typ='m'");
while ($planet = mysqli_fetch_array($abfrage)) {
    $checked = true;
}
if (!$checked) {
    mysqli_query($verindung, "UPDATE `planeten` SET heimat=1,besitzer='$id' WHERE id='$pid'");
    mysqli_query($verindung, "UPDATE planeten SET frachtraum='150',besitzer='$id',name='noname' WHERE id='$pid'") or exit($verindung->error);
    $sond = new Bauplan_Schiffe('Sonde');
    mysqli_query($verindung, "INSERT INTO schiffe 
        (klasse,warpkern,typ,x,y,`system`,besitzer,energie,hull,alarmstufe,`name`,`nachricht`) 
        VALUES (
            'Sonde',
            '".$sond->maxwarpkern."',
            's',
            '".$planetx->position->x."',
            '".$planetx->position->y."',
            '".$planetx->position->system->id."',
            '$id',
            '".$sond->maxenergie."',
            '".$sond->maxhull."',
            'green'
            ,'Sonde von ".$myAccount->nickname."'
            ,''
            )");

    $wiesen = [];

    for ($i = 0; $i < count($planetx->feld); ++$i) {
        if ($planetx->feld[$i]->untergrund->id == 2) {
            $wiesen[] = $i;
        }
    }

    $index = rand(0, count($wiesen));

    $planetx->feld[$index]->bau = new Bauplan_Gebaude('18');
    $planetx->feld[$index]->aktiv = 1;
    $planetx->feld[$index]->rest_bauzeit = 0;
    $planetx->feld[$index]->save();

    echo '<META HTTP-EQUIV="refresh" content="0;URL=planet.php?pid=', $pid, '">';
}
