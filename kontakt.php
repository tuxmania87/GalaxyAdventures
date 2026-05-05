<?php
include("head.php");
if($_SESSION["Id"]>0)
include("navlogged.php");
else
include("nav.php");
?>
 <h2>Kontakt</h2>
 <br /><br />
 Falls Probleme technischer Art anstehen oder Anfragen bez&uuml;glich irgendwelcher organisatorischen Probleme, bin ich unter folgenden Daten zu erreichen.<br /><br />
 <ul>
 <li>Email: <a href="mailto:" . (defined('GA_ADMIN_EMAIL') ? GA_ADMIN_EMAIL : 'admin@example.com') . "">" . (defined('GA_ADMIN_EMAIL') ? GA_ADMIN_EMAIL : 'admin@example.com') . "</a>
 <li>IRC: <a href="irc://de.quakenet.org:6667/galaxy-adventures">im Quakenet unter #galaxy-adventures</a></li>  Idler erw&uuml;nscht :)
 </ul>
<br />
<br />
<h3>Impressum</h3><br />
Robert Hartmann<br />
Bernburgerstrasse. 18<br />
06108 Halle/Saale<br />
<br />

E-Mail: <a href="mailto:" . (defined('GA_ADMIN_EMAIL') ? GA_ADMIN_EMAIL : 'admin@example.com') . "">" . (defined('GA_ADMIN_EMAIL') ? GA_ADMIN_EMAIL : 'admin@example.com') . "</a><br />
Internet: <a href="" . (defined('GA_BASE_URL') ? GA_BASE_URL : '.') . "/
">" . (defined('GA_BASE_URL') ? GA_BASE_URL : '.') . "/
</a><br /><br />
Haftungshinweis: Trotz sorgf&auml;ltiger inhaltlicher Kontrolle &uuml;bernehmen wir keine Haftung f&uuml;r die Inhalte externer Links. F&uuml;r den Inhalt der verlinkten Seiten sind ausschlie&szlig;lich deren Betreiber verantwortlich.<br /><br />
<?php
include("foot.php");
?>
