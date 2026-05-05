<?php

include("head.php");
include("navlogged.php");
include("klassen.php");

//CHEATSCHUTZ ANFANG
//CHEATSCHUTZ ENDE

$id = $_SESSION["Id"];

$verbindung = get_verbindung();

$check = false;
$ii = mysqli_query($verbindung, "SELECT * FROM konto WHERE besitzer='$id'");
while ($row = mysqli_fetch_array($ii))
    $check = true;
if (!$check)
    mysqli_query($verbindung, "INSERT INTO konto (besitzer) VALUES ('$id')");

$account = new Account($id);

$checkmail = false;
$postquery2 = mysqli_query($verbindung, "SELECT * FROM mail WHERE empfaenger='$id' AND neu=1 AND del=0"); //id einsetzen
while ($account2 = mysqli_fetch_array($postquery2))    //Abfrage der accountdaten
    $checkmail = true;

/* $checklog=false;
  $logquery2=mysqli_query($verbindung, "SELECT * FROM logbuch WHERE wen='$id' AND neu=1"); //id einsetzen
  while($account3=mysqli_fetch_array($logquery2)) 			//Abfrage der accountdaten
  $checklog=true;
 */
$checkvertrag = false;
$logquery2 = mysqli_query($verbindung, "SELECT * FROM vertrag WHERE partner='$id' AND valid=0"); //id einsetzen
while ($account3 = mysqli_fetch_array($logquery2))    //Abfrage der accountdaten
    $checkvertrag = true;

echo 'Willkommen ' . $account->nickname . '. ';
echo ' ( Stufe: ', $account->level, ' ),<br /><br />';


if ($checkmail)
    echo '<a href="mail.php"><div><table><tr><td><img src="mail32.png" border="0" /></td><td>Du hast <span style="color:green">neue</span> Nachrichten!</td></tr></table></div></a>';
//log
if ($checklog)
    echo '<a href="logbuch.php"><div><table><tr><td><img src="log32.png" border="0" /></td><td>Du hast <span style="color:green">neue</span> Logbucheintr&auml;ge!</td></tr></table></div></a>';
//vertrag
if ($checkvertrag)
    echo '<a href="vertrag.php"><div><table><tr><td><img src="vertrag32.png" border="0" /></td><td>Du hast <span style="color:green">neue</span> Vertragsvorschl&auml;ge!</td></tr></table></div></a>';

//ENDE BETA HINEWEIS
if ($account->chat == 1) {
    echo '<div id="chatid" style="border:2px solid #FFFFFF;width:900px;"></div>';
    echo '<br /><form name="form1" onsubmit="return false;"><div style="border:2px solid #FFFFFF;width:900px;"><input type="text" name="sendchatform" id="sendchatform" size="80" onkeydown="kdown(event);" />&nbsp;';

    $bu = new Button("chat_history.php", "Verlauf");
    $bu->printme();
    echo '</div></form>';
    $bu = new Button("option.php", "Chat ausblenden");
    $bu->printme();
    echo '<br /><br />';
}
else
    echo '<a href="option.php">Chat einblenden</a><br /><br />';
echo '<h3>wichtige Links</h3>';
$bu=new Button("gacode.htm", "GA-Code (Textformatierung)");
$bu->printme(); echo '<br />';
$bu=new Button("quest.php?sid=0", "Quests");
$bu->printme(); echo '<br />';
$bu=new Button("forum/", "Forum");
$bu->printme(); echo '<br />';
$bu=new Button("bugs/", "Bugs melden");
$bu->printme(); echo '<br />';
$bu=new Button("logbuch.php", "Logbuch");
$bu->printme(); echo '<br />';

//TIP
$tipa = array();
$tipvar = mysqli_query($verbindung, "SELECT * FROM tip");
while ($tipp = mysqli_fetch_array($tipvar)) {
    $tipa[] = $tipp["text"];
}
$randvar = rand(0, (sizeof($tipa) - 1));
//echo 'Tipps: ',$tipa[$randvar],'<br />';


if (!(strpos($account->nickname, "Kolonist") === false))
    echo '<br /><span style="color:red;font-weight:bold;">Achtung!</span> Du heisst immernoch Kolonist im Spiel. Es wird empfohlen dich <a href="option.php">umzubennenen</a>, da Kolonist der Standardname ist !';


echo '<br /><br />';
$checked = false;
$abfrage = mysqli_query($verbindung, "SELECT * FROM `planeten` WHERE besitzer='$id' AND typ='m'");
while ($planet = mysqli_fetch_array($abfrage)) {
    $checked = true;
}
if (!$checked) {
    echo '<span style="color:red;font-weight:bold;">Achtung!</span> Du besitzt keinen Planeten! Bitte einen der folgenden w&auml;hlen!<br />';
    echo '<table class="invitetable" style="text-align:center;"><tr><th>Display</th><td>Name<th>System</th><th>Koordinaten des Systems</th></tr>';
    $abfrage = mysqli_query($verbindung, "SELECT planeten.id FROM planeten,systeme WHERE planeten.system=systeme.id AND systeme.x>=1000 AND systeme.y>=1000 AND planeten.besitzer='2'");
    while ($planet = mysqli_fetch_array($abfrage)) {
        $newpl = new Planeten($planet[0]);
        echo '<tr><td><a href="getPlanet.php?pid=', $newpl->id, '"><img src="images/misc/', $newpl->bild, '" border="0" /></a></td><td>noname (', $newpl->id, ')</td><td>', $newpl->position->system->name, '</td><td>', $newpl->position->system->x, '/', $newpl->position->system->y, '</td></tr>';
    }
    echo '</table>';
}
//TICK
$tm = mysqli_query($verbindung, "SELECT `start`, `end`, (`end`-`start`) as dauer FROM `ticklog` WHERE id=(SELECT max(id) FROM `ticklog`)") or die($verbindung->error);
while ($tm2 = mysqli_fetch_array($tm)) {
    $onlinecounter = 0;
    $isonlinevar = mysqli_query($verbindung, "SELECT * FROM account");
    while ($checkonline = mysqli_fetch_array($isonlinevar))
        if (isonline($checkonline["aktion"]))
            $onlinecounter++;
    if ($onlinecounter == 1)
        echo 'Es ist gerade <a href="player.php">1 Spieler</a> online!<br />';
    if ($onlinecounter > 1)
        echo 'Es sind gerade <a href="player.php">', $onlinecounter, ' Spieler</a> online!<br />';

    echo 'Dabei seit ', $account->mitglied, ' Ticks!<br />';
    echo '<br /><font color="yellow"><b>permanenter Hinweis</b></font>: Solltest du Opfer von permanenten Diebst&auml;hlen werden ( sogenanntes Farming ) wende dich bitte an das <a href="forum/">Forum - Gerichtshof </a> und bringe den permanenten Diebstahl zur Anzeige!<br />';
    echo '<br />';
    if ($account->level <= 3)
        echo '<span style="color:yellow;font-weight:bold;">Hinweis:</span> Ich empfehle dir das <a href="wiki/index.php/Tutorial">Tutorial</a> und das <a href="wiki/">Wiki</a> durchzulesen<br /><span style="color:yellow;font-weight:bold;">Hinweis:</span> Du bist die ersten 3 Level gesch&uuml;tzt, du kannst nich angegriffen werden, kannst keine Rohstoffe klauen, kannst nicht beklaut werden und nicht angreifen.<br />';
    if ($account->level <= 2)
        echo '<span style="color:yellow;font-weight:bold;">Hinweis:</span> Die ersten 2 Level haben Geb&auml;ude keine Bauzeit. Sie sind also sofort verf&uuml;gbar!<br />';
    echo '<br />';
    if ($tm2["status"] == 1)
        echo 'Tick l&auml;uft gerade .... Bitte warten!';
    else
        echo 'Letzer Tick ausgeführt am : ', gerdatum($tm2["start"]), ' (Dauer: ', $tm2["dauer"], ' Sekunden)';
}
//TICK


include("foot.php");
?>
