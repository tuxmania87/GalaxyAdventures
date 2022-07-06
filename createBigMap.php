<?php
define ("MAXMEM", 32*1024*1024);  //--- memory limit (32M) ---

$abschnitt = $argv[1];
$dim = $argv[2];

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
$startx =  ($abschnitt % ($dim / 20)) * 20;
$starty =  floor( ($abschnitt/ ($dim/ 20))) * 20;


$img = imagecreatetruecolor(20*34, 20*34);

for($i=$startx;$i<$startx+20;$i++) {
    for($j=$starty;$j<$starty+20;$j++) {
        $f = new Weltraum($i, $j, 0, false);
        $split = explode(".",$f->bild);
        
        if(strtolower($split[sizeof($split)-1]) == "png") {
            $piece = imagecreatefrompng("images/".$f->bild);
        }
        if(strtolower($split[sizeof($split)-1]) == "jpg") {
            $piece = imagecreatefromjpeg("images/".$f->bild);
        }
        
        // Draw border
        $farbe = ImageColorAllocate($piece, 160, 0, 0); 
        drawBorder($piece,$farbe);
        
        //var_dump($piece);
        
        imagecopy($img,$piece,$i*34-$startx*34,$j*34-$starty*34,0,0,34,34);
        
    }
}

imagepng($img);
imagedestroy($img);
?>
