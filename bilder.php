<?php
Header("Content-Type: image/png");
# Hier wird der Header gesendet, der später die Bilder "rendert" ausser png kann auch jpeg dastehen

##################################################
$width = 100; // Später die Breite des Rechtecks
$height = 100; // Später die Höhe des Rechtecks
$img = ImageCreateFromJPEG($_GET["bild"]); # Hier wird das Bild einer von einem Vorhandenen Bild benutzt (hier: g.jpg)
#
# Seit Version 1.6 der GD-Library ist .gif abgeschaltet also:
#
#---------------------------------------------------
# Zuhause von der GD-Lib: <a href="http://www.boutell.com/gd/" title="http://www.boutell.com/gd/ ansurfen" target="_blank">http://www.boutell.com/gd/</a>
#---------------------------------------------------
#
# Wenn ihr die GD-Library 2.0 habt immer .jpg oder .png aber kein .gif benutzen!
#
# So kann man GD-Library einstellen:
#
# 1. Öffnet im Windows-Ordner die php.ini z.B. C:\Windows\php.ini
#
# 2. Jetzt sucht (STR+F) nach ';Windows Extensions' (ohne den Hochkommata)
#
# 3. Dann schreibt 'extension=php_gd2.dll' unter 'extension=php_gd.dll'
#
# 4. Jetzt macht ein ; vor 'extension=php_gd.dll'. Also aus 'extension=php_gd.dll' mach ';extension=php_gd.dll'
#
# 5. Oder wenn ihr GD-Lib 1.X wollt, dann macht es umgekehrt
##################################################

$black = ImageColorAllocate($img, 0, 255, 255); # Farbe schwarz mit $black festlegen

$font_height = ImageFontHeight(3); # Hier wird die Schrifthöhe mit 3 belegt (hier könnt ihr mit den Werten rumprobieren)

$font_width = ImageFontWidth(3); # Hier wird die Schriftbreite mit 3 belegt (hier könnt ihr mit den Werten rumprobieren)

$image_height = ImageSY($img); # Hier wird in einer Variable die Höhe des Bildes (hier g.jpg) gespeichert

$image_width = ImageSX($img); # Hier wird in einer Variable die Breite des Bildes (hier g.jpg) gespeichert

$text = $_GET["feld"]; # Hier ist der Text, der später im Bild stehen soll

$length = $font_width*strlen($text); # Hier wird die Schriftbreite an das Bild angepasst

# Hier kriegt man durch Teilungen die Mitte des Bildes heraus #

$image_center_x = ($image_width/2)-($length/2);
$image_center_y = ($image_height/2)-($font_height/2);

###############################################################

ImageString($img, 5, $image_center_x, $image_center_y, $text, $black);
/**
* Die 3 nach der Variable $img steht für die GD-Lib interne Schriftart diese geht von 1-5 (also ausprobieren)
*
###############################################################
* Mit $image_center_x und $image_center_y wurde die Mitte herausgefunden und nun angewandt. (Dort können auch eigene Zahlen stehen)
* Beispiel:
*
* ImageString($img, 3, 200, 150, 'Das ist ein Testtext', $black);
*
* Hier ist der String (Zeichenkette, also der Text) 200px von oben und 150px von links vom Bildrand entfernt
*
###############################################################
*
*/

ImagePNG($img); # Hier wird das Bild PNG zugewiesen
ImageDestroy($img) # Hier wird der Speicherplatz für andere Sachen geereinigt
?>
