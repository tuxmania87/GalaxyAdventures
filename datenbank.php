<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

$verbindung = get_verbindung();

  function bool2string($a) {
        return $a ? 'ja' : 'nein';
    }

if (!isset($_GET["kategorie"])) {
    echo '<ul>';
    echo '<li>';
    $bu = new Button("datenbank.php?kategorie=planetfeld", "Planetenfelder");
    $bu->printme();
    echo '</li>';
    echo '<li>';
    $bu = new Button("datenbank.php?kategorie=gebaude", "Gebäude");
    $bu->printme();
    echo '</li>';
    echo '<li>';
    $bu = new Button("datenbank.php?kategorie=weltraum", "Weltraumfelder");
    $bu->printme();
    echo '</li>';
    echo '<li>';
    $bu = new Button("datenbank.php?kategorie=rohstoffe", "Rohstoffe");
    $bu->printme();
    echo '</li>';
    echo '<li>';
    $bu = new Button("datenbank.php?kategorie=schiffe", "Schiffe");
    $bu->printme();
    echo '</li>';
    echo '<li>';
    $bu = new Button("datenbank.php?kategorie=systems", "Systemtypen");
    $bu->printme();
    echo '</li>';
    echo '</ul>';

    die();
}

if ($_GET["kategorie"] == "planetfeld") {

    echo '<h3>Planetenfelder</h3>';
    echo '<table class="liste"><tr><th>Id</th><th>Name</th><th>Bild</th></tr>';

    $q = mysqli_query($verbindung, "select * from planetenfelder");
    while ($r = mysqli_fetch_array($q)) {
        echo '<tr>';
        echo '<td>' . $r["id"] . '</td>';
        echo '<td>' . $r["name"] . '</td>';
        echo '<td><img src="images/buildings/' . $r["bild"] . '" border="0" /></td>';
        echo '</tr>';
    }
    echo '</table>';
}

if ($_GET["kategorie"] == "gebaude") {
    echo '<h3>Gebäude</h3>';
    echo '<table class="liste"><tr><th>Id</th><th>Display</th><th>Name</th><th>Kosten</th><th>Dauerkosten</th><th>Effekt</th><th>Dauereffekt</th><th>Bauzeiten</th><th>Baubar auf</th></tr>';

    $q = mysqli_query($verbindung, "select id from gebaude");
    while ($r = mysqli_fetch_array($q)) {
        $b = new Bauplan_Gebaude($r["id"]);


        echo '<tr><td>' . $b->id . '</td><td>';
        for ($i = 0; $i < sizeof($b->bild); $i++) {
            echo '<img src="images/buildings/' . $b->bild[$i] . '" border="0" />';
        }
        echo '</td>';
        echo '<td>' . $b->name . '</td>';
        echo '<td><table>';
        for ($i = 0; $i < sizeof($b->baukosten->fracht); $i++) {
            if ($b->baukosten->fracht[$i]->anzahl > 0) {
                echo '<tr><td>' . $b->baukosten->fracht[$i]->name . '</td>';
                echo '<td><img src="images/misc/' . $b->baukosten->fracht[$i]->bild . '" border="0" /></td>';
                echo '<td>' . $b->baukosten->fracht[$i]->anzahl . '</td></tr>';
            }
        }
        echo '</table></td>';
        echo '<td><table>';
        for ($i = 0; $i < sizeof($b->braucht->fracht); $i++) {
            if ($b->braucht->fracht[$i]->anzahl > 0) {
                echo '<tr><td>' . $b->braucht->fracht[$i]->name . '</td>';
                echo '<td><img src="images/misc/' . $b->braucht->fracht[$i]->bild . '" border="0" /></td>';
                echo '<td>' . $b->braucht->fracht[$i]->anzahl . '</td></tr>';
            }
        }
        echo '</table></td>';
        echo '<td>';
        if ($b->lager > 0)
            echo "+" . $b->lager . " Lager<br />";
        if ($b->epslager > 0)
            echo "+" . $b->epslager . " EPS-Lager<br />";
        if ($b->schilde > 0)
            echo "+" . $b->schilde . " Schilde<br />";
        if ($b->laser > 0)
            echo "+" . $b->laser . " Phaser<br />";
        if ($b->sonstiges)
            echo $b->sonstiges;
        echo '</td>';
        echo '<td><table>';
        for ($i = 0; $i < sizeof($b->produziert->fracht); $i++) {
            if ($b->produziert->fracht[$i]->anzahl > 0) {
                echo '<tr><td>' . $b->produziert->fracht[$i]->name . '</td>';
                echo '<td><img src="images/misc/' . $b->produziert->fracht[$i]->bild . '" border="0" /></td>';
                echo '<td>' . $b->produziert->fracht[$i]->anzahl . '</td></tr>';
            }
        }
        echo '</table></td>';
        echo '<td>';
        echo $b->bauzeit . " Ticks";
        echo '</td>';
        echo '<td>';
        for ($i = 0; $i < sizeof($b->untergrund); $i++) {
            echo '<img src="images/buildings/' . $b->untergrund[$i]->bild . '" border="0" />';
        }
        echo '</td></tr>';
    }

    echo '</table>';
}

if ($_GET["kategorie"] == "rohstoffe") {
    echo '<h3>Rohstoffe</h3>';

    echo '<table class="liste"><tr><th>id</th><th>Name</th><th>Bild</th></tr>';

    $list = Res::getList();
    for ($i = 0; $i < sizeof($list); $i++) {
        echo '<tr><td>' . $list[$i]->id . '<td>' . $list[$i]->name . '</td><td><img src="images/misc/' . $list[$i]->bild . '" border="0" /></td></tr>';
    }
    echo '</table><br />';
}


if ($_GET["kategorie"] == "weltraum") {
    echo '<h3>Weltraumfelder</h3>';

    echo '<table class="liste">';
    $list = Weltraumfelder::getList();

  

    echo '<table>';
    for ($i = 0; $i < sizeof($list); $i++) {
        if ($i % 3 == 0 || $i == 0)
            echo '<tr>';
        echo '<td><table class="liste">';
        echo '<tr><th>ID</th><td>' . $list[$i]->id . '</td></tr>';
        echo '<tr><th>Name</th><td>' . $list[$i]->name . '</td></tr>';
        echo '<tr><th>Bild</th><td><img src="images/' . $list[$i]->bild . '" border="0" onmouseover="Tip(\'' . $list[$i]->tooltip . '\')" onmouseout="UnTip()" /></td></tr>';
        echo '<tr><th>Einflugkosten</th><td>' . $list[$i]->einflugkosten . '</td></tr>';
        echo '<tr><th>passierbar</th><td>' . bool2string($list[$i]->passierbar) . '</td></tr>';
        echo '<tr><th>Beschreibung</th><td>' . $list[$i]->beschreibung . '</td></tr>';
        //echo '<tr><th>Tooltip</th><td>'.$list[$i]->name.'</td></tr>';
        echo '<tr><th>Erzvorkommen</th><td>' . bool2string($list[$i]->erz > 0) . '</td></tr>';
        echo '<tr><th>Deuteriumvorkommen</th><td>' . bool2string($list[$i]->deut > 0) . '</td></tr>';
        echo '<tr><th>bebaubar</th><td>' . bool2string($list[$i]->bebaubar) . '</td></tr>';
        echo '<tr><th>tödlich</th><td>' . bool2string($list[$i]->deadly) . '</td></tr>';
        echo '<tr><th>Energieverlust</th><td>' . ($list[$i]->energieverlust*10) . '%</td></tr>';
        echo '<tr><th>Ausfallen der<br />Waffen und Schilde</th><td>' . bool2string($list[$i]->hide) . '</td></tr>';
        echo '</table></td>';

        if ($i + 1 % 3 == 0)
            echo '</tr>';
    }
    echo '</table>';
}

if ($_GET["kategorie"] == "schiffe") {
    $l = Bauplan_Schiffe::getList();
    echo '<table class="invitetable" style="text-align:center;">';
    echo '<tr><th>ID</th><th>Name</th><th>Bild</th><th>Hülle</th><th>Schilde</th><th>Phaser</th><th>Torpedo</th><th>Gondeln</th><th>Lager</th><th>EPS</th><th>Reaktor</th><th>Warpkern</th><th>Flugkosten</th><th>LRS</th><th>baubar von Spielern</th></tr>';
    for($i = 0 ; $i< sizeof($l);$i++) {
        echo '<tr><td>'.$l[$i]->id.'</td><td>'.$l[$i]->klasse.'</td><td><img src="'.$l[$i]->bild.'" border="0" /></td><td>'.$l[$i]->maxhull.'</td><td>'.$l[$i]->maxschilde.'</td><td>'.$l[$i]->laser.' ('.$l[$i]->maxphaser.')</td><td>'.$l[$i]->maxgondeln.'</td><td>'.$l[$i]->lager.'</td><td>'.$l[$i]->maxenergie.'</td><td>'.$l[$i]->energieoutput.'</td>';
        echo '<td>'.$l[$i]->maxwarpkern.'</td><td>'.$l[$i]->flugkosten.'</td><td>'.$l[$i]->lrs.'</td><td>'.  bool2string($l[$i]->siedler).'</td></tr>';
    }
    echo '</table>';
}

if ($_GET["kategorie"] == "systems") {
    $l = Systemfelder::getList();
    echo '<table class="invitetable" style="text-align:center;">';
    echo '<tr><th>ID</th><th>Name</th><th>Bild</th></tr>';
    for($i = 0 ; $i< sizeof($l);$i++) {
        echo '<tr><td>'.$l[$i]->id.'</td><td>'.$l[$i]->name.'</td><td><img src="images/systems/'.$l[$i]->bild.'" border="0" /></td></tr>';
    }
    echo '</table>';
}


include("foot.php");
?>
