<?php
$time=$_GET["q"];
if($time>time()+1 || $time<time()-1) $randomcode="fehler"; else
{
$randomcode=md5($time);
$randomcode=substr($randomcode,0,6);
}
header ("Content-type: image/png");
$im = @ImageCreate (60, 25)
      or die ("Kann keinen neuen GD-Bild-Stream erzeugen");
$background_color = ImageColorAllocate ($im, 0, 0, 0);
//$text_color = ImageColorAllocate ($im, 233, 14, 91);
$text_color = ImageColorAllocate ($im, rand(0,255), rand(0,255), rand(0,255));
ImageString ($im, 12, 5, 5, $randomcode, $text_color);
ImagePNG ($im);
?> 