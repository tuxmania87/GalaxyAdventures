<?php
include_once("connect.php");


session_start();
include("klassen.php");

$hash=$_GET["hash"];
$sesid=$_SESSION["Id"]==''||$_SESSION["Id"]==0?$_SESSION["nick"]:$_SESSION["Id"];
mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$hash','".$_SERVER["REMOTE_ADDR"]."','".$sesid."')") or die(mysql_error());
mysql_query("UPDATE account SET aktion='".date("Y-m-d H:i:s")."' WHERE id='".$_SESSION["Id"]."'");

//HASH abfrage
if(substr($hash,0,5)=="!seen")
	if(ctype_digit(substr($hash,6,strlen($hash)-6)))
		{
		$usr=new Account(substr($hash,6,strlen($hash)-6));
		$msg='User [Spieler:'.$usr->id.'] war zuletzt online am: '.gerdatum($usr->aktion);
		mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$msg','".$_SERVER["REMOTE_ADDR"]."','9')") or die(mysql_error());
		}
	else
	{
	$msg='Falscher Syntax: !seen <ID des Users>';
	mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$msg','".$_SERVER["REMOTE_ADDR"]."','9')") or die(mysql_error());
	}

if(substr($hash,0,8)=="!wpunkte")
	if(ctype_digit(substr($hash,9,strlen($hash)-9)))
		{
		$usr=new Account(substr($hash,9,strlen($hash)-9));
		$msg='User [Spieler:'.$usr->id.'] hat '.$usr->wpunkte.' Wirtschaftspunkte!';
		mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$msg','".$_SERVER["REMOTE_ADDR"]."','9')") or die(mysql_error());
		}
	else
	{
	$msg='Falscher Syntax: !wpunkte <ID des Users>';
	mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$msg','".$_SERVER["REMOTE_ADDR"]."','9')") or die(mysql_error());
	}

	
if(substr($hash,0,7)=="!gacode")	
	{
	$msg='Bitte verwende statt HTML Code ->  Gacode: [link:http://www.galaxy-adventures.net/de/gacode.htm]';
	mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$msg','".$_SERVER["REMOTE_ADDR"]."','9')") or die(mysql_error());
	}
	
if(substr($hash,0,5)=="!help")	
	{
	$msg='folgende Kommandos sind verfuegbar: ';
	mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$msg','".$_SERVER["REMOTE_ADDR"]."','9')") or die(mysql_error());
	$msg='!seen ID - Zeigt an wann User mit Nummer ID zuletzt online war.';
	mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$msg','".$_SERVER["REMOTE_ADDR"]."','9')") or die(mysql_error());
	$msg='!wpunkte ID - Zeigt die Wirtschaftspunkte fuer User ID an.';
	mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$msg','".$_SERVER["REMOTE_ADDR"]."','9')") or die(mysql_error());
	$msg='!gacode - Hinweise zur Textformatierung.';
	mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$msg','".$_SERVER["REMOTE_ADDR"]."','9')") or die(mysql_error());
	$msg='!help - Diese Seite.';
	mysql_query("INSERT INTO chat (zeit,nachricht,ip,uid) VALUES ('".date("Y-m-d H:i:s")."','$msg','".$_SERVER["REMOTE_ADDR"]."','9')") or die(mysql_error());
	
	
	}
?>



