<?php
include_once("connect.php");
$planet1=0;$planet2=0;$planet3=0;$planet4=0;
$deutnebel=0; $blaunebel=0; $greennebel=0;
$abfrage=mysqli_query($verbindung, "SELECT * FROM schiffe");
while($row=mysqli_fetch_array($abfrage))
{
if($row["typ"]=='b') $blaunebel++;
if($row["typ"]=='g') $greennebel++;
if($row["typ"]=='d') $deutnebel++;
if($row["typ"]=='m' && $row["klasse"]=='m' && $row["besitzer"]==2) $planet1++;
if($row["typ"]=='m' && $row["klasse"]=='l' && $row["besitzer"]==2) $planet2++;
if($row["typ"]=='m' && $row["klasse"]=='i' && $row["besitzer"]==2) $planet3++;
if($row["typ"]=='m' && $row["klasse"]=='z' && $row["besitzer"]==2) $planet4++;
}
echo 'Alte (jetzige) Map:<br /><br />';
echo 'Deuteriumnebel: ',$deutnebel,'<br />';
echo 'Ceru: ',$blaunebel,'<br />';
echo 'Metaphasen: ',$greennebel,'<br /><br />';
echo 'Klasse M Planeten: ',$planet1,'<br />';
echo 'Klasse Lava Planeten: ',$planet2,'<br />';
echo 'Klasse Ice Planeten: ',$planet3,'<br />';
echo 'Klasse Wuste Planeten: ',$planet4,'<br />';

//welt 2
$planet1=0;$planet2=0;$planet3=0;$planet4=0;
$deutnebel=0; $blaunebel=0; $greennebel=0;
$abfrage=mysqli_query($verbindung, "SELECT * FROM schiffe2");
while($row=mysqli_fetch_array($abfrage))
{
if($row["typ"]=='b') $blaunebel++;
if($row["typ"]=='g') $greennebel++;
if($row["typ"]=='d') $deutnebel++;
if($row["typ"]=='m' && $row["klasse"]=='m' && $row["besitzer"]==2) $planet1++;
if($row["typ"]=='m' && $row["klasse"]=='l' && $row["besitzer"]==2) $planet2++;
if($row["typ"]=='m' && $row["klasse"]=='i' && $row["besitzer"]==2) $planet3++;
if($row["typ"]=='m' && $row["klasse"]=='z' && $row["besitzer"]==2) $planet4++;
}
echo '<br /><br />Neue Map:<br /><br />';
echo 'Deuteriumnebel: ',$deutnebel,'<br />';
echo 'Ceru: ',$blaunebel,'<br />';
echo 'Metaphasen: ',$greennebel,'<br /><br />';
echo 'Klasse M Planeten: ',$planet1,'<br />';
echo 'Klasse Lava Planeten: ',$planet2,'<br />';
echo 'Klasse Ice Planeten: ',$planet3,'<br />';
echo 'Klasse Wuste Planeten: ',$planet4,'<br />';


?>