<?php


/* ======================
Variablen
====================== */
$NICK = 'GATe';
$IDENT = 'GATe';
$CHANNEL = '#galaxy-adventures';


/* ======================
Funktionen
====================== */
function tellIRC($input) {
global $fp;
fputs($fp, $input."\r\n\r\n");
}

function pong($id,$mess1) {

global $CHANNEL, $fp, $firstPING;
fputs($fp, "PONG ".$id."\r\n\r\n");

if ($firstPING <= 1) {
fputs($fp, "JOIN $CHANNEL\r\n\r\n");
fputs($fp, "PRIVMSG $CHANNEL :Dies ist ein Test:\r\n\r\n");
fputs($fp, "PRIVMSG $CHANNEL :Die 2. Zeile die gepostet werden soll!!!\r\n\r\n");
tellIRC("QUIT Visit www.meine-homepage.de");
}

}


/* ======================
Conneciton aufbauen
====================== */
$fp = fsockopen ("irc.de.quakenet.org", 6667, $errno, $errstr, 30);
if (!$fp) {

die("$errstr ($errno)
\n");

}

tellIRC("USER $NICK $NICK $NICK :$NICK");
tellIRC("NICK $NICK $IDENT");


/* ======================
Auslesen
====================== */
$firstPING = 0;
while (!feof($fp)&&!$eof) {

$msg = str_replace("\n", "", str_replace("\r", "", fgets ($fp,2048)));
if (strtoupper(substr($msg, 0, 4)) == 'PING') {

$firstPING++;
$pID = substr($msg, 6);
//test


$mess1='AA';
pong($pID,$mess1);


} else if (substr($msg, -8) == 'c4m quit') {

tellIRC("QUIT :Visit www.meine-homepage.de");

}
echo $msg,'<br />';
}
fclose($fp);

?>