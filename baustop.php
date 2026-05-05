<?php

include("head.php");
include("navlogged.php");
include("klassen.php");
include_once 'auth.php';

$userId = requireLogin();
$pid    = requireIntParam('pid');
$fid    = requireIntParam('fid');

$planet = new Planeten($pid);
requireOwnership($planet->besitzer->id, 'Planet');

/**
 * Gebäude-Rückgabe-Tabelle:
 * Gebäude-ID => [Rückgabe-Baustoff, Rückgabe-Duranium, Rückgabe-Deuterium, Rückgabe-Tritanium, Gesamtgewicht, Name]
 */
$gebaeude = [
     1 => ['name' => 'Baustofffabrik',       'bs' =>  20, 'dur' =>   0, 'deu' =>   0, 'tri' => 0, 'total' =>  20],
     2 => ['name' => 'Lager',                'bs' =>  10, 'dur' =>   0, 'deu' =>   0, 'tri' => 0, 'total' =>  10],
     3 => ['name' => 'Solarstation',         'bs' =>  20, 'dur' =>   0, 'deu' =>   0, 'tri' => 0, 'total' =>  20],
     4 => ['name' => 'Werft',                'bs' => 100, 'dur' =>  50, 'deu' =>   0, 'tri' => 0, 'total' => 150],
     5 => ['name' => 'Plasmakanone',         'bs' => 130, 'dur' => 125, 'deu' =>   0, 'tri' => 0, 'total' => 255],
     6 => ['name' => 'Schildturm',           'bs' => 130, 'dur' =>  80, 'deu' =>   0, 'tri' => 0, 'total' => 210],
     7 => ['name' => 'Wasserwerk',           'bs' =>  30, 'dur' =>  10, 'deu' =>   0, 'tri' => 0, 'total' =>  40],
     8 => ['name' => 'Mine',                 'bs' =>  20, 'dur' =>   0, 'deu' =>   0, 'tri' => 0, 'total' =>  20],
     9 => ['name' => 'Duraniumanlage',       'bs' =>  30, 'dur' =>   0, 'deu' =>   0, 'tri' => 0, 'total' =>  30],
    10 => ['name' => 'Forschungslabor',      'bs' => 200, 'dur' => 150, 'deu' =>   0, 'tri' => 0, 'total' => 350],
    11 => ['name' => 'Terraforming I',       'bs' =>   0, 'dur' =>   0, 'deu' => 200, 'tri' => 0, 'total' => 200],
    12 => ['name' => 'Lagerhöhle',           'bs' =>  10, 'dur' =>   5, 'deu' =>   0, 'tri' => 0, 'total' =>  15],
    13 => ['name' => 'Hitzekraftwerk',       'bs' =>  40, 'dur' =>  20, 'deu' =>   0, 'tri' => 0, 'total' =>  60],
    14 => ['name' => 'Soriumfabrik',         'bs' => 300, 'dur' => 100, 'deu' =>   0, 'tri' => 0, 'total' => 400],
    15 => ['name' => 'Terraforming II',      'bs' =>   0, 'dur' =>   0, 'deu' => 200, 'tri' => 0, 'total' => 200],
    16 => ['name' => 'Schildturm II',        'bs' => 130, 'dur' =>  80, 'deu' =>   0, 'tri' => 0, 'total' => 210],
    17 => ['name' => 'Plasmakanone II',      'bs' => 130, 'dur' => 125, 'deu' =>   0, 'tri' => 0, 'total' => 300],
    18 => ['name' => 'Terraforming III',     'bs' =>   0, 'dur' =>   0, 'deu' => 300, 'tri' => 0, 'total' => 300],
    21 => ['name' => 'Teilchenbeschleuniger','bs' => 200, 'dur' => 150, 'deu' =>   0, 'tri' => 0, 'total' => 350],
    22 => ['name' => 'Tritaniumanlage',      'bs' => 250, 'dur' => 150, 'deu' =>   0, 'tri' => 0, 'total' => 400],
    23 => ['name' => 'Teilchenbeschl. II',   'bs' => 200, 'dur' => 150, 'deu' =>   0, 'tri' => 0, 'total' => 350],
    24 => ['name' => 'Fusionsreaktor',       'bs' => 100, 'dur' =>  40, 'deu' =>   0, 'tri' => 0, 'total' => 140],
];

$del  = isset($_POST['del']) ? intval($_POST['del']) : 0;
$feld = $planet->feld[$fid];

// --- Bau abbrechen und Ressourcen zurückgeben ---
if ($del > 0 && isset($gebaeude[$del]) && $feld->was == $del && $feld->bauzeit > 0) {
    $g       = $gebaeude[$del];
    $freiraum = $planet->frachtraum->max - $planet->frachtraum->gesamt();

    // Nur zurückgeben was reinpasst
    if ($freiraum >= $g['total']) {
        $planet->frachtraum->baustoff  += $g['bs'];
        $planet->frachtraum->duranium  += $g['dur'];
        $planet->frachtraum->deuterium += $g['deu'];
        $planet->frachtraum->tritanium += $g['tri'];
    }

    $feld->was    = 0;
    $feld->bauzeit = 0;
    $feld->save();
    $planet->frachtraum->save();
    echo '<meta http-equiv="refresh" content="0; URL=planet.php?pid=', $planet->id, '" />';
}

// --- Anzeige: welches Gebäude wird gerade gebaut? ---
if (isset($gebaeude[$feld->was]) && $feld->bauzeit > 0) {
    $g = $gebaeude[$feld->was];

    $rueckgabe = [];
    if ($g['bs']  > 0) $rueckgabe[] = $g['bs']  . ' Baustoff';
    if ($g['dur'] > 0) $rueckgabe[] = $g['dur']  . ' Duranium';
    if ($g['deu'] > 0) $rueckgabe[] = $g['deu']  . ' Deuterium';
    if ($g['tri'] > 0) $rueckgabe[] = $g['tri']  . ' Tritanium';

    echo 'Wenn du den Bau von <b>', htmlspecialchars($g['name']), '</b> stoppst, ',
         'erhältst du ', implode(' und ', $rueckgabe), ' zurück!<br />';
    echo '<br /><form action="baustop.php?pid=', $pid, '&fid=', $fid,
         '" method="post" onSubmit="return frage(2)">',
         '<input type="hidden" name="del" value="', $feld->was, '">',
         '<input type="submit" value="Bau stoppen"></form>';
}

echo '<br /><a href="planet.php?pid=', $pid, '">zurück zum Planeten</a>';

include("foot.php");
?>
