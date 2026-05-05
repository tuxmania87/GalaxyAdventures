<?php
include_once("connect.php");

echo "digraph G {\n";
echo "node [shape=box];\n";

$q = mysqli_query($verbindung, "select id, name from forschung");
while($r= mysqli_fetch_array($q)) {
	echo $r["id"]." [label=\"".$r["name"]."\"]\n";
}

$q = mysqli_query($verbindung, "select * from forschung");
while($r= mysqli_fetch_array($q)) {
	$a = explode("/",$r["pre"]);
	for($i=0;$i<count($a);$i++) {
		if(ctype_digit($a[$i]))
			echo $a[$i]." -> ".$r["id"]."\n";
	}
}

echo "}";
