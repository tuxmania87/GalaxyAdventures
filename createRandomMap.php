<?php
include_once("connect.php");
define ("MAXMEM", 32*1024*1024);  //--- memory limit (32M) ---

$dimension = $argv[1];
$density = $argv[2];

$numCenterPoints = (int) (pow($dimension,2) *0.1);

$centerPoints = array();

$types = array(
1,
3, 
4, 
5, 
6, 
7, 
8, 
);

$density = $density / 100.0;

$fillUpCounter = count($types) / $density - count($types);

for($i=0; $i<$fillUpCounter; $i++ ) {
	$types[] = 0;
}


echo "Number of CenterPoints: $numCenterPoints\n";

mysqli_query($verbindung, "TRUNCATE TABLE weltraum");

// for each center Point determine Position
for($i=0; $i<$numCenterPoints; $i++) {

	//determine type
	$randomType = $types[rand(1,count($types)-1)];
	//determine position
	$_p = array();
	$_p["x"] = rand(1,$dimension);
	$_p["y"] = rand(1,$dimension);
	$_p["type"] = $randomType;

	echo "CenterPoint (".$_p["x"]."|".$_p["y"].") Type: ".$_p["type"]."\n";

	$centerPoints[] = $_p;
	echo "$i\n";
}

echo "create map\n";
// for each non center point assign type
for($x = 1; $x <= $dimension; $x++) {
	for($y = 1; $y <= $dimension; $y++) {
		//echo "X:$x Y:$y\n";
		//search closest center point
		$distance = sqrt(pow($dimension,2) * 2);
		$distance_i = 0;

		$isCenterPoint = false;

		for($i = 0; $i < count($centerPoints); $i++) {

			if($x == $centerPoints[$i]["x"] && $y == $centerPoints[$i]["y"]) {
				//$isCenterPoint = true;
			}

			$_d = sqrt(pow( abs($x-$centerPoints[$i]["x"]),2 ) + pow(abs($y - $centerPoints[$i]["y"]),2) );
			if($_d < $distance) {
				$distance = $_d;
				$distance_i = $i;
			}
		}

		if(!$isCenterPoint && $centerPoints[$distance_i]["type"] !== 0 && $distance <10) {
			mysqli_query($verbindung, "INSERT INTO weltraum (x,y,system,typ,zielx,ziely,zielsystem) VALUES ($x,$y,0,'".$centerPoints[$distance_i]["type"]."',0,0,0)");
		}
		//  INSERT INTO weltraum (x,y,system,typ,zielx,ziely,zielsystem) 
		//  VALUES ($x,$y,0,'$centerPoints["type"]',0,0,0)

	}
}


