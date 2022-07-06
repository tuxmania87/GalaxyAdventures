<?php
define ("MAXMEM", 32*1024*1024);  //--- memory limit (32M) ---

$dim = $argv[1];

function drawBorder(&$img, &$color, $thickness = 1)
{
    $x1 = 0;
    $y1 = 0;
    $x2 = ImageSX($img) - 1;
    $y2 = ImageSY($img) - 1;

    for($i = 0; $i < $thickness; $i++)
    {
        ImageRectangle($img, $x1++, $y1++, $x2--, $y2--, $color);
    }
} 

header('Content-Type: image/png');
include("klassen.php");

$img = imagecreatetruecolor(680*5, 680*5);

for($i=0;$i<$dim/20;$i++) {
    for($j=0;$j<$dim/20;$j++) {
        $it = $j*$dim/20 + $i;
        echo $it."\n";
        $piece = imagecreatefrompng("minimap/bild".$it.".png");
        // Draw border
        //var_dump($piece);
        
        imagecopy($img,$piece,$i*680,$j*680,0,0,680,680);
        
    }
}

imagepng($img);
imagedestroy($img);
?>
