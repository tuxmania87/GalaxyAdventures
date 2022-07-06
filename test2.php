<?php

class test {
public $zahl;
}

$a = new test();
$a->zahl = new test();
$a->zahl->zahl = 2;
$b = $a->zahl;

$a->zahl->zahl = 3;
echo $b->zahl;
?>
