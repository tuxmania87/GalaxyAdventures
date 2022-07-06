<?php
include_once("connect.php");

echo "digraph G {\n";
echo "node [shape=box];\n";

$q = mysql_query("select id, name from forschung");
while($r= mysql_fetch_array($q)) {
	echo $r["id"]." [label=\"".$r["name"]."\"]\n";
}

$q = mysql_query("select * from forschung");
while($r= mysql_fetch_array($q)) {
	$a = explode("/",$r["pre"]);
	for($i=0;$i<sizeof($a);$i++) {
		if(ctype_digit($a[$i]))
			echo $a[$i]." -> ".$r["id"]."\n";
	}
}

echo "}";
