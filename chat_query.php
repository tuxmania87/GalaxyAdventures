<?php
include_once("connect.php");
session_start();
include("klassen.php");

echo '<table>';

$abfrage=mysql_query("SELECT * FROM chat WHERE id > ((SELECT MAX(id) FROM chat)-15) ORDER BY id ASC LIMIT 15");
while($row=mysql_fetch_array($abfrage))
	{
	$arg1="";
	$arg2="";
	$text=pruefetext($row["nachricht"]);
	$usr=new Account($row["uid"]);
	if($usr->id==9) { $arg1="color:red;"; $arg2="font-weight:bold;"; }
	if($usr->id==1) { $arg1="color:#00C0FF;"; }
	if($usr->id=='' || $usr->id==0) $usr->nickname=$row["uid"];
	echo '<tr><td style="width:135px;">',gerdatum($row["zeit"]),'</td><td style="min-width:140px;"><span style="',$arg1,$arg2,'">',$usr->nickname,'</span></td><td><span style="',$arg1,$arg2,'">',$text,'</span></td></tr>';
	}
echo '</table>';

//mysql_query("UPDATE account SET aktion='".date("Y-m-d H:i:s")."' WHERE id='".$_SESSION["Id"]."'") or die(mysql_error());
?>