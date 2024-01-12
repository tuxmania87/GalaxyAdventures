<?php

define('MAXMEM', 32 * 1024 * 1024);  // --- memory limit (32M) ---

$dim = $argv[1];
$offsetX = $argv[2];
$offsetY = $argv[3];
$outname = $argv[4];

// dim of the image is 32 + 2 pixels for border
// (left and right, up and down)
$IMAGE_DIMENSION = 34;

// Number of image tiles, fixed at 20 images
// so output is 20 images high and wide
$TILE_DIMENSION = 20;

// echo 'test_enter';

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

// header('Content-Type: image/png');
include 'klassen.php';

function drawMinimapPart($abschnitt, $dim, $outname)
{
    global $TILE_DIMENSION;
    global $IMAGE_DIMENSION;

    global $offsetX;
    global $offsetY;

    $startx = ($abschnitt % ($dim / $TILE_DIMENSION)) * $TILE_DIMENSION;
    $starty = floor($abschnitt / ($dim / $TILE_DIMENSION)) * $TILE_DIMENSION;

    $startx += $offsetX;
    $starty += $offsetY;

    $img = imagecreatetruecolor($TILE_DIMENSION * $IMAGE_DIMENSION, $TILE_DIMENSION * $IMAGE_DIMENSION);

    for ($i = $startx; $i < $startx + $TILE_DIMENSION; ++$i) {
        for ($j = $starty; $j < $starty + $TILE_DIMENSION; ++$j) {
            $f = new Weltraum($i, $j, 0, false);
            $split = explode('.', $f->bild);

            // echo $i." ".$j."\n";

            // echo 'DEBUG '.$i.' | '.$j.' '.$f->bild.' ending '.strtolower($split[sizeof($split) - 1])."\n";

            if (strtolower($split[sizeof($split) - 1]) == 'png') {
                $piece = imagecreatefrompng('images/'.$f->bild);
            }
            if (strtolower($split[sizeof($split) - 1]) == 'jpg') {
                $piece = imagecreatefromjpeg('images/'.$f->bild);
            }

            // Draw border
            // 160,0,0  RGB so its red
            $farbe = imagecolorallocate($piece, 160, 0, 0);
            // var_dump($farbe);
            drawBorder($piece, $farbe);

            imagecopy($img, $piece, $i * $IMAGE_DIMENSION - $startx * $IMAGE_DIMENSION, $j * $IMAGE_DIMENSION - $starty * $IMAGE_DIMENSION, 0, 0, $IMAGE_DIMENSION, $IMAGE_DIMENSION);
        }
    }

    imagepng($img, $outname);
    imagedestroy($img);
}

$itermax = $dim / $TILE_DIMENSION;
$itermaxSquared = $itermax * $itermax;
$upperBound = $itermaxSquared;

for ($x = 0; $x < $upperBound; ++$x) {
    echo 'bearbeite Sektor '.$x."\n";
    drawMinimapPart($x, $dim, 'minimap/bild'.$x.'.png');
}

$TILE_DIMENSION_IN_PIXEL = $IMAGE_DIMENSION * $TILE_DIMENSION;

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
