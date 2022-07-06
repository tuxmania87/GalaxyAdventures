<?php
include_once("connect.php");

echo '<table>';
$systeme=array("bblaublau","bblaugelb","bblauorange","bblaurot","bblauschwarz","bblauweiss","blau","blaubig","blaublau","brotblau","brotgelb","brotorange","brotrot","brotschwarz","brotweiss","gelb","gelbblau","gelbgelb","gelbweiss","orange","orangegelb","orangeorange","orangeweiss","rot","rotbig","rotblau","rotgelb","rotorange","rotrot","rotweiss","weiss","weissblau");
for($i=0;$i<sizeof($systeme);$i++)
	$systeme[$i]=$systeme[$i].".jpg";
$code=array(0=>"A",
		    1=>"B",
			2=>"C",
			3=>"D",
			4=>"E",
			5=>"F",
			6=>"G",
			7=>"H",
			8=>"I",
			9=>"J",
			10=>"K",
			11=>"L",
			12=>"M",
			13=>"N",
			14=>"O",
			15=>"P",
			16=>"Q",
			17=>"R",
			18=>"S",
			19=>"T",
			20=>"U",
			21=>"V",
			22=>"W",
			23=>"X",
			24=>"Y",
			25=>"a",
			26=>"b",
			27=>"c",
			28=>"d",
			29=>"e",
			30=>"f",
			31=>"g",
			32=>"h",
			33=>"i",
			34=>"j");

for($y=1;$y<=120;$y++)
for($x=1;$x<=120;$x++)
{
//if($x==$getx-10) echo '<tr><td>',$y,'</td>';


$checked="";
$ttip="";
$done=false;

	$abfrage=mysql_query("SELECT * FROM systeme WHERE x='$x' AND y='$y'");
	while($row=mysql_fetch_array($abfrage))
	{
	echo '<td>',$code[array_search($row["bild"],$systeme)],'</td>';
	$done=true;
	}
	
	$abfrage=mysql_query("SELECT * FROM weltraum WHERE system='".$system->id."' AND x='$x' AND y='$y'");
	while($row=mysql_fetch_array($abfrage))
	{
	if($row["typ"]=='b') echo '<td>1</td>';
	if($row["typ"]=='e') echo '<td>2</td>';
	if($row["typ"]=='g') echo '<td>3</td>';
	if($row["typ"]=='d') echo '<td>4</td>';
	if($row["typ"]=='x') echo '<td>5</td>';
	if($row["typ"]=='p') echo '<td>6</td>';
	if($row["typ"]=='lim') echo '<td>7</td>';
	
	$done=true;
	}
	if(!$done) echo '<td>0</td>';

echo '</td>';
if($x==120) echo '</tr>';
}

echo '</table>';