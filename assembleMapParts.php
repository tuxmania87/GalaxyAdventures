<?php

define('MAXMEM', 32 * 1024 * 1024);  // --- memory limit (32M) ---

// dim of the image is 32 + 2 pixels for border
// (left and right, up and down)
$IMAGE_DIMENSION = 34;

// Number of image tiles, fixed at 20 images
// so output is 20 images high and wide
$TILE_DIMENSION = 20;

$TILE_DIMENSION_IN_PIXEL = $IMAGE_DIMENSION * $TILE_DIMENSION;

$dim = $argv[1];
$outname = $argv[2];

function drawBorder(&$img, &$color, $thickness = 1)
{
    $x1 = 0;
    $y1 = 0;
    $x2 = imagesx($img) - 1;
    $y2 = imagesy($img) - 1;

    for ($i = 0; $i < $thickness; ++$i) {
        imagerectangle($img, $x1++, $y1++, $x2--, $y2--, $color);
    }
}

header('Content-Type: image/png');
include 'klassen.php';

$img = imagecreatetruecolor($TILE_DIMENSION_IN_PIXEL * ($dim / $TILE_DIMENSION), $TILE_DIMENSION_IN_PIXEL * ($dim / $TILE_DIMENSION));

for ($i = 0; $i < $dim / $TILE_DIMENSION; ++$i) {
    for ($j = 0; $j < $dim / $TILE_DIMENSION; ++$j) {
        $it = $j * $dim / $TILE_DIMENSION + $i;
        echo $it."\n";
        $piece = imagecreatefrompng('minimap/bild'.$it.'.png');
        // Draw border
        // var_dump($piece);

        imagecopy($img, $piece, $i * $TILE_DIMENSION_IN_PIXEL, $j * $TILE_DIMENSION_IN_PIXEL, 0, 0, $TILE_DIMENSION_IN_PIXEL, $TILE_DIMENSION_IN_PIXEL);
    }
}

imagepng($img, $outname);
imagedestroy($img);
