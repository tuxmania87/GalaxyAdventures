<?php
session_start();
$_SESSION=array();
// Löschen aller Session-Variablen.
session_unset();
// Zum Schluss Löschen der Session.
session_destroy();
echo '<meta http-equiv="Refresh" content="0; url=index.php">';
?>
