<?php
include("klassen.php");
$id = $_GET["id"];

if(ctype_digit($id)) {
    $ac = new Account($id);
    echo $ac->nickname;
}

?>
