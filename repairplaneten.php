<?php
include("klassen.php");


$q = mysql_query("select * from planet2");
while($r = mysql_fetch_array($q)) {
      for($i=1;$i<51;$i++) {
              if(strpos($r["feld".$i], "-") === FALSE) {
			$e = explode("/",$r["feld".$i]);
			for($j=0;$j<sizeof($e);$j++) {
				if($e[$j] == '')
					$e[$j] = 0;
				
			}
			$implode = implode("/",$e);
			mysql_query("update planet2 set feld".$i."='".$implode."' where id = ".$r["id"]);
              }
      }
}

?>

