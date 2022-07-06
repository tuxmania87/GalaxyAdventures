<?php

include_once("connect.php");
$non = array();
$lastid = checkforlastid('account');
for ($i = 1; $i <= $lastid; $i++) {
    $exists = false;
    $var1 = mysql_query("SELECT * FROM account WHERE id='$i'");
    while ($var11 = mysql_fetch_array($var1)) {
        $exists = true;
    }
    if (!$exists) {
        $non[] = $i;
        mysql_query("DELETE FROM schiffe WHERE typ='s' AND besitzer='$i'");
        mysql_query("UPDATE schiffe SET torpedo='0-0',name='noname',energie=20,maxenergie=20,rohstoffa=0,rohstoffb=0,rohstoffc=0,rohstoffd=0,deuterium=0,tritanium=0,isochips=0,antimaterie=0,dili=0,schilde=0,maxschilde=0,laser=0,lager=500 WHERE besitzer='$i' AND typ='m'") or die(mysql_error());
    }
}
for ($i = 0; $i < sizeof($non); $i++) {
    $ooo = $non[$i];
    $abfragevar = mysql_query("SELECT * FROM schiffe WHERE besitzer='$ooo' AND typ='m'");
    while ($abfrage = mysql_fetch_array($abfragevar)) {
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
            mysql_query("UPDATE schiffe SET besitzer=2 WHERE typ='m' AND id='$pid'");
        }
    }
}
