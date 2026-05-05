<?php
include("klassen.php");

$q = mysqli_query($verbindung, "select * from planet2 where id=".$argv[1]);
echo "update planet2 set ";
while($r = mysqli_fetch_array($q)) {
	for($i=1;$i<=50;$i++) {
		echo "feld".$i."='".$r["feld".$i];
		if($i!=50)
			echo "',";
	}
}
echo "' where id in (select id from planeten where typ='".$argv[2]."');"
?>

