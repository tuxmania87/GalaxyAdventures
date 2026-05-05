<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';
include_once 'connect.php';
include_once 'auth.php';

requireLogin();
$verbindung = get_verbindung();

function bool2string(bool $a): string
{
    return $a ? 'ja' : 'nein';
}

function frachttabelle(array $fracht): string
{
    $rows = '';
    foreach ($fracht as $f) {
        if ($f->anzahl > 0) {
            $rows .= '<tr>'
                   . '<td>' . htmlspecialchars($f->name) . '</td>'
                   . '<td><img src="images/misc/' . $f->bild . '" border="0" /></td>'
                   . '<td>' . $f->anzahl . '</td>'
                   . '</tr>';
        }
    }
    return $rows ? '<table>' . $rows . '</table>' : '—';
}

$kategorie = $_GET['kategorie'] ?? '';

// Übersicht: Kategorie nicht gesetzt
if ($kategorie === '') {
    $kategorien = [
        'planetfeld' => 'Planetenfelder',
        'gebaude'    => 'Gebäude',
        'weltraum'   => 'Weltraumfelder',
        'rohstoffe'  => 'Rohstoffe',
        'schiffe'    => 'Schiffe',
        'systems'    => 'Systemtypen',
    ];
    echo '<ul>';
    foreach ($kategorien as $key => $label) {
        echo '<li>';
        $bu = new Button('datenbank.php?kategorie=' . $key, $label);
        $bu->printme();
        echo '</li>';
    }
    echo '</ul>';
    include 'foot.php';
    exit;
}

// --- Planetenfelder ---
if ($kategorie === 'planetfeld') {
    echo '<h3>Planetenfelder</h3>';
    echo '<table class="liste"><tr><th>Id</th><th>Name</th><th>Bild</th></tr>';
    $q = mysqli_query($verbindung, 'SELECT * FROM planetenfelder');
    while ($r = mysqli_fetch_array($q)) {
        echo '<tr>'
           . '<td>' . $r['id'] . '</td>'
           . '<td>' . htmlspecialchars($r['name']) . '</td>'
           . '<td><img src="images/buildings/' . $r['bild'] . '" border="0" /></td>'
           . '</tr>';
    }
    echo '</table>';
}

// --- Gebäude ---
if ($kategorie === 'gebaude') {
    echo '<h3>Gebäude</h3>';
    echo '<table class="liste"><tr>'
       . '<th>Id</th><th>Display</th><th>Name</th><th>Kosten</th>'
       . '<th>Dauerkosten</th><th>Effekt</th><th>Dauereffekt</th>'
       . '<th>Bauzeiten</th><th>Baubar auf</th>'
       . '</tr>';

    $q = mysqli_query($verbindung, 'SELECT id FROM gebaude');
    while ($r = mysqli_fetch_array($q)) {
        $b = new Bauplan_Gebaude($r['id']);

        $bilder = '';
        foreach ($b->bild as $bild) {
            $bilder .= '<img src="images/buildings/' . $bild . '" border="0" />';
        }

        $effekte = '';
        if ($b->lager    > 0) $effekte .= '+' . $b->lager    . ' Lager<br />';
        if ($b->epslager > 0) $effekte .= '+' . $b->epslager . ' EPS-Lager<br />';
        if ($b->schilde  > 0) $effekte .= '+' . $b->schilde  . ' Schilde<br />';
        if ($b->laser    > 0) $effekte .= '+' . $b->laser    . ' Phaser<br />';
        if ($b->sonstiges)    $effekte .= $b->sonstiges;

        $untergrund = '';
        foreach ($b->untergrund as $u) {
            $untergrund .= '<img src="images/buildings/' . $u->bild . '" border="0" />';
        }

        echo '<tr>'
           . '<td>' . $b->id . '</td>'
           . '<td>' . $bilder . '</td>'
           . '<td>' . htmlspecialchars($b->name) . '</td>'
           . '<td>' . frachttabelle($b->baukosten->fracht) . '</td>'
           . '<td>' . frachttabelle($b->braucht->fracht) . '</td>'
           . '<td>' . ($effekte ?: '—') . '</td>'
           . '<td>' . frachttabelle($b->produziert->fracht) . '</td>'
           . '<td>' . $b->bauzeit . ' Ticks</td>'
           . '<td>' . $untergrund . '</td>'
           . '</tr>';
    }
    echo '</table>';
}

// --- Rohstoffe ---
if ($kategorie === 'rohstoffe') {
    echo '<h3>Rohstoffe</h3>';
    echo '<table class="liste"><tr><th>Id</th><th>Name</th><th>Bild</th></tr>';
    foreach (Res::getList() as $res) {
        echo '<tr>'
           . '<td>' . $res->id . '</td>'
           . '<td>' . htmlspecialchars($res->name) . '</td>'
           . '<td><img src="images/misc/' . $res->bild . '" border="0" /></td>'
           . '</tr>';
    }
    echo '</table>';
}

// --- Weltraumfelder ---
if ($kategorie === 'weltraum') {
    echo '<h3>Weltraumfelder</h3><table>';
    $list = Weltraumfelder::getList();
    foreach ($list as $i => $feld) {
        if ($i % 3 === 0) echo '<tr>';
        echo '<td><table class="liste">'
           . '<tr><th>ID</th><td>'          . $feld->id . '</td></tr>'
           . '<tr><th>Name</th><td>'        . htmlspecialchars($feld->name) . '</td></tr>'
           . '<tr><th>Bild</th><td><img src="images/' . $feld->bild . '" border="0" '
           .   'onmouseover="Tip(\'' . htmlspecialchars($feld->tooltip) . '\')" onmouseout="UnTip()" /></td></tr>'
           . '<tr><th>Einflugkosten</th><td>'     . $feld->einflugkosten . '</td></tr>'
           . '<tr><th>passierbar</th><td>'        . bool2string((bool)$feld->passierbar) . '</td></tr>'
           . '<tr><th>Beschreibung</th><td>'      . htmlspecialchars($feld->beschreibung) . '</td></tr>'
           . '<tr><th>Erzvorkommen</th><td>'      . bool2string($feld->erz > 0) . '</td></tr>'
           . '<tr><th>Deuteriumvorkommen</th><td>'. bool2string($feld->deut > 0) . '</td></tr>'
           . '<tr><th>bebaubar</th><td>'          . bool2string((bool)$feld->bebaubar) . '</td></tr>'
           . '<tr><th>tödlich</th><td>'           . bool2string((bool)$feld->deadly) . '</td></tr>'
           . '<tr><th>Energieverlust</th><td>'    . ($feld->energieverlust * 10) . '%</td></tr>'
           . '<tr><th>Waffen/Schilde aus</th><td>'. bool2string((bool)$feld->hide) . '</td></tr>'
           . '</table></td>';
        if (($i + 1) % 3 === 0) echo '</tr>';
    }
    echo '</table>';
}

// --- Schiffe ---
if ($kategorie === 'schiffe') {
    echo '<table class="invitetable" style="text-align:center;">';
    echo '<tr>'
       . '<th>ID</th><th>Name</th><th>Bild</th><th>Hülle</th><th>Schilde</th>'
       . '<th>Phaser</th><th>Torpedo</th><th>Gondeln</th><th>Lager</th>'
       . '<th>EPS</th><th>Reaktor</th><th>Warpkern</th><th>Flugkosten</th>'
       . '<th>LRS</th><th>baubar von Spielern</th>'
       . '</tr>';
    foreach (Bauplan_Schiffe::getList() as $s) {
        echo '<tr>'
           . '<td>' . $s->id . '</td>'
           . '<td>' . htmlspecialchars($s->klasse) . '</td>'
           . '<td><img src="' . $s->bild . '" border="0" /></td>'
           . '<td>' . $s->maxhull . '</td>'
           . '<td>' . $s->maxschilde . '</td>'
           . '<td>' . $s->laser . ' (' . $s->maxphaser . ')</td>'
           . '<td>' . $s->maxgondeln . '</td>'
           . '<td>' . $s->lager . '</td>'
           . '<td>' . $s->maxenergie . '</td>'
           . '<td>' . $s->energieoutput . '</td>'
           . '<td>' . $s->maxwarpkern . '</td>'
           . '<td>' . $s->flugkosten . '</td>'
           . '<td>' . $s->lrs . '</td>'
           . '<td>' . bool2string((bool)$s->siedler) . '</td>'
           . '</tr>';
    }
    echo '</table>';
}

// --- Systemtypen ---
if ($kategorie === 'systems') {
    echo '<table class="invitetable" style="text-align:center;">';
    echo '<tr><th>ID</th><th>Name</th><th>Bild</th></tr>';
    foreach (Systemfelder::getList() as $s) {
        echo '<tr>'
           . '<td>' . $s->id . '</td>'
           . '<td>' . htmlspecialchars($s->name) . '</td>'
           . '<td><img src="images/systems/' . $s->bild . '" border="0" /></td>'
           . '</tr>';
    }
    echo '</table>';
}

include 'foot.php';
