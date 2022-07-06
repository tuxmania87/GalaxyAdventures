<?php
$file=$_GET["img"];
list($width, $height) = getimagesize($file) ;
echo 'Breite: ',$width;
?>
