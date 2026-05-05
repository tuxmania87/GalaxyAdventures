<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

include_once 'auth.php';
$userId = requireLogin();
{

    $order = $_GET["order"];
    $tt = "";
    if ($order == 'na')
        $tt = " ORDER BY name ASC";
    if ($order == 'nd')
        $tt = " ORDER BY name DESC";
    if ($order == 'ka')
        $tt = " ORDER BY x,y ASC";
    if ($order == 'kd')
        $tt = " ORDER BY x,y DESC";
    if ($order == 'ta')
        $tt = " ORDER BY klasse ASC";
    if ($order == 'td')
        $tt = " ORDER BY klasse DESC";
    ?>

    <form action="createflotte.php" method="post">
        <h3>Schiffsauswahl Slots: <?php echo getSlot($_SESSION["Id"]) >= 50 ? '<span style="color:red;">' . getSlot($_SESSION["Id"]) . '</span>' : '<span style="color:green;">' . getSlot($_SESSION["Id"]) . '</span>'; ?>/50</h3><table class="liste">
            <tr>
                <th>Display</th>
                <th><a href="schiffchoice.php?order=<?php echo $order == 'ta' ? 'td' : 'ta'; ?>">Typ</a></th>
                <th><a href="schiffchoice.php?order=<?php echo $order == 'na' ? 'nd' : 'na'; ?>">Name</a></th>
                <th><a href="schiffchoice.php?order=<?php echo $order == 'ka' ? 'kd' : 'ka'; ?>">Position</a></th>
                <th>System</th>
                <th>S-Koords</th>
                <th>Energie</th>
                <th>Warpkern</th>
                <th>H&uuml;lle</td><td>Schilde</th>
                <th>Orbit</th>
            </tr>
            <?php
            $id = $_SESSION["Id"];
            $c = 1;
            if ($_GET["handel"] == 1)
                $abfrage = mysqli_query($verbindung, "SELECT * FROM `schiffe` WHERE besitzer='$id' AND typ='s' AND klasse='Handelsschiff' $tt"); else
                $abfrage = mysqli_query($verbindung, "SELECT * FROM `schiffe` WHERE besitzer='$id' AND typ='s' AND klasse!='Handelsschiff' $tt");
            while ($planet = mysqli_fetch_array($abfrage)) {
                $schiff = new Schiffe($planet["id"]);
                $system = $schiff->position->system;
                echo '<tr><td><a href="schiffe.php?sid=', $schiff->id, '"><img src="', $schiff->bild, '" border="0" /></a></td><td>', $schiff->klasse, '</td><td>', $schiff->name, ' <span style="color:silver;">(', $schiff->id, ')</span></td><td>', $schiff->position->x, '|', $schiff->position->y, '</td>';

                echo "<td>";
                if ($system->id == 0) {
                    echo "Weltraum";
                } else {
                    echo $system->name . "-System (" . $system->id . ")";
                }
                echo "</td>";
                echo '<td>', $system->x, '/', $system->y, '</td><td>', $schiff->energie, '/', $schiff->maxenergie, '+', $schiff->energieoutput, '</td><td>', $schiff->warpkern, '(', floor($schiff->warpkern / $schiff->energieoutput), ')</td>';

                $t_color = "";
                $t_color2 = "";

                if ($schiff->hull / $schiff->maxhull <= 0.5 && $schiff->hull / $schiff->maxhull > 0.2) {
                    $t_color = '<span style="color:yellow;">';
                    $t_color2 = "</span>";
                }

                if ($schiff->hull / $schiff->maxhull <= 0.2) {
                    $t_color = '<span style="color:red;">';
                    $t_color2 = "</span>";
                }

                echo '<td>'. $t_color . $schiff->hull. $t_color2 . '/'. $schiff->maxhull. '</td>';
                echo '<td>', $schiff->schildstatus == 1 ? '<span style="color:yellow;">' : '<span>', $schiff->schilde, '/', $schiff->maxschilde, '</span></td><td>', $schiff->position->orbit == 1 ? 'im Orbit' : 'nicht im Orbit', '</td>';
                if ($schiff->werft == 0)
                    echo '<td><input type="checkbox" name="schiff', $c, '" value="', $schiff->id, '"></td>';
                echo '</tr>';
                $c++;
            }
            echo '</table><br />Flottename: <input type="text" name="flottenname">';
            $bu = new Button("", "Flotte erstellen");
            $bu->printme();
            echo '<input type="hidden" name="anzahl" value="', $c, '"></form><br />';
            $abfrage = mysqli_query($verbindung, "SELECT * FROM `schiffe` WHERE besitzer='$id' AND typ=''");
            if (mysqli_num_rows($abfrage) > 0) {
                echo '<table class="bordered"><tr><td>Bild</td><td>Klasse</td><td>Position</td><td>Energie</td><td>Status</td></tr>';
            }
            while ($planet = mysqli_fetch_array($abfrage)) {
                $schiff = new Schiffe($planet["id"]);
                echo '<tr><td><img src="', $schiff->bild, '" border="0" /></td><td>', $schiff->klasse, '</td><td>(', $schiff->position->x, '|', $schiff->position->y, ') im ', $schiff->position->system->name, '-System (', $schiff->position->system->id, ')</td><td>', $schiff->energie, '/', $schiff->maxenergie, '</td><td><span style="color:yellow;">(noch ', $schiff->frachtraum->baustoff, ' Runden)</span></td></tr>';
            }
            echo '</table>';
        }


        include("foot.php");
        ?>
