<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

$sid = $_GET["sid"];
$tid = $_GET["tid"];

include_once 'auth.php';
$userId = requireLogin();
$sid = requireIntParam('sid');
$tid = requireIntParam('tid');
{
    $schiff = new Schiffe($sid);
    $target = new Schiffe($tid);

    if ($schiff->position->x == $target->position->x && $schiff->position->y == $target->position->y && $schiff->position->orbit == $target->position->orbit && $schiff->position->system->id == $target->position->system->id) {

        echo '<h2>Schiffsscan</h2>';
        echo '<h3>Lagerraum</h3>';
        echo '<table class="invitetable">';
        for ($i = 0; $i < count($target->frachtraum->fracht); $i++)
            if ($target->frachtraum->fracht[$i]->anzahl > 0)
                echo '<tr><th>',$target->frachtraum->fracht[$i]->name, '</th><td><img src="images/misc/', $target->frachtraum->fracht[$i]->bild, '" border="0" /></td><td>', $target->frachtraum->fracht[$i]->anzahl, '</td></tr>';
        echo '</table>';
        echo '<br />';
        $bu = new Button("schiffe.php?sid=". $sid,"zurück zum Schiff");
        $bu->printme();
    }
}
include("foot.php");
?>



