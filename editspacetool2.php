<?php
session_start();
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h3>Bitte Instrument w&auml;hlen:</h3>
<table>
<?php
echo '<tr><td><a href="editspace2.php?pinsel=W-d&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="deut.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-d&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">grosser Deutnebel, Einflug 3x, Sammeln 10 pro 1E</font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=W-dk&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="deutklein.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-dk&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">kleiner Deutnebel, Einflug 2x, Sammeln 5 pro 1E</font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=W-e&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="erz.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-e&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">grosses Erzfeld, Einflug 3x, Sammeln 8 pro 1E</font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=W-ek&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="erzklein.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-ek&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">kleines Erzfeld, Einflug 2x, Sammeln 4 pro 1E</font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=W-x&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="schwarzesloch.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-x&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">schwarzes Loch - Einflug IMMER tot</font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=W-b&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="nebel.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-b&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">Ceru Nebel, 2 Hullschaden pro Tick, kein beamen fighten schild</font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=W-g&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="green.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-g&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">meta nebel - 10% schilde pro tick ( von maxschilden )</font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=W-p&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="pulsar.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-p&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">gravi riss - sofort tot ohne schilde, ansonsten nichts </font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=W-radio&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="nebelgelb.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-radio&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">radioaktiver Nebel - bei einflug mit schilde 2% der hülle, ohne schilde 50% der hülle ,   pro tick wie einflug </font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=W-metrion&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="metrion.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-metrion&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">Metrion - beim einflug 10E verlust , pro tick 2 E</font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=W-lim&x=',$_GET["x"],'&y=',$_GET["y"],'"><img src="limes.jpg" border="0" /></a></td><td><a href="editspace2.php?pinsel=W-lim&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">Begrenzungsnebel</font></a></td></tr>';
echo '<tr><td><a href="editspace2.php?pinsel=S-1&x=',$_GET["x"],'&y=',$_GET["y"],'">SYSTEME</a></td><td><a href="editspace2.php?pinsel=S-1&x=',$_GET["x"],'&y=',$_GET["y"],'"><font color="yellow">SYSTEME</font></a></td></tr>';


?>
</table>
</body>
</html>