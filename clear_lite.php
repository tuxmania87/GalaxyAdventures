<?php

include_once 'connect.php';

$verbindung = get_verbindung();

$user = 13;
$non = [];
$lastid = checkforlastid('account');
$exists = false;
$var1 = mysqli_query($verbindung, "SELECT * FROM account WHERE id='$user'");
while ($var11 = mysqli_fetch_array($var1)) {
    $exists = true;
}
if (!$exists) {
    mysqli_query($verbindung, "DELETE FROM schiffe WHERE typ='s' AND besitzer='$user'");
    mysqli_query($verbindung, "UPDATE planeten SET name='noname',energie=20,maxenergie=20,baustoff=0,duranium=0,torpedo='0-0',erz=0,sorium=0,deuterium=0,tritanium=0,isochips=0,antimaterie=0,dili=0,schilde=0,maxschilde=0,laser=0,lager=500 WHERE besitzer='$user'") or exit($verbindung->error);
}
$abfragevar = mysqli_query($verbindung, "SELECT * FROM planeten WHERE besitzer='$user'");
while ($abfrage = mysqli_fetch_array($abfragevar)) {
    echo $abfrage['x'],'/',$abfrage['y'],'<br />';
    $pid = $abfrage['id'];
    $pl = new Planeten($pid);

    if (strpos($pl->typ, 'om') === false && strpos($pl->typ, 'mm') === false && strpos($pl->typ, 'lm') === false) {
        for ($o = 1; $o <= 50; ++$o) {
            $pl->feld[$o]->was = 0;
            $pl->feld[$o]->bauzeit = 0;
            $pl->feld[$o]->aktiv = 1;
            $pl->feld[$o]->hull = 60;
            if ($o == 15 || $o == 17 || $o == 24 || $o == 25) {
                $pl->feld[$i]->untergrund = 'm';
            }
            if ($o == 12 || $o == 14 || $o == 16 || $o == 19 || $o == 20 || $o == 22 || $o == 35 || $o == 38 || $o == 44) {
                $pl->feld[$i]->untergrund = 'f';
            }
            $pl->feld[$o]->save();
        }
    }
    // MOND O !!
    if ($pl->typ == 'om') {
        mysqli_query($verbindung, "UPDATE planet2 F,planeten P SET F.feld1 = '0-0-w-60-1',F.feld2 = '0-0-w-60-1',F.feld3 = '0-0-g-60-1',F.feld4 = '0-0-f-60-1',F.feld5 = '0-0-w-60-1',F.feld6 = '0-0-w-60-1',F.feld7 = '0-0-g-60-1',F.feld8 = '0-0-f-60-1',F.feld9 = '0-0-m-60-1',F.feld10 = '0-0-g-60-1',F.feld11 = '0-0-g-60-1',F.feld12 = '0-0-f-60-1',F.feld13 = '0-0-w-60-1',F.feld14 = '0-0-f-60-1',F.feld15 = '0-0-w-60-1',F.feld16 = '0-0-f-60-1',F.feld17 = '0-0-g-60-1',F.feld18 = '0-0-w-60-1',F.feld19 = '0-0-w-60-1',F.feld20 = '0-0-w-60-1',F.feld21 = '0-0-g-60-1',F.feld22 = '0-0-f-60-1',F.feld23 = '0-0-w-60-1',F.feld24 = '0-0-w-60-1' WHERE F.id=P.id AND P.id='".$pl->id."' AND P.typ='om'") or exit($verbindung->error);
    }
    if ($pl->typ == 'lm') {
        mysqli_query($verbindung, "UPDATE planet2 F,planeten P SET F.feld1 = '0-0-i-60-1',F.feld2 = '0-0-i-60-1',F.feld3 = '0-0-f-60-1',F.feld4 = '0-0-f-60-1',F.feld5 = '0-0-i-60-1',F.feld6 = '0-0-i-60-1',F.feld7 = '0-0-g-60-1',F.feld8 = '0-0-f-60-1',F.feld9 = '0-0-m-60-1',F.feld10 = '0-0-g-60-1',F.feld11 = '0-0-g-60-1',F.feld12 = '0-0-f-60-1',F.feld13 = '0-0-f-60-1',F.feld14 = '0-0-m-60-1',F.feld15 = '0-0-w-60-1',F.feld16 = '0-0-m-60-1',F.feld17 = '0-0-f-60-1',F.feld18 = '0-0-i-60-1',F.feld19 = '0-0-i-60-1',F.feld20 = '0-0-f-60-1',F.feld21 = '0-0-g-60-1',F.feld22 = '0-0-f-60-1',F.feld23 = '0-0-i-60-1',F.feld24 = '0-0-i-60-1' WHERE F.id=P.id AND P.id='".$pl->id."' AND P.typ='lm'") or exit($verbindung->error);
    }
    echo 'TYP: ',$pl->typ,'<br />';
    if ($pl->typ == 'mm') {
        mysqli_query($verbindung, "UPDATE planet2 F,planeten P SET F.feld1 = '0-0-i-60-1',F.feld2 = '0-0-w-60-1',F.feld3 = '0-0-f-60-1',F.feld4 = '0-0-i-60-1',F.feld5 = '0-0-i-60-1',F.feld6 = '0-0-i-60-1',F.feld7 = '0-0-g-60-1',F.feld8 = '0-0-f-60-1',F.feld9 = '0-0-m-60-1',F.feld10 = '0-0-g-60-1',F.feld11 = '0-0-g-60-1',F.feld12 = '0-0-f-60-1',F.feld13 = '0-0-g-60-1',F.feld14 = '0-0-m-60-1',F.feld15 = '0-0-w-60-1',F.feld16 = '0-0-g-60-1',F.feld17 = '0-0-f-60-1',F.feld18 = '0-0-w-60-1',F.feld19 = '0-0-i-60-1',F.feld20 = '0-0-i-60-1',F.feld21 = '0-0-g-60-1',F.feld22 = '0-0-f-60-1',F.feld23 = '0-0-i-60-1',F.feld24 = '0-0-i-60-1' WHERE F.id=P.id AND P.id='".$pl->id."' AND P.typ='mm'") or exit($verbindung->error);
    }
    mysqli_query($verbindung, "UPDATE planeten SET besitzer=2 WHERE id='$pid'");
}
