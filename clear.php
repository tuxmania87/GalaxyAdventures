<?php

include_once("connect.php");
$non = array();
$lastid = checkforlastid('account');
for ($i = 1; $i <= $lastid; $i++) {
    $exists = false;
    $var1 = mysqli_query($verbindung, "SELECT * FROM account WHERE id='$i'");
    while ($var11 = mysqli_fetch_array($var1)) {
        $exists = true;
    }
    if (!$exists) {
        $non[] = $i;
        mysqli_query($verbindung, "DELETE FROM schiffe WHERE typ='s' AND besitzer='$i'");
        mysqli_query($verbindung, "UPDATE schiffe SET torpedo='0-0',name='noname',energie=20,maxenergie=20,rohstoffa=0,rohstoffb=0,rohstoffc=0,rohstoffd=0,deuterium=0,tritanium=0,isochips=0,antimaterie=0,dili=0,schilde=0,maxschilde=0,laser=0,lager=500 WHERE besitzer='$i' AND typ='m'") or die(mysqli_error($verbindung));
    }
}
for ($i = 0; $i < count($non); $i++) {
    $ooo = $non[$i];
    $abfragevar = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE besitzer='$ooo' AND typ='m'");
    while ($abfrage = mysqli_fetch_array($abfragevar)) {
        echo $abfrage["x"], '/', $abfrage["y"], '<br />';
        $pid = $abfrage["id"];
        $pfeld = new planetfeld($pid);
        for ($o = 1; $o <= 50; $o++) {
            $ppp = 'feld' . $o;
            splitfeld($pfeld->$ppp, $a, $b, $c, $d, $e);
            $pfeld->$ppp = '0-0-' . $c . '-60-1';
            if ($o == 15 || $o == 17 || $o == 24 || $o == 25)
                $pfeld->$ppp = '0-0-m-60-1';
            if ($o == 12 || $o == 14 || $o == 16 || $o == 19 || $o == 20 || $o == 22 || $o == 35 || $o == 38 || $o == 44)
                $pfeld->$ppp = '0-0-f-60-1';
            $pfeld->setData();
            mysqli_query($verbindung, "UPDATE schiffe SET besitzer=2 WHERE typ='m' AND id='$pid'");
        }
    }
}
