<?php

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

echo '<table>';

for($i=0;$i<sizeof($systeme);$i++)
echo '<tr><td><img src="',$systeme[$i],'" border="0" /></td><td>',$code[$i],'</td></tr>';

$bilder=array("nebel.jpg","erz.jpg","green.jpg","deut.jpg","schwarzesloch.jpg","pulsar.jpg","limes.jpg");
for($i=0;$i<sizeof($bilder);$i++)
echo '<tr><td><img src="',$bilder[$i],'" border="0" /></td><td>',$i+1,'</td></tr>';

echo '</table>';