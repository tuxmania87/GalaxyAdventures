<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

$fromsplit = explode("-", $_GET["from"]);
$tosplit = explode("-", $_GET["to"]);

if (!ctype_digit($fromsplit[1]) || !ctype_digit($tosplit[1]))
    die("Fehler: ID ung&uuml;ltig");
if (!(($fromsplit[0] == 'P' || $fromsplit[0] == 'S') && ($tosplit[0] == 'P' || $tosplit[0] == 'S')))
    die("Manipulations-Fehler: 1");
if ($fromsplit[0] == 'P' && $tosplit[0] == 'P')
    die();


$fromid = $fromsplit[1];
$toid = $tosplit[1];

$modus = $_GET["modus"];

$to = $tosplit[0] == 'S' ? new Schiffe($toid) : new Planeten($toid);
$from = $fromsplit[0] == 'S' ? new Schiffe($fromid) : new Planeten($fromid);

if ($modus == 1 && $from->besitzer->level <= 3 && $to->besitzer->id != $from->besitzer->id)
    die("Unter Level 3 kannst du nur Warenauf deinen Schiffen und Planeten transferieren!");
if ($modus == 2 && $to->besitzer->level <= 3 && $from->besitzer->id != $to->besitzer->id)
    die("Unter Level 3 kannst du nur Warenauf deinen Schiffen und Planeten transferieren!");
if ($to->besitzer->level != $from->besitzer->level && $modus <= 2)
    die("Waren k&ouml;nnen nur zwischen Benutzern mit gleichem Level transferiert werden!");


if ($to->position->x != $from->position->x || $to->position->y != $from->position->y || $to->position->orbit != $from->position->orbit || $to->position->system->id != $from->position->system->id)
    die("Alocation Error");


if ($_GET["modus"] == 1 && $from->besitzer->id != $_SESSION["Id"])
    die("Fehler: Besitzer-ID ung&uuml;ltig");
if ($_GET["modus"] == 2 && $to->besitzer->id != $_SESSION["Id"])
    die("Fehler: Besitzer-ID ung&uuml;ltig");


if ($_POST["do"] == 1) {
    $uber = array();
    for($i=0;$i<count($from->frachtraum->fracht);$i++) {
        $uber[] = (isset($_POST["p".$i])? (int) $_POST["p".$i]: 0 );
    }
    $from->beamen(1, $uber, $to);
}

if ($_POST["do"] == 4) {
    $uber = array();
    for($i=0;$i<count($from->frachtraum->fracht);$i++) {
        $uber[] = (isset($_POST["p".$i])? (int) $_POST["p".$i]: 0 );
    }
    $from->beamen(4, $uber, $to);
}

if ($_POST["do"] == 2) {
    $uber = array();
    for($i=0;$i<count($from->frachtraum->fracht);$i++) {
        $uber[] = (isset($_POST["p".$i])? (int) $_POST["p".$i]: 0 );
    }
    $to->beamen(2, $uber, $from);
}

if ($_POST["do"] == 3) {
    $uber = array();
    for($i=0;$i<count($from->frachtraum->fracht);$i++) {
        $uber[] = (isset($_POST["p".$i])? (int) $_POST["p".$i]: 0 );
    }
    $to->beamen(3, $uber, $from);
}



if ($_GET["modus"] == 1) {
    echo '<h3>Transfer</h3>Energie: ', $from->energie, '<br />';
    echo '<form action="beam.php?modus=1&from=', $fromsplit[0] == 'S' ? 'S-' : 'P-', $from->id, '&to=', $tosplit[0] == 'S' ? 'S-' : 'P-', $to->id, '" method="post"><table class="invitetable" style="text-align:center;">';
    echo '<tr><th>Material</th><th>#</th><th></th><th>', $from->name, '</th><th></th><th>', $to->name, '</th></tr>';

    for ($i = 0; $i < count($from->frachtraum->fracht); $i++)
        if ($from->frachtraum->fracht[$i]->anzahl > 0) {
            echo '<tr><th>', $from->frachtraum->fracht[$i]->name, '</th><td>'.$from->frachtraum->fracht[$i]->anzahl;
            echo $from->frachtraum->fracht[$i]->max >=0?'/'.$from->frachtraum->fracht[$i]->max :'';
            echo '</td><td><img src="images/misc/', $from->frachtraum->fracht[$i]->bild, '" border="0" /></td><td><input type="text" size="6" name="p', $i, '"></td><td>--></td><td>'. $to->frachtraum->fracht[$i]->anzahl. ($to->frachtraum->fracht[$i]->max >=0?'/'.$to->frachtraum->fracht[$i]->max:'') . '</td></tr>';
         }

    echo '<tr><th>Lagerraum</th><td></td><td></td><td>', $from->frachtraum->gesamt(), '/', $from->frachtraum->max, '</td><td></td><td>', $to->frachtraum->gesamt(), '/', $to->frachtraum->max, '</td></td></tr>';
    echo '</table>';
    echo '<input type="hidden" name="do" value="1">'; $bu = new Button("","transferieren"); $bu->printme(); echo '</form>';
}

if ($_GET["modus"] == 4) {
    $konto = new Konto($_SESSION["Id"]);
    echo '<h3>Transfer</h3>Energie: ', $from->energie, '<br />';
    echo '<form action="beam.php?modus=4&from=', $fromsplit[0] == 'S' ? 'S-' : 'P-', $from->id, '&to=', $tosplit[0] == 'S' ? 'S-' : 'P-', $to->id, '" method="post"><table class="bordered">';
    echo '<tr><td></td><td></td><td>', $from->name, '</td><td></td><td>Warenkonto</td></tr>';

    for ($i = 0; $i < count($from->frachtraum->fracht); $i++)
        if ($from->frachtraum->fracht[$i]->anzahl > 0)
            echo '<tr><td>', $from->frachtraum->fracht[$i]->name, '</td><td><img src="images/misc/', $from->frachtraum->fracht[$i]->bild, '" border="0" /></td><td><input type="text" size="6" name="p'.$i.'">  (', $from->frachtraum->fracht[$i]->anzahl, ')</td><td>--></td><td>', $konto->frachtraum->fracht[$i]->anzahl, '</td></tr>';

    echo '<tr><td></td><td></td><td>', $from->frachtraum->gesamt(), '/', $from->frachtraum->max, '</td><td></td><td>unendlich</td></td></tr>';
    echo '</table>';
    echo '<input type="hidden" name="do" value="4">'; $bu = new Button("","transferieren"); $bu->printme(); echo '</form>';
}


if ($_GET["modus"] == 2) {
    if (!in_array($to->besitzer->id, $from->besitzer->vertrag("handel")) && $from->schildstatus == 1 && $from->besitzer->id != $to->besitzer->id && $from->besitzer->allianz->id != $to->from->besitzer->allianz->id)
        die("Schilde sind aktiviert. Beamen nicht möglich!");
    echo '<h3>Transfer</h3>Energie: ', $to->energie, '<br />';
    echo '<form action="beam.php?modus=2&to=', $tosplit[0] == 'S' ? 'S-' : 'P-', $to->id, '&from=', $fromsplit[0] == 'S' ? 'S-' : 'P-', $from->id, '" method="post"><table class="invitetable" style="text-align:center;">';
    echo '<tr><th>Material</th><th>', $to->name, '</th><th></th><th></th><th>', $from->name, '</th></tr>';
    for ($i = 0; $i < count($from->frachtraum->fracht); $i++)
        if ($from->frachtraum->fracht[$i]->anzahl > 0)
            echo '<tr><th>'.$from->frachtraum->fracht[$i]->name.'</th><td>'.$to->frachtraum->fracht[$i]->anzahl.'</td><td><img src="images/misc/'.$from->frachtraum->fracht[$i]->bild.'" border="0" /></td><td><--</td><td><input type="text" size="6" name="p'.$i.'"></td><td>'.$from->frachtraum->fracht[$i]->anzahl.'</td></tr>';

    echo '<tr><td></td><td>', $to->frachtraum->gesamt(), '/', $to->frachtraum->max, '</td><td></td><td></td><td></td><td>', $from->frachtraum->gesamt(), '/', $from->frachtraum->max, '</td></tr>';

    echo '</table>';
    echo '<input type="hidden" name="do" value="2">'; $bu = new Button("","transferieren"); $bu->printme(); echo '</form>';
}



if ($_GET["modus"] == 3) {
    $konto = new Konto($_SESSION["Id"]);
    echo '<h3>Transfer</h3>Energie: ', $to->energie, '<br />';
    echo '<form action="beam.php?modus=3&to=', $tosplit[0] == 'S' ? 'S-' : 'P-', $to->id, '&from=', $fromsplit[0] == 'S' ? 'S-' : 'P-', $from->id, '" method="post"><table class="bordered">';
    echo '<tr><td></td><td></td><td>', $to->name, '</td><td></td><td>Warenkonto</td></tr>';
    for ($i = 0; $i < count($to->frachtraum->fracht); $i++)
        if ($konto->frachtraum->fracht[$i]->anzahl > 0)
            echo '<tr><td>'.$konto->frachtraum->fracht[$i]->name.'</td><td><img src="images/misc/'.$konto->frachtraum->fracht[$i]->bild.'" border="0" /></td><td>', $to->frachtraum->fracht[$i]->anzahl, '</td><td><--</td><td><input type="text" size="6" name="p'.$i.'">  (', $konto->frachtraum->fracht[$i]->anzahl, ')</td></tr>';



    echo '<tr><td></td><td></td><td>', $to->frachtraum->gesamt(), '/', $to->frachtraum->max, '</td><td></td><td>unendlich</td></td></tr>';

    echo '</table>';
    echo '<input type="hidden" name="do" value="3">'; $bu = new Button("","transferieren"); $bu->printme(); echo '</form>';
}


echo '<br />';
if ($from->besitzer->id == $_SESSION["Id"] && $from->typ == 's' && $modus == 2) {
    $bu = new Button("schiffe.php?sid=". $from->id,"vor zum Zielschiff");
    $bu->printme();
}
if ($from->besitzer->id == $_SESSION["Id"] && $from->typ != 's' && $modus == 2) {
    $bu = new Button("planet.php?pid=". $from->id,"vor zum Zielplaneten");
    $bu->printme();
}

//echo $to->besitzer->id==$_SESSION["Id"] ,' - ', $to->typ=='s' ,' - ', $modus==1;

if ($modus >= 3) {
    $bu = new Button("konto.php","Zum Warenkonto");
    $bu->printme();
}
if ($to->besitzer->id == $_SESSION["Id"] && $to->typ == 's' && $modus == 1) {
    $bu = new Button("schiffe.php?sid=". $to->id,"vor zum Zielschiff");
    $bu->printme();
}
if ($to->besitzer->id == $_SESSION["Id"] && $to->typ != 's' && $modus == 1) {
    $bu = new Button("planet.php?pid=".$to->id,"vor zum Zielplaneten");
    $bu->printme();
}
if ($to->typ == 's' && ($modus == 2 || $modus == 3)) {
    $bu = new Button("schiffe.php?sid=".$to->id,"zurück zum Schiff");
    $bu->printme();
}
if ($to->typ != 's' && ($modus == 2 || $modus == 3)) {
    $bu = new Button("planet.php?pid=".$to->id,"zurück zum Planeten");
    $bu->printme();
}
if ($from->typ == 's' && ($modus == 1 || $modus == 4)) {
    $bu = new Button("schiffe.php?sid=".$from->id,"zurück zum Schiff");
    $bu->printme();
}
if ($from->typ != 's' && ($modus == 1 || $modus == 4)) {
    $bu = new Button("planet.php?pid=".$from->id,"zurück zum Planeten");
    $bu->printme();
}



include("foot.php");
?>
