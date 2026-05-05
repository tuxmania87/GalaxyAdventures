<?php

include("head.php");
include("navlogged.php");
include("klassen.php");



$pid = $_GET["pid"];
$fid = $_GET["fid"];

if (!ctype_digit($pid))
    die("Fehler: ID not valid");
if (!ctype_digit($fid))
    die("Fehler: ID not valid");
$planet = new Planeten($pid);

if ($planet->besitzer->id != $_SESSION["Id"])
    die("Fehler:");

//aktiveren
$feld = new Gebaude($pid, $fid);

if(isset($_GET["aktivieren"]) && ctype_digit($_GET["aktivieren"]) && ($_GET["aktivieren"]==1 || $_GET["aktivieren"] == 0) ) {
    $feld->aktiv = $_GET["aktivieren"];
    $feld->save();
}

if(isset($_GET["destroy"])) {
    $ausgabe ="";
    for($i=0;$i<count($planet->frachtraum->fracht);$i++) {
        
        if($planet->frachtraum->gesamt() + floor($feld->bau->baukosten->fracht[$i+1]->anzahl/2) <= $planet->frachtraum->max) {
            $planet->frachtraum->fracht[$i]->anzahl += floor($feld->bau->baukosten->fracht[$i+1]->anzahl/2);
            if(floor($feld->bau->baukosten->fracht[$i+1]->anzahl/2 > 0))
                $ausgabe .= "<span class=\"success\">Du hast ".floor($feld->bau->baukosten->fracht[$i+1]->anzahl/2)." ".$planet->frachtraum->fracht[$i]->name." zurück erhalten.</span><br />";
        } else {
            $t_val = $planet->frachtraum->max -  $planet->frachtraum->gesamt();
            if($t_val > 0) {
                $planet->frachtraum->fracht[$i]->anzahl += $t_val;
                $ausgabe .= "<span class=\"success\">Du hast ".$t_val." ".$planet->frachtraum->fracht[$i]->name." zurück erhalten.</span><br />";
            }
        }
        
    }
    
    //release once effect stuff
    if($feld->rest_bauzeit == 0) {
        mysqli_query($verbindung, "update planeten set
            maxenergie=maxenergie-".$feld->bau->epslager.",
            lager=lager-".$feld->bau->lager.",
            laser=laser-".$feld->bau->laser.",
            maxschilde=maxschilde-".$feld->bau->schilde." where id = ".$pid) or die(mysqli_error($verbindung));
    }
    
    $planet->frachtraum->save();
    $ausgabe .= "<span class=\"success\">".$feld->bau->name." wurde erfolgreich abgerissen.</span><br />";
    $feld->bau=0;
    $feld->rest_bauzeit=0;
    $feld->aktiv=0;
    $feld->save();
    echo $ausgabe."<br />";
    echo '<meta http-equiv="refresh" content="2; URL=planet.php?pid='.$pid.'">';
    die();
}

echo '<h3>Gebäudeübersicht</h3>';

echo '<table class="liste">';

echo '<tr><th>Name</th><td>' . $feld->name . '</td></tr>';
echo '<tr><th>Bild</th><td><img src="images/buildings/' . $feld->bild . '" border="0" /></td></tr>';
echo '<tr><th>Untergrund</th><td><img src="images/buildings/' . $feld->untergrund->bild . '" border="0" /></td></tr>';
echo '<tr><th>Kosten je Tick</th><td>';
echo '<table>';
for ($i = 0; $i < count($feld->bau->braucht->fracht); $i++) {
    if ($feld->bau->braucht->fracht[$i]->anzahl > 0) {
        echo '<tr><td>' . $feld->bau->braucht->fracht[$i]->name . '</td><td><img src="images/misc/' .
        $feld->bau->braucht->fracht[$i]->bild . '" border="0" /></td><td>' . $feld->bau->braucht->fracht[$i]->anzahl . '</td></tr>';
    }
}
echo '</table>';
echo '</td></tr>';
echo '<tr><th>Produktion je Tick</th><td>';
echo '<table>';
for ($i = 0; $i < count($feld->bau->produziert->fracht); $i++) {
    if ($feld->bau->produziert->fracht[$i]->anzahl > 0) {
        echo '<tr><td>' . $feld->bau->produziert->fracht[$i]->name . '</td><td><img src="images/misc/' .
        $feld->bau->produziert->fracht[$i]->bild . '" border="0" /></td><td>' . $feld->bau->produziert->fracht[$i]->anzahl . '</td></tr>';
    }
}
echo '</table>';
echo '</td></tr>';

echo '<tr><th>Effekt</th><td>';
if($feld->bau->lager > 0)
    echo '+'.$feld->bau->lager.' Lager<br />';
if($feld->bau->epslager > 0)
    echo '+'.$feld->bau->epslager.' EPS-Lager<br />';
if($feld->bau->schilde > 0)
    echo '+'.$feld->bau->schilde.' Schilde<br />';
if($feld->bau->laser > 0)
    echo '+'.$feld->bau->laser.' Phaser<br />';
if($feld->bau->sonstiges != "")
    echo $feld->bau->sonstiges;
    
echo '</td></tr>';

if($feld->bau->forschung && $feld->rest_bauzeit == 0) {
    echo '<tr><th>Forschung</th><td>';
    $bu = new Button("forschung.php?pid=".$pid."&fid=".$fid,"Forschungsliste");
    $bu->printme();
    echo '</td></tr>';
}

if($feld->bau->werft && $feld->rest_bauzeit == 0) {
    echo '<tr><th>Schiffsbau</th><td>';
    $bu = new Button("createship.php?pid=".$pid."&fid=".$fid,"Bauliste");
    $bu->printme();
    echo '</td></tr>';
}


if ($feld->rest_bauzeit > 0) {
    echo '<tr><th>Status</th><td>fertig in ' . $feld->rest_bauzeit . ' Ticks</td></tr>';
} else {
    echo '<tr><th>Status</th><td>' . ($feld->aktiv == 1 ? '<span style="color:green;">aktiviert</span>' : '<span style="color:red;">deaktiviert</span>') . '</td></tr>';
}
echo '</table><br />';

if ($feld->rest_bauzeit == 0) {

    $bu = new Button("destroy.php?pid=" . $pid . "&fid=" . $fid . "&aktivieren=".(1-$feld->aktiv), ($feld->aktiv == 0 ? "<span style=\"color:green;\">Gebäude aktivieren</span>" : "<span style=\"color:red;\">Gebäude deaktivieren</span>"));
    $bu->printme();
}

echo '<form action="destroy.php?pid=',$pid,'&fid=',$fid,'&destroy=1" method="post" onSubmit="return frage(1)">';
$bu = new Button("",$feld->name." abreißen");
$bu->printme();

echo '</form>';


echo '<br /><br />';
$bu = new Button("planet.php?pid=" . $pid, "zurück zum Planeten");
$bu->printme();



include("foot.php");
?>
