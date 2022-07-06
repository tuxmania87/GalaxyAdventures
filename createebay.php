<?php

include("head.php");
include("navlogged.php");
include("klassen.php");
$id = $_SESSION["Id"];

$me = new Konto($id);

if($_POST["sent"] == 1) {
    //create dummy container
    $sell = new Frachtraum("","dummy");
    $buy = new Frachtraum("","dummy");
    
    for($i=1;$i<sizeof($sell->fracht);$i++) {
        $sell->fracht[$i]->anzahl = ( isset($_POST["psell".$i]) && ctype_digit($_POST["psell".$i]) ?$_POST["psell".$i]:'0');
    }
    
    for($i=1;$i<sizeof($buy->fracht);$i++) {
        $buy->fracht[$i]->anzahl = ( isset($_POST["pbuy".$i]) && ctype_digit($_POST["pbuy".$i]) ?$_POST["pbuy".$i]:'0');
    }
    
    //check if we have all
    $dontsave = false;
    for($i=0; $i<sizeof($me->frachtraum->fracht);$i++) {
        if($me->frachtraum->fracht[$i]->anzahl >= $sell->fracht[$i+1]->anzahl)
            $me->frachtraum->fracht[$i]->anzahl -= $sell->fracht[$i+1]->anzahl;
        else {
            $dontsave = true;
        }
    }
    if(!$dontsave) {
        //success
        $me->frachtraum->save();
        //export sell container
        $dbstring = "0/";
        for($i=1; $i<=sizeof($sell->fracht);$i++) {
            $dbstring .= $sell->fracht[$i]->anzahl;
            if($i != sizeof($sell->fracht))
                $dbstring .= "/";
        }
        
        $dbstring2 = "0/";
        for($i=1; $i<=sizeof($buy->fracht);$i++) {
            $dbstring2 .= $buy->fracht[$i]->anzahl;
            if($i != sizeof($buy->fracht))
                $dbstring2 .= "/";
        }
        mysql_query("insert into ebay (id,anbieter,sell,buy,datum) values (NULL,'".$_SESSION["Id"]."','".$dbstring."','".$dbstring2."',NULL)") or die(mysql_error());
        echo '<meta http-equiv="refresh" content="0; URL=konto.php">';
    }
}


echo '<h3>Dein Konto</h3>';
echo '<table class="invitetable">';
for ($i = 0; $i < sizeof($me->frachtraum->fracht); $i++) {
    if ($me->frachtraum->fracht[$i]->anzahl > 0)
        echo '<tr><td><img src="images/misc/' . $me->frachtraum->fracht[$i]->bild . '" border="0" /></td><td width="200px">' . $me->frachtraum->fracht[$i]->name . ': <td>' . $me->frachtraum->fracht[$i]->anzahl . '</td></tr>';
}
echo '</table><br />';

echo '<h3>Warenb&ouml;rse</h3>';
echo '<form action="createebay.php" method="post"> <input type="hidden" name="sent" value="1" /><br />';
echo '<table class="invitetable"><tr><th></th><th>Material</th><th>anbieten</th><th>verlangen</th></tr>';
for ($i = 0; $i < sizeof($me->frachtraum->fracht); $i++) {
    if($me->frachtraum->fracht[$i]->anzahl > 0)
        echo '<tr><td><img src="images/misc/'.$me->frachtraum->fracht[$i]->bild.'" border="0" /></td><td>',$me->frachtraum->fracht[$i]->name, '</td><td><input type="text" size="4" name="psell'.($i+1).'" value="0" /></td><td><input type="text" size="4" name="pbuy'.($i+1).'" value="0" /></td></tr>';
    
}

echo '</table>';
echo '<input type="hidden" name="eintragen" value="1">';
echo '<input type="submit" value="eintragen"></form>';
echo '<br /><a href="konto.php">zur&uuml;ck</a>';

include("foot.php");
?>
