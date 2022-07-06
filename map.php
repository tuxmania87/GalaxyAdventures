<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

$getx = $_GET["x"];
$gety = $_GET["y"];
$getsystem = $_GET["system"];

if (isset($getsystem) && !ctype_digit($getsystem))
    die("ID Fehler");
if ($getx > 0 && $gety > 0 && $getsystem > 0)
    die("Du kannst nur Systeme oder den Weltraum betrachten");

if ($getx > 0 && $gety > 0) {
    $system = new System(0);

    if ($getx <= 0)
        $getx = 10;
    if ($gety <= 0)
        $gety = 10;
}
if ($getsystem > 0)
    $system = new System($getsystem);

if ($system->id == 0) {
    echo '<h2>Der Weltraum</h2>';
    echo '<table class="bordered">';

    echo '<tr><td>x/y</td>';
    for ($i = $getx - 10; $i <= 10 + $getx; $i++)
        echo '<td>', $i, '</td>';
    echo '</tr>';

    for ($y = $gety - 10; $y <= 10 + $gety; $y++)
        for ($x = $getx - 10; $x <= 10 + $getx; $x++) {
            if ($x == $getx - 10)
                echo '<tr><td>', $y, '</td>';

            $f = new Weltraum($x,$y,0,false);
            echo '<td style="width:32px;height:32px;background-repeat:no-repeat;background-image:url(\'images/'.$f->bild.'\');">';
            echo '<a onmouseover="Tip(\'<b>'.$f->name.'</b><br />'.$f->tooltip.'\')" onmouseout="UnTip()" href="map.php?x=', $x, '&y=', $y, '" style="display:block;padding:8px;padding:14px;"></a></td>';

            echo '</td>';
            if ($x == 20 + $getx)
                echo '</tr>';
        }

    echo '</table>';
} else {
    echo '<h2>Das ', $system->name, '-System</h2>';
    echo '<table>';
    for ($y = 0; $y <= 20; $y++)
        for ($x = 0; $x <= 20; $x++) {
            if ($x == 0)
                echo '<tr><td>', $y, '</td>';
            if ($y == 0 && $x > 0)
                echo '<td>', $x, '</td>'; else {
    		 $f = new Weltraum($x,$y,$system->id,false);
			 echo '<td style="width:32px;height:32px;background-repeat:no-repeat;background-image:url(\'images/'.$f->bild.'\');">';
            echo '<a onmouseover="Tip(\'<b>'.$f->name.'</b><br />'.$f->tooltip.'\')" onmouseout="UnTip()" href="map.php?x=', $x, '&y=', $y, '" style="display:block;padding:8px;padding:14px;"></a></td>';

                                if (!$done && $x > 0)
//                    echo '<td><a href="map.php?x=', $system->x, '&y=', $system->y, '"><img src="weltraum.jpg" border="0" /></a></td>';
                if ($x == 20)
                    echo '</tr>';
            }
        }
    echo '</table>';
}


include("foot.php");
?>
