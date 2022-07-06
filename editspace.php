<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

$account = new Account($_SESSION["Id"]);
if (!$account->mapper)
    die("Error: Insufficient Access Level");


if (($_GET["x"]) == '' || $_GET["y"] == '') {
    $sx = 10;
    $sy = 10;
} else {
    $sx = $_GET["x"];
    $sy = $_GET["y"];
}

if (ctype_digit($_GET["del"]) && $_GET["del"] > 0) {
    $abfrage = mysql_query("SELECT * FROM schiffe WHERE besitzer!=2 AND system='" . $_GET["del"] . "'");
    $schiffgefunden = mysql_num_rows($abfrage) > 0;

    $abfrage = mysql_query("SELECT * FROM planeten WHERE besitzer!=2 AND system='" . $_GET["del"] . "'");
    $planetgefunden = mysql_num_rows($abfrage) > 0;

    if ($schiffgefunden)
        echo "Hinweis: Es sind noch schiffe im System. Es kann nicht gel&ouml;scht werden!";
    if ($planetgefunden)
        echo "Hinweis: Es sind noch besidelte planeten im System. Es kann nicht gel&ouml;scht werden!";
    if (!$schiffgefunden && !$planetgefunden) {
        mysql_query("DELETE FROM weltraum WHERE system='" . $_GET["del"] . "'") or die(mysql_error());
        mysql_query("DELETE FROM schiffe WHERE system='" . $_GET["del"] . "'");
        mysql_query("DELETE FROM planeten WHERE system='" . $_GET["del"] . "'");
        mysql_query("DELETE FROM systeme WHERE id='" . $_GET["del"] . "'");
    }
}

if (ctype_digit($_GET["dx"]) && $_GET["dx"] > -25 && ctype_digit($_GET["dy"]) && $_GET["dy"] > -25) {  //loeschen
    mysql_query("DELETE FROM planeten WHERE x='" . $_GET["dx"] . "' AND y='" . $_GET["dy"] . "' AND system='0'");
    mysql_query("DELETE FROM weltraum WHERE x='" . $_GET["dx"] . "' AND y='" . $_GET["dy"] . "' AND system='0'");
}

if(!ctype_digit($_GET["pinsel"]) && ctype_digit(substr($_GET["pinsel"], 2)) && isset($_GET["tx"]) ) {
    $sysid = substr($_GET["pinsel"], 2);
   echo "insert into systeme (id,typ,name,x,y) values (NULL,'".$sysid."','".mysql_real_escape_string($_POST["psysname"])."','".$_GET["tx"]."','".$_GET["ty"]."')";
    mysql_query("insert into systeme (id,typ,name,x,y) values (NULL,'".$sysid."','".mysql_real_escape_string($_POST["psysname"])."','".$_GET["tx"]."','".$_GET["ty"]."')");
}


if (ctype_digit($_GET["rx"]) && $_GET["rx"] > -25 && ctype_digit($_GET["ry"]) && $_GET["ry"] > -25 && $_POST["operation"] >= 0 && isset($_POST["operation"])) {
    $systeme = array("bblaublau", "bblaugelb", "bblauorange", "bblaurot", "bblauschwarz", "bblauweiss", "blau", "blaubig", "blaublau", "brotblau", "brotgelb", "brotorange", "brotrot", "brotschwarz", "brotweiss", "gelb", "gelbblau", "gelbgelb", "gelbweiss", "orange", "orangegelb", "orangeorange", "orangeweiss", "rot", "rotbig", "rotblau", "rotgelb", "rotorange", "rotrot", "rotweiss", "weiss", "weissblau");
    $bildinsert = $systeme[$_POST["operation"]];
    $lastid = checkforlastid("systeme") + 1;
    mysql_query("INSERT INTO systeme (id,x,y,name,bild) VALUES ('$lastid','" . $_GET["rx"] . "','" . $_GET["ry"] . "','" . $_POST["sysname"] . "','" . $bildinsert . ".jpg')") or die(mysql_error());
}



if (ctype_digit($_GET["sx"]) && $_GET["sx"] > -25 && ctype_digit($_GET["sy"]) && $_GET["sy"] > -25) {
    if ($_GET["pinsel"] == '')
        echo '<font color="red"><b>Bitte erst w&auml;hlen!</b></font><br />';
    //WELTALL
    else if(ctype_digit($_GET["pinsel"])) {
        echo "INSERT INTO weltraum (x,y,typ,system) VALUES ('" . $_GET["sx"] . "','" . $_GET["sy"] . "','".$_GET["pinsel"]."','0')";
          mysql_query("INSERT INTO weltraum (x,y,typ,system) VALUES ('" . $_GET["sx"] . "','" . $_GET["sy"] . "','".$_GET["pinsel"]."','0')");   
    } else {
        //insert system
        $sys = new Systemfelder(substr($_GET["pinsel"], 2));
        echo '<img src="images/systems/'.$sys->bild.'" border="0" /><form action="editspace.php?tx='.$_GET["sx"].'&ty='.$_GET["sy"].'&pinsel='.$_GET["pinsel"].'" method="post">Name des Systems: <input type="text" name="psysname" />
            <input type="submit" value="eintragen" /></form>';
        die();
    }
}

if ($klasse != 'S' || !(ctype_digit($_GET["sx"]) && $_GET["sx"] > -25 && ctype_digit($_GET["sy"]) && $_GET["sy"] > -25)) {
//KEIN PINSEL
    if (!isset($_GET["pinsel"]) || $_GET["pinsel"] == '') {
        echo 'Du hast nichts ausgew&auml;hlt. Bitte <a class="thickbox" href="editspacetool.php?height=470&x=', $_GET["x"], '&y=', $_GET["y"], '"> >HIER< </a> ausw&auml;hlen!<br />';
    } else {
    echo '<div style="width:400px;border:2px solid red;">';

    $feld = 0;
    if(ctype_digit($_GET["pinsel"])) {
        $feld = new Weltraumfelder(ctype_digit($_GET["pinsel"])?$_GET["pinsel"]:'0');
    } else {
        $feld = new Systemfelder(substr($_GET["pinsel"], 2));
        $feld->bild = 'systems/' . $feld->bild;
    }
    echo '<a class="thickbox" href="editspacetool.php?x=', $_GET["x"], '&y=', $_GET["y"], '"><img src="images/'.$feld->bild.'" border="0" /> - '.$feld->name.'</a><br />';
    $dovar = $feld->id;

    echo '</div>';
    }



    echo '<br /><hr /><br /><table>';
    for ($y = $sy - 10 - 1; $y <= $sy + 10; $y++)
        for ($x = $sx - 10 - 1; $x <= $sx + 10; $x++) {
            if ($x == $sx - 10 - 1)
                echo '<tr><td>', $y, '</td>';
            if ($y == $sy - 10 - 1 && $x > $sx - 10 - 1)
                echo '<td><center>', $x - 1, '</td>';
            if ($y > $sy - 10 - 1) {
                $done = false;
                $cfeld = new Weltraum($x, $y, "0", false);

                $csys = new System(array($x,$y));
                
                if($csys->feld != null) {
                    echo '<td><a href="editspace.php?pinsel=', $_GET["pinsel"], '&x=', $_GET["x"], '&y=', $_GET["y"], '&del=',$csys->id, '"><img src="images/systems/' . $csys->bild . '" border="0" /></a></td>';
                $done = true;
                }
                else if($cfeld->feld->id == 0) {
                echo '<td><a href="editspace.php?pinsel=', $_GET["pinsel"], '&x=', $_GET["x"], '&y=', $_GET["y"], '&sx=', $x, '&sy=', $y, '"><img src="images/' . $cfeld->feld->bild . '" border="0" /></a></td>';
                $done = true;
                } else {
                    echo '<td><a href="editspace.php?pinsel=', $_GET["pinsel"], '&x=', $_GET["x"], '&y=', $_GET["y"], '&dx=', $x, '&dy=', $y, '"><img src="images/' . $cfeld->feld->bild . '" border="0" /></a></td>';
                     $done = true;
                }


                if (!$done) {
                    if ($x < -10 || $y < -10)
                        echo '<td><img src="weltraum.jpg" style="border:1px solid red;" /></td>';
                    else
                        echo '<td><a href="editspace.php?pinsel=', $_GET["pinsel"], '&x=', $_GET["x"], '&y=', $_GET["y"], '&sx=', $x, '&sy=', $y, '"><img src="weltraum.jpg" border="0" /></a></td>';
                }
                if ($x == $sx + 10)
                    echo '</tr>';
            }
        }
    echo '</table>';
    echo '<br /><form action="editspace.php" method="get"><input type="hidden" name="pinsel" value="', $_GET["pinsel"], '" /><input type="text" size="2" name="x" /> - <input type="text" size="2" name="y" /><br /><input type="submit" value="einstellen"></form>';
}
?>
