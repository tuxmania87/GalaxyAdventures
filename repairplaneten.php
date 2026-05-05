<?php
include("klassen.php");


$q = mysqli_query($verbindung, "select * from planet2");
while($r = mysqli_fetch_array($q)) {
      for($i=1;$i<51;$i++) {
              if(strpos($r["feld".$i], "-") === FALSE) {
			$e = explode("/",$r["feld".$i]);
			for($j=0;$j<count($e);$j++) {
				if($e[$j] == '')
					$e[$j] = 0;
				
			}
			$implode = implode("/",$e);
			mysqli_query($verbindung, "update planet2 set feld".$i."='".$implode."' where id = ".$r["id"]);
              }
      }
}

?>

