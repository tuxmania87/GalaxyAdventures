<?php

function fst($text) {
    $trenn = false;
    $eins = "";
    $zwei = "";
    for ($i = 0; $i < strlen($text); $i++)
        if ($text[$i] == '-')
            $trenn = true; else if (!$trenn)
            $eins.=$text[$i]; else
            $zwei.=$text[$i];
    return $eins;
}

function snd($text) {
    $trenn = false;
    $eins = "";
    $zwei = "";
    for ($i = 0; $i < strlen($text); $i++)
        if ($text[$i] == '-')
            $trenn = true; else if (!$trenn)
            $eins.=$text[$i]; else
            $zwei.=$text[$i];
    return $zwei;
}

include("head.php");
include("navlogged.php");
include("klassen.php");
$id = $_SESSION["Id"];




if(isset($_POST["kid"]) && ctype_digit($_POST["kid"])) {
    $a = new Angebote($_POST["kid"]) ;
    if($a->seller->id == $id) {
        //Angebot zurücknehmen ( 10% Malus)
        $k = new Konto($a->seller->id);
        for($i=0; $i<count($k->frachtraum->fracht);$i++) {
            $k->frachtraum->fracht[$i]->anzahl += ceil($a->sell->fracht[$i+1]->anzahl*0.9);
        }
        $k->frachtraum->save();
        mysqli_query($verbindung, "delete from ebay where id =".$a->id);
    } else {
        ///angebot kaufen
        $k = new Konto($_SESSION["Id"]);
        for($i=0; $i<count($k->frachtraum->fracht);$i++) {
            $k->frachtraum->fracht[$i]->anzahl += $a->sell->fracht[$i+1]->anzahl;
        }
        $k->frachtraum->save();
        mysqli_query($verbindung, "delete from ebay where id =".$a->id);
    }
    
} 

$me = new Konto($id);

echo '<h3>Dein Warenkonto</h3>';

//hinweis
echo '<span style="color:yellow;">Du kannst Waren in dein Warenkonto einzahlen, indem du ein Ferengi-Schiff im Weltraum aufsuchst und deine Waren dort hinaufbeamst.<br />Du findest Ferengi Schiffe alle 10x10 Felder im Weltraum!</span><br /><br />';

echo '<table class="invitetable">';
for ($i = 0; $i < count($me->frachtraum->fracht); $i++) {
    if ($me->frachtraum->fracht[$i]->anzahl > 0)
        echo '<tr><td><img src="images/misc/' . $me->frachtraum->fracht[$i]->bild . '" border="0" /></td><td width="200px">' . $me->frachtraum->fracht[$i]->name . ': <td>' . $me->frachtraum->fracht[$i]->anzahl . '</td></tr>';
}
echo '</table><br />';

$bu = new Button("createebay.php", "Angebot einstellen");
$bu->printme();

echo '<br /><hr /><br /><h3>Warenb&ouml;rse</h3><table class="invitetable"><tr><th>Anbieter</th><th>bietet an</th><th>verlangt</th><th> </th></tr>';

$l = Angebote::getList();

for($i=0; $i<count($l);$i++) {
    echo '<tr><td>'.$l[$i]->seller->nickname.'</td><td>';
    for($j=0;$j<count($l[$i]->sell->fracht);$j++) {
        if($l[$i]->sell->fracht[$j]->anzahl > 0) 
            echo "<img src=\"images/misc/".$l[$i]->sell->fracht[$j]->bild."\" border=\"0\" /> ".$l[$i]->sell->fracht[$j]->anzahl." ".$l[$i]->sell->fracht[$j]->name."<br />";
    }
    echo '</td><td>';
    for($j=0;$j<count($l[$i]->buy->fracht);$j++) {
        if($l[$i]->buy->fracht[$j]->anzahl > 0) 
            echo "<img src=\"images/misc/".$l[$i]->buy->fracht[$j]->bild."\" border=\"0\" /> ".$l[$i]->buy->fracht[$j]->anzahl." ".$l[$i]->buy->fracht[$j]->name."<br />";
    }
    echo '</td>';
    if ($l[$i]->seller->id != $_SESSION["Id"])
        echo '<form action="konto.php" method="post"><input type="hidden" name="kid" value="', $l[$i]->id, '" /><td><input type="submit" value="kaufen"></td></form>'; else
        echo '<form action="konto.php" method="post"><input type="hidden" name="kid" value="', $l[$i]->id, '" /><td><input type="submit" value="zur&uuml;cknehmen"></td></form>';
}


echo '</table>';
include("foot.php");
?>
