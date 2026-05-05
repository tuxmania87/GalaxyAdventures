<?php
include("klassen.php");

$verbindung = get_verbindung();

function getpass() {
    $abc = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    $pwd = "";

    for ($i = 0; $i <= 8; $i++) {
        $bool = rand(1, 2);
        if ($bool == 1) {
            $zahl = rand(1, 24);
            $pwd .= $abc[$zahl];
        }
        if ($bool == 2) {
            $zahl = rand(1, 9);
            $pwd .= $zahl;
        }
    }
    return($pwd);
}

//Begin
if ($_POST["sent"] == 1 && $_POST["einverstanden"] == 'on') {
    $accname = $_POST["name"];
    $accemail = $_POST["email"];
    changeit($accname);
    changeit($accemail);
    echo '!!ACCMAIL: ', $accemail;
    $existName = 0;
    $abfrage = mysqli_query($verbindung, "SELECT * FROM account WHERE login='$accname' OR email='$accemail'");
    while ($account = mysqli_fetch_array($abfrage)) {
        $existName = 1;
    }
    if ($existName == 0) {
        $accpasswort = getpass();
        echo 'Erfolgreich registriert, Passwort wird zugesendet';
        $message = "Du wurdest erfolgreich registriert!\n\n Dein Name lautet: $accname \n Dein Passwort lautet: $accpasswort .\n\n Link zum einloggen: " . (defined('GA_BASE_URL') ? GA_BASE_URL : '.') . "/
 \n\n sollte es Probleme geben wende dich an: den Administrator \n Das GA - Team";
        mail($accemail, "Willkommen beim Browser Game - Galaxy Adventures!", $message, "From: GA-Registrierung <" . (defined('GA_MAIL_FROM') ? GA_MAIL_FROM : 'noreply@example.com') . ">");
        echo '<a href="login.php">zum login</a>';
        $pass = $accpasswort;
        $accpasswort = md5($accpasswort);
        $ip = $_SERVER['REMOTE_ADDR'];
        mysqli_query($verbindung, 
            "INSERT INTO `account` (`login`,email,passwort,beschreibung,regdatum,regip) 
                VALUES 
                ('$accname' , '$accemail' , '$accpasswort','', CURRENT_TIMESTAMP,'$ip')"
                )  OR die($verbindung->error);
    }
}
        include("nav2.php");
        ?>
<table width="750" border="0" valign="top" align="middle">
    <tr><td align="middle"><b>Registrieren</b></td></tr>
    <tr><td bgcolor="#300000">&nbsp;</td></tr>
    <tr><td align="middle">

            <br />
            <form action="register.php" method="post" name="agb">
                <table>
                    <tr><td>Name:</td><td><input type="text" name="name"></td></tr>
                    <tr><td>email-Adresse:</td><td><input type="text" name="email"</td></tr>
                </table>
                Der Account wird nicht nur f&uuml;r das Spiel erstellt. Gleichzeitig wird ein Account im <a href="#"><b><font color="yellow"> >Forum< </font></b></a> eingerichtet und ein Account im <a href="#"><b><font color="yellow"> >Bugtracker< </font></b></a> indem ihr Bugs melden k&ouml;nnt.<br /><br />
                Hebt desshalb eure Accountinformationen gut auf. Wenn man sein Passwort im Spiel &auml;ndert, &auml;ndert es sich <b>NICHT</b> gleichzeitig im Forum oder Bugtracker.<br /><br />
                Im folgenden die AGB's des Spiels:<br />
                <input type="hidden" name="sent" value="1">
                <textarea rows="8" cols="75" readonly="readonly" name="agbtext">

DIE AGB's / Regeln

&sect;1   Die Teilnahme an Galaxy - Adventures (nachfolgend "GA" genannt) ist und bleibt kostenfrei.

&sect;2   F&uuml;r die GA-Mitgliedschaft besteht weder eine Mindestvertragslaufzeit noch eine K&uuml;ndigungsfrist.

&sect;3   GA garantiert keine 100% Ausfallsicherheit des Spiels.

&sect;4   GA &uuml;bernimmt keine Haftung f&uuml;r Sch&auml;den, die durch die Nutzung von GA entstanden sind.

&sect;5   GA beh&auml;lt sich das Recht vor, bei Versto&szlig; gegen die AGBs oder geltende Gesetze den Account fristlos und ohne Benachrichtigung zu l&ouml;schen.

&sect;6   Als Inhaber eines Accounts wird die Person angesehen, die den Account angemeldet hat; Eigent&uuml;mer bleibt jedoch GA. Dem Inhaber ist es nicht gestattet seinen Account zu verkaufen oder versteigern.

&sect;7   Die Verantwortung f&uuml;r einen Account tr&auml;gt ausschlie&szlig;lich der Inhaber. Account-Sitting ist nur nach der Bekanntgabe im Forum gestattet. Konsequenzen, die aus ggf. auftretenden Missbrauch entstehen, tr&auml;gt der Inhaber ersatzlos.

&sect;8   Jedes Mitglied darf nur einen Account besitzen. Mehrfach-Accounts ("Multis") werden gesperrt oder respektlos gel&ouml;scht.

&sect;9   Spieler, die sich eine Internetverbindung teilen oder gr&ouml;&szlig;tenteils am selben Computer GA spielen (aus div. Gr&uuml;nden), sollen sich bitte beim Admin melden, " . (defined('GA_ADMIN_EMAIL') ? GA_ADMIN_EMAIL : 'admin@example.com') . ". Jene Angaben sind freiwillig, vors&auml;tzlich gemachte Falschangaben k&ouml;nnen jedoch zur L&ouml;schung des Accounts f&uuml;hren. Die Daten werden nicht an Dritte weitergegeben.

&sect;10  Auf alle spielinternen Verluste, die durch serverseitige Fehler entstanden sind, besteht <u>kein</u> Anrecht auf Wiedergutschrift. Derartige Fehler d&uuml;rfen nicht missbraucht werden und sind unverz&uuml;glich per E-Mail oder im Forum zu melden.

&sect;11  GA ist eine Plattform, &uuml;ber die die Mitglieder untereinander, z.B. durch Nachrichten, kommunizieren k&ouml;nnen. F&uuml;r den Inhalt sind die Mitglieder ausschlie&szlig;lich selbst verantwortlich. Pornographische, rassistische, beleidigende oder gegen geltendes Recht versto&szlig;ende Inhalte k&ouml;nnen zur Sperrung oder L&ouml;schung des Accounts f&uuml;hren.

&sect;12  Es ist verboten jegliche Seiten von GA mit anderen Programmen oder Scripts au&szlig;er dem Internet-Browser aufzurufen, dies bezieht sich insbesondere auf sogenannte Bots oder andere Tools, die Vorg&auml;nge im Spiel (teilweise) automatisieren oder den Server attackieren. Desweiteren ist es verboten GA &uuml;ber sog. "Anonymizer"-Programme oder -Proxies aufzurufen.

&sect;13  Das Mitglied erkl&auml;rt sich damit einverstanden, in unregelm&auml;&szlig;igen Abst&auml;nden &uuml;ber wichtige &auml;nderungen, Erweiterungen, Neuigkeiten o.&auml;. per E-Mail informiert zu werden.

&sect;14  Es gelten immer die allgemeinen Gesch&auml;ftsbedingungen in der jeweils neuesten Fassung, jeder Benutzer wird &uuml;ber &auml;nderungen per E-Mail informiert und eine Frist von 14 Tagen zur Ablehnung einger&auml;umt.

&sect;15  Es gilt deutsches Recht. Als Gerichtsstand wird Halle/Saale vereinbart.

&sect;16  Sollte einer der hier aufgef&uuml;hrten Paragraphen gegen geltendes Recht versto&szlig;en, so bleiben die Anderen jedoch unber&uuml;hrt dessen.
                </textarea><br />
                <br />Nun die Regeln des Forums ( alle Rechte bleiben bei www.phpbb.com )<br /><br />
                <textarea rows="8" cols="75" readonly="readonly" name="agbtext2">
Mit dem Zugriff auf �Star Trek - Galaxy Adventures II - Forum� wird zwischen dir und dem Betreiber ein Vertrag mit folgenden Regelungen geschlossen:
1. Nutzungsvertrag

   1. Mit dem Zugriff auf Star Trek - Galaxy Adventures II - Forum  schliesst du einen Nutzungsvertrag mit dem Betreiber des Boards ab (im Folgenden �Betreiber�) und erkl&auml;rst dich mit den nachfolgenden Regelungen einverstanden.
   2. Wenn du mit diesen Regelungen nicht einverstanden bist, so darfst du das Board nicht weiter nutzen. F&uuml;r die Nutzung des Boards gelten jeweils die an dieser Stelle ver&ouml;ffentlichten Regelungen.
   3. Der Nutzungsvertrag wird auf unbestimmte Zeit geschlossen und kann von beiden Seiten ohne Einhaltung einer Frist jederzeit gek&uuml;ndigt werden.

2. Einr&auml;umung von Nutzungsrechten

   1. Mit dem Erstellen eines Beitrags erteilst du dem Betreiber ein einfaches, zeitlich und r&auml;umlich unbeschr&auml;nktes und unentgeltliches Recht, deinen Beitrag im Rahmen des Boards zu nutzen.
   2. Das Nutzungsrecht nach Punkt 2, Unterpunkt a bleibt auch nach K&uuml;ndigung des Nutzungsvertrages bestehen.

3. Pflichten des Nutzers

   1. Du erkl&auml;rst mit der Erstellung eines Beitrags, dass er keine Inhalte enth&auml;lt, die gegen geltendes Recht oder die guten Sitten verstossen. Du erkl&auml;rst insbesondere, dass du das Recht besitzt, die in deinen Beitr&auml;gen verwendeten Links und Bilder zu setzen bzw. zu verwenden.
   2. Der Betreiber des Boards &uuml;bt das Hausrecht aus. Bei Verst&ouml;ssen gegen diese Nutzungsbedingungen oder anderer im Board ver&ouml;ffentlichten Regeln kann der Betreiber dich nach Abmahnung zeitweise oder dauerhaft von der Nutzung dieses Boards ausschliessen und dir ein Hausverbot erteilen.
   3. Du nimmst zur Kenntnis, dass der Betreiber keine Verantwortung f&uuml;r die Inhalte von Beitr&auml;gen &uuml;bernimmt, die er nicht selbst erstellt hat oder die er zur Kenntnis genommen hat. Du gestattest dem Betreiber, dein Benutzerkonto, Beitr&auml;ge und Funktionen jederzeit zu l&ouml;schen oder zu sperren.
   4. Du gestattest dem Betreiber dar&uuml;ber hinaus, deine Beitr&auml;ge abzu&auml;ndern, sofern sie gegen o. g. Regeln verstossen oder geeignet sind, dem Betreiber oder einem Dritten Schaden zuzuf&uuml;gen.

4. General Public License

   1. Du nimmst zur Kenntnis, dass es sich bei phpBB um eine unter der General Public License (GPL) bereitgestellten Foren-Software der phpBB Group (www.phpbb.com) handelt; deutschsprachige Informationen werden durch die deutschsprachige Community unter www.phpbb.de zur Verf&uuml;gung gestellt. Beide haben keinen Einfluss auf die Art und Weise, wie die Software verwendet wird. Sie k&ouml;nnen insbesondere die Verwendung der Software f&uuml;r bestimmte Zwecke nicht untersagen oder auf Inhalte fremder Foren Einfluss nehmen.

5. Gew&auml;hrleistung

   1. Der Betreiber haftet mit Ausnahme der Verletzung von Leben, K&ouml;rper und Gesundheit und der Verletzung wesentlicher Vertragspflichten (Kardinalpflichten) nur f&uuml;r Sch&auml;den, die auf einem vors&auml;tzlichen oder grob fahrl&auml;ssigen Verhalten zur&uuml;ckzuf&uuml;hren sind. Dies gilt auch f&uuml;r mittelbare Folgesch&auml;den wie insbesondere entgangenen Gewinn.
   2. Die Haftung ist gegen &uuml;ber Verbrauchern ausser bei vors&auml;tzlichen oder grob fahrl&auml;ssigen Verhalten oder bei Sch&auml;den aus der Verletzung von Leben, K&ouml;rper und Gesundheit und der Verletzung wesentlicher Vertragspflichten (Kardinalpflichten) auf die bei Vertragsschluss typischer Weise vorhersehbaren Sch&auml;den und im &uuml;brigen der H&ouml;he nach auf die vertragstypischen Durchschnittssch&auml;den begrenzt. Dies gilt auch f&uuml;r mittelbare Folgesch&auml;den wie insbesondere entgangenen Gewinn.
   3. Die Haftung ist gegen&uuml;ber Unternehmern ausser bei der Verletzung von Leben, K&ouml;rper und Gesundheit oder vors&auml;tzlichen oder grob fahrl&auml;ssigen Verhalten des Boards auf die bei Vertragsschluss typischer Weise vorhersehbaren Sch&auml;den und im &uuml;brigen der H&ouml;he nach auf die vertragstypischen Durchschnittssch&auml;den begrenzt. Dies gilt auch f&uuml;r mittelbare Sch&auml;den, insbesondere entgangenen Gewinn.
   4. Die Haftungsbegrenzung der Abs&auml;tze 1 bis 3 gilt sinngem&auml;ss auch zugunsten der Mitarbeiter und Erf&uuml;llungsgehilfen des Betreibers.
   5. Anspr&uuml;che f&uuml;r eine Haftung aus zwingendem nationalem Recht bleiben unber&uuml;hrt.

6. &auml;nderungsvorbehalt

   1. Der Betreiber ist berechtigt, die Nutzungsbedingungen und die Datenschutzrichtlinie zu &auml;ndern. Die &auml;nderung wird dem Nutzer per E-Mail mitgeteilt.
   2. Der Nutzer ist berechtigt, den &auml;nderungen zu widersprechen. Im Falle des Widerspruchs erlischt das zwischen dem Betreiber und dem Nutzer bestehende Vertragsverh&auml;ltnis mit sofortiger Wirkung.
   3. Die &auml;nderungen gelten als anerkannt und verbindlich, wenn der Nutzer den &auml;nderungen zugestimmt hat.

Informationen &uuml;ber den Umgang mit deinen pers&ouml;nlichen Daten sind in der Datenschutzrichtlinie enthalten.
                </textarea>

                <br /><input type="checkbox" name="einverstanden" onclick="document.agb.sentknopf.disabled=document.agb.sentknopf.disabled==true?false:true;" checked="true">&nbsp;&nbsp;Hiermit best&auml;tige ich, dass ich die AGB gelesen und verstanden habe. Ich erkl&auml;re mich einverstanden mit diesen.
                <br /><input type="submit" value="registrieren" name="sentknopf">
            </form>
        </td></tr>
</table>


<br>
<br>
<br>

</center>
</body>
</html>
