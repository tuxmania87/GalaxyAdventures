<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

$sid = $_GET["sid"];

$userId = requireLogin();
$sid = requireIntParam('sid');
{
    $schiff = new Schiffe($sid);
    if ($schiff->besitzer->id !== $userId) exit('Fehler: Nicht dein Schiff.');
{

        if ($_POST["sent"] == 1) {
            $rate = $_POST["wamount"];

            switch ($_POST["coretyp"]) {
                case "classic":
                    $error = "";
                    if ($rate + $schiff->warpkern > $schiff->maxwarpkern)
                        $rate = $schiff->maxwarpkern - $schiff->warpkern;
                    
                    $newrate = floor($rate/50);
                    if($newrate < $rate)
                        echo "Warpkernladung wid auf $newrate korrigiert.<br />";
                    $rate = $newrate;
                    
                    if ($schiff->frachtraum->fracht[8]->anzahl < $rate * 2) {
                        $error = "Zu wenig Deuterium an Board. Vorhanden: ".$schiff->frachtraum->fracht[8]->anzahl.". Verlangt: ".($rate*2);
                    }
                    if ($schiff->frachtraum->fracht[7]->anzahl < $rate * 2)
                        $error = "Zu wenig Antimaterie an Board. Vorhanden: ".$schiff->frachtraum->fracht[7]->anzahl.". Verlangt: ".($rate*2);
                    
                    if ($schiff->frachtraum->fracht[6]->anzahl < $rate)
                        $error = "Zu wenig Dilitium an Board. Vorhanden: ".$schiff->frachtraum->fracht[6]->anzahl.". Verlangt: ".$rate;
                    
                    if ($error == "") {
                        $schiff->frachtraum->fracht[8]->anzahl-=($rate * 2);
                        $schiff->frachtraum->fracht[7]->anzahl-=($rate * 2);
                        $schiff->frachtraum->fracht[6]->anzahl-=$rate;
                        $schiff->warpkern+=(50 * $rate);
                        echo 'Dein Warpkern wurde aufgeladen!';
                        $schiff->frachtraum->save();
                        mysqli_query($verbindung, "UPDATE schiffe SET warpkern='" . $schiff->warpkern . "' WHERE id='" . $schiff->id . "'") or die(mysqli_error($verbindung));
                    } else
                        echo $error, '<br />';
                    break;
              case "deut":
                  if($rate + $schiff->warpkern > $schiff->maxwarpkern) {
                      $newrate = $schiff->maxwarpkern- $schiff->warpkern;
                      echo "Warpkernladung korrigiert auf $newrate<br />";
                      $rate = $newrate;
                  }
                  if($schiff->frachtraum->fracht[8]->anzahl >= $rate*2 ) {
                      $schiff->frachtraum->fracht[8]->anzahl-= $rate*2;
                      $schiff->warpkern+=$rate;
                      $schiff->frachtraum->save();
                      echo "Warpkern wurde erfolgreich um $rate aufgeladen!<br />";
                      mysqli_query($verbindung, "UPDATE schiffe SET warpkern='" . $schiff->warpkern . "' WHERE id='" . $schiff->id . "'") or die(mysqli_error($verbindung));
                  } else {
                      echo "Nicht genug Deuterium vorhanden. Benötigt: ".($rate*2)."<br />";
                  }
                  break;
              case "plasma":
                  if($rate + $schiff->warpkern > $schiff->maxwarpkern) {
                      $newrate = $schiff->maxwarpkern- $schiff->warpkern;
                      echo "Warpkernladung korrigiert auf $newrate<br />";
                      $rate = $newrate;
                  }
                  
                  $rate = floor($rate/5);
                  
                  if($schiff->frachtraum->fracht[9]->anzahl >= $rate ) {
                      $schiff->frachtraum->fracht[9]->anzahl-= $rate;
                      $schiff->warpkern+=$rate*5;
                      $schiff->frachtraum->save();
                      echo "Warpkern wurde erfolgreich um ".($rate*5)." aufgeladen!<br />";
                      mysqli_query($verbindung, "UPDATE schiffe SET warpkern='" . $schiff->warpkern . "' WHERE id='" . $schiff->id . "'") or die(mysqli_error($verbindung));
                  } else {
                      echo "Nicht genug Plasma vorhanden. Benötigt: ".($rate)."<br />";
                  }
                  break;
            }
        }


        echo '<h3>Warpkern-Aufladung</h3><br />';
        echo 'Um eine Ladung (50 Einheiten des Warpkerns) aufzuladen, brauchst du 1 Dilithium, 2 Antimaterie und 2 Deuterium. Pro Tick werden dann 2 Energie mehr erzeugt und die Ladung des Warpkerns verbaucht anstatt des Deuteriums.<br /><br /><h4>Waren auf deinem Schiff</h4>';
        echo '<img src="images/misc/dili.png" border="0" />Dilithium: ', $schiff->frachtraum->fracht[6]->anzahl;
        echo '<br /><img src="images/misc/antimaterie.png" border="0" />Antimaterie: ', $schiff->frachtraum->fracht[7]->anzahl;
        echo '<br /><img src="images/misc/deuterium.png" border="0" />Deuterium: ', $schiff->frachtraum->fracht[8]->anzahl;

        $decopy = $schiff->frachtraum->fracht[8]->anzahl;
        $dicopy = $schiff->frachtraum->fracht[6]->anzahl;
        $amcopy = $schiff->frachtraum->fracht[7]->anzahl;
        $warpcore = 0;

        while ($decopy-2 >= 0 && $dicopy-1 >= 0 && $amcopy-2 >= 0) {
            $decopy-=2;
            $dicopy--;
            $amcopy-=2;
            $warpcore+=50;
        }

        //$warpcore-=50;
        $sum = $warpcore > 0 ? $warpcore : 0;

        echo '<br />Das enspricht <span style="font-weight:bold;">' . $sum . ' </span>Warpkernladungen.<br />';
        echo '<form action="warpload.php?sid=', $sid, '" method="post"><input type="hidden" name="sent" value="1" />Den Warpkern um <input type="text" name="wamount" size="3" /> aufladen!<br />';
        $bu = new Button("", "aufladen");
        $bu->printme();
        echo '<input type="hidden" name="coretyp" value="classic" /></form>';


        echo '<br /><img src="images/misc/deuterium.png" border="0" />Deuterium: ', $schiff->frachtraum->fracht[8]->anzahl;
        echo '<br />Das enspricht <span style="font-weight:bold;">' . floor($schiff->frachtraum->fracht[8]->anzahl / 2) . ' </span>Warpkernladungen.<br />';

        echo '<form action="warpload.php?sid=', $sid, '" method="post"><input type="hidden" name="sent" value="1" />Den Warpkern um <input type="text" name="wamount" size="3" /> aufladen!<br />';
        $bu = new Button("", "aufladen");
        $bu->printme();
        echo '<input type="hidden" name="coretyp" value="deut" /></form>';

        echo '<br /><img src="images/misc/plasma.png" border="0" />Plasma: ', $schiff->frachtraum->fracht[9]->anzahl;
        echo '<br />Das enspricht <span style="font-weight:bold;">' . (5 * $schiff->frachtraum->fracht[9]->anzahl) . ' </span>Warpkernladungen.<br />';

        echo '<form action="warpload.php?sid=', $sid, '" method="post"><input type="hidden" name="sent" value="1" />Den Warpkern um <input type="text" name="wamount" size="3" /> aufladen!<br />';
        $bu = new Button("", "aufladen");
        $bu->printme();
        echo '<input type="hidden" name="coretyp" value="plasma" /></form>';

        echo '<br /><img src="images/misc/warpkern.png" border="0" /><span style="font-weight:bold;">Warpkern</span>: ', $schiff->warpkern, '/', $schiff->maxwarpkern;
        echo '<br /><br />';
        $bu = new Button("schiffe.php?sid=".$sid, "zurück zum Schiff");
        $bu->printme();
    }
}
include("foot.php");
