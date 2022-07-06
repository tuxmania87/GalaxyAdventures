<?php
if($_GET["pw"]=='klausx') {
include("head.php");
include("navlogged.php");
include("klassen.php");
$newx=$_GET["setx"];
$newy=$_GET["sety"];
$postx=$_POST["setx"];
$posty=$_POST["sety"];
$getx=$_POST["x1"];
$gety=$_POST["y1"];

if(!isset($getx)) $getx=0;
if(!isset($gety)) $gety=0;

if($_POST["deldo"]!='') {
$rs=$_POST["deldo"];
$punkt=false;
$varwort="";$nx="";$ny="";
for($i=0;$i<strlen($rs);$i++)
{
if($rs[$i]=='-') $punkt=true;
if($rs[$i]!='-' && !$punkt) $nx.=$rs[$i];
if($rs[$i]!='-' && $punkt) $ny.=$rs[$i];
}

mysql_query("DELETE FROM schiffe2 WHERE x='$nx' AND y='$ny' AND typ!='h' AND typ!='s'") or die(mysql_error());
}

echo '!X!',$getx,'  !Y',$gety,'<br />';

if($_POST["do"]==2) { $lastid=checkforlastid('planeten')+1;
mysql_query("INSERT INTO planeten (id,x,y,besitzer,lager,baustoff,duranium,typ,name,orbit,bild) VALUES ('$lastid','$postx','$posty','2','300','0','0','m','noname','1','planet.jpg')") or die(mysql_error());
mysql_query("INSERT INTO `planet2` (`pid`, `feld1`, `feld2`, `feld3`, `feld4`, `feld5`, `feld6`, `feld7`, `feld8`, `feld9`, `feld10`, `feld11`, `feld12`, `feld13`, `feld14`, `feld15`, `feld16`, `feld17`, `feld18`, `feld19`, `feld20`, `feld21`, `feld22`, `feld23`, `feld24`, `feld25`, `feld26`, `feld27`, `feld28`, `feld29`, `feld30`, `feld31`, `feld32`, `feld33`, `feld34`, `feld35`, `feld36`, `feld37`, `feld38`, `feld39`, `feld40`, `feld41`, `feld42`, `feld43`, `feld44`, `feld45`, `feld46`, `feld47`, `feld48`, `feld49`, `feld50`) VALUES
('$lastid', '0-0-i', '0-0-i', '0-0-i', '0-0-g', '0-0-g', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-d', '0-0-f', '0-0-g', '0-0-f', '0-0-m', '0-0-f', '0-0-m', '0-0-g', '0-0-f', '0-0-f', '0-0-d', '0-0-f', '0-0-d', '0-0-m', '0-0-m', '0-0-g', '0-0-g', '0-0-w', '0-0-g', '0-0-d', '0-0-g', '0-0-w', '0-0-g', '0-0-g', '0-0-f', '0-0-d', '0-0-w', '0-0-f', '0-0-g', '0-0-g', '0-0-i', '0-0-i', '0-0-g', '0-0-f', '0-0-g', '0-0-g', '0-0-i', '0-0-g', '0-0-g', '0-0-i')");
}

if($_POST["do"]==7) { $lastid=checkforlastid('planeten')+1;
mysql_query("INSERT INTO planeten (id,x,y,besitzer,lager,baustoff,duranium,typ,name,orbit,bild) VALUES ('$lastid','$postx','$posty','2','300','0','0','l','noname','1','lava.jpg')");
mysql_query("INSERT INTO `planet2` (`pid`, `feld1`, `feld2`, `feld3`, `feld4`, `feld5`, `feld6`, `feld7`, `feld8`, `feld9`, `feld10`, `feld11`, `feld12`, `feld13`, `feld14`, `feld15`, `feld16`, `feld17`, `feld18`, `feld19`, `feld20`, `feld21`, `feld22`, `feld23`, `feld24`, `feld25`, `feld26`, `feld27`, `feld28`, `feld29`, `feld30`, `feld31`, `feld32`, `feld33`, `feld34`, `feld35`, `feld36`, `feld37`, `feld38`, `feld39`, `feld40`, `feld41`, `feld42`, `feld43`, `feld44`, `feld45`, `feld46`, `feld47`, `feld48`, `feld49`, `feld50`) VALUES
('$lastid', '0-0-l', '0-0-l', '0-0-s', '0-0-s', '0-0-l', '0-0-l', '0-0-l', '0-0-l', '0-0-l', '0-0-s', '0-0-l', '0-0-l', '0-0-v', '0-0-s', '0-0-s', '0-0-l', '0-0-l', '0-0-s', '0-0-l', '0-0-l', '0-0-l', '0-0-s', '0-0-v', '0-0-s', '0-0-l', '0-0-s', '0-0-s', '0-0-l', '0-0-l', '0-0-l', '0-0-s', '0-0-l', '0-0-v', '0-0-s', '0-0-l', '0-0-l', '0-0-s', '0-0-v', '0-0-l', '0-0-v', '0-0-v', '0-0-l', '0-0-s', '0-0-s', '0-0-s', '0-0-l', '0-0-l', '0-0-l', '0-0-l', '0-0-l')");
}

if($_POST["do"]==10) { $lastid=checkforlastid('planeten')+1;
mysql_query("INSERT INTO planeten (id,x,y,besitzer,lager,baustoff,duranium,typ,name,orbit,bild) VALUES ('$lastid','$postx','$posty','2','300','0','0','z','noname','1','wuste.png')");
mysql_query("INSERT INTO `planet2` (`id`, `pid`, `feld1`, `feld2`, `feld3`, `feld4`, `feld5`, `feld6`, `feld7`, `feld8`, `feld9`, `feld10`, `feld11`, `feld12`, `feld13`, `feld14`, `feld15`, `feld16`, `feld17`, `feld18`, `feld19`, `feld20`, `feld21`, `feld22`, `feld23`, `feld24`, `feld25`, `feld26`, `feld27`, `feld28`, `feld29`, `feld30`, `feld31`, `feld32`, `feld33`, `feld34`, `feld35`, `feld36`, `feld37`, `feld38`, `feld39`, `feld40`, `feld41`, `feld42`, `feld43`, `feld44`, `feld45`, `feld46`, `feld47`, `feld48`, `feld49`, `feld50`) VALUES
(NULL, '$lastid', '0-0-d', '0-0-d', '0-0-d', '0-0-d', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-d', '0-0-d', '0-0-d', '0-0-dm', '0-0-d', '0-0-dm', '0-0-dm', '0-0-d', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-d', '0-0-dm', '0-0-d', '0-0-d', '0-0-dm', '0-0-d', '0-0-dm', '0-0-d', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-dm', '0-0-d', '0-0-d', '0-0-d', '0-0-dm', '0-0-d', '0-0-d', '0-0-dm', '0-0-dm', '0-0-d', '0-0-d', '0-0-d', '0-0-dm', '0-0-d', '0-0-d', '0-0-d', '0-0-d', '0-0-d', '0-0-d')");
}


if($_POST["do"]==11) { $lastid=checkforlastid('planeten')+1;
mysql_query("INSERT INTO planeten (id,x,y,besitzer,lager,baustoff,duranium,typ,name,orbit,bild) VALUES ('$lastid','$postx','$posty','2','300','0','0','i','noname','1','eisplanet.jpg')");
mysql_query("INSERT INTO `planet2` (`id`, `pid`, `feld1`, `feld2`, `feld3`, `feld4`, `feld5`, `feld6`, `feld7`, `feld8`, `feld9`, `feld10`, `feld11`, `feld12`, `feld13`, `feld14`, `feld15`, `feld16`, `feld17`, `feld18`, `feld19`, `feld20`, `feld21`, `feld22`, `feld23`, `feld24`, `feld25`, `feld26`, `feld27`, `feld28`, `feld29`, `feld30`, `feld31`, `feld32`, `feld33`, `feld34`, `feld35`, `feld36`, `feld37`, `feld38`, `feld39`, `feld40`, `feld41`, `feld42`, `feld43`, `feld44`, `feld45`, `feld46`, `feld47`, `feld48`, `feld49`, `feld50`) VALUES
(NULL, '$lastid', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-fl', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-fl', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-fl', '0-0-fl', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-fl', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-i', '0-0-fl', '0-0-i', '0-0-fl', '0-0-i', '0-0-i', '0-0-i', '0-0-i')");
}



if($_POST["do"]==3)
mysql_query("INSERT INTO weltraum (x,y,typ) VALUES ('$postx','$posty','d')");
if($_POST["do"]==4)
mysql_query("INSERT INTO weltraum (x,y,typ) VALUES ('$postx','$posty','b')");
if($_POST["do"]==5)
mysql_query("INSERT INTO weltraum (x,y,typ) VALUES ('$postx','$posty','g')");
if($_POST["do"]==6)
mysql_query("INSERT INTO weltraum (x,y,typ) VALUES ('$postx','$posty','e')");
if($_POST["do"]==8)
mysql_query("INSERT INTO weltraum (x,y,typ) VALUES ('$postx','$posty','p')");
if($_POST["do"]==9)
mysql_query("INSERT INTO weltraum (x,y,typ) VALUES ('$postx','$posty','x')");



if($_GET["set"]==1)
{
$ch=false;
$tt=mysql_query("SELECT * FROM schiffe2 WHERE typ='m' AND typ='e' AND typ='h' AND typ='d' AND typ='b' AND typ='g' AND x='$newx' AND y='$newy'");
while($t=mysql_fetch_array($tt))
$ch=true;
if(!$ch) {

?>
<table><tr><td>
<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="do" value="2">
<input type="hidden" name="setx" value="<?php echo $newx; ?>"><input type="hidden" name="sety" value="<?php echo $newy; ?>"><input type="image" src="planet.jpg"><input type="hidden" name="x1" value="<?php echo $getx; ?>"><input type="hidden" name="y1" value="<?php echo $gety; ?>"></form>
</td><td>
<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="do" value="7">
<input type="hidden" name="setx" value="<?php echo $newx; ?>"><input type="hidden" name="sety" value="<?php echo $newy; ?>"><input type="image" src="lava.jpg"><input type="hidden" name="x1" value="<?php echo $getx; ?>"><input type="hidden" name="y1" value="<?php echo $gety; ?>"></form>
</td><td>
<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="do" value="11">
<input type="hidden" name="setx" value="<?php echo $newx; ?>"><input type="hidden" name="sety" value="<?php echo $newy; ?>"><input type="image" src="eisplanet.jpg"><input type="hidden" name="x1" value="<?php echo $getx; ?>"><input type="hidden" name="y1" value="<?php echo $gety; ?>"></form>
</td><td>
<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="do" value="10">
<input type="hidden" name="setx" value="<?php echo $newx; ?>"><input type="hidden" name="sety" value="<?php echo $newy; ?>"><input type="image" src="wuste.png"><input type="hidden" name="x1" value="<?php echo $getx; ?>"><input type="hidden" name="y1" value="<?php echo $gety; ?>"></form>
</td><td>
<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="do" value="3">
<input type="hidden" name="setx" value="<?php echo $newx; ?>"><input type="hidden" name="sety" value="<?php echo $newy; ?>"><input type="image" src="deut.jpg"><input type="hidden" name="x1" value="<?php echo $getx; ?>"><input type="hidden" name="y1" value="<?php echo $gety; ?>"></form>
</td><td>
<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="do" value="4">
<input type="hidden" name="setx" value="<?php echo $newx; ?>"><input type="hidden" name="sety" value="<?php echo $newy; ?>"><input type="image" src="nebel.jpg"><input type="hidden" name="x1" value="<?php echo $getx; ?>"><input type="hidden" name="y1" value="<?php echo $gety; ?>"></form>
</td><td>
<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="do" value="8">
<input type="hidden" name="setx" value="<?php echo $newx; ?>"><input type="hidden" name="sety" value="<?php echo $newy; ?>"><input type="image" src="pulsar.jpg"><input type="hidden" name="x1" value="<?php echo $getx; ?>"><input type="hidden" name="y1" value="<?php echo $gety; ?>"></form>
</td><td>
<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="do" value="9">
<input type="hidden" name="setx" value="<?php echo $newx; ?>"><input type="hidden" name="sety" value="<?php echo $newy; ?>"><input type="image" src="black.jpg"><input type="hidden" name="x1" value="<?php echo $getx; ?>"><input type="hidden" name="y1" value="<?php echo $gety; ?>"></form>
</td><td>
<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="do" value="5">
<input type="hidden" name="setx" value="<?php echo $newx; ?>"><input type="hidden" name="sety" value="<?php echo $newy; ?>"><input type="image" src="green.jpg"><input type="hidden" name="x1" value="<?php echo $getx; ?>"><input type="hidden" name="y1" value="<?php echo $gety; ?>"></form>
</td><td>
<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="do" value="6">
<input type="hidden" name="setx" value="<?php echo $newx; ?>"><input type="hidden" name="sety" value="<?php echo $newy; ?>"><input type="image" src="erz.jpg"><input type="hidden" name="x1" value="<?php echo $getx; ?>"><input type="hidden" name="y1" value="<?php echo $gety; ?>"></form>

</td></tr></table>
<?php
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><input type="submit" value="zur&uuml;ck"></form>';
$wirdset=1;
}
else echo '!!ERROR!!';
} else {
echo '<table class="bordered">';
echo '<tr><td>x/y</td>';
for($i=1+$getx;$i<=20+$getx;$i++) echo '<td>',$i,'</td>';
echo '</tr>';

for($y=1+$gety;$y<=20+$gety;$y++)
{
for($x=1+$getx;$x<=20+$getx;$x++)
{
if($x==1+$getx && $y>1+$gety-1) echo '<tr><td>',$y,'</td>';


if($y>=1+$gety) {

$checked="";
$abfrage=mysql_query("SELECT * FROM planeten WHERE x='$x' AND y='$y'");
while($feld=mysql_fetch_array($abfrage))
{
$checked=$feld["typ"];
}


if($checked=='m')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="planet.jpg"></td></form>';
if($checked=='z')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="wuste.png"></td></form>';
if($checked=='i')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="eisplanet.jpg"></td></form>';

if($checked=='l')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="lava.jpg"></td></form>';

$abfrage=mysql_query("SELECT * FROM weltraum WHERE x='$x' AND y='$y'");
while($feld=mysql_fetch_array($abfrage))
{
$checked=$feld["typ"];
}


if($checked=='p')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="pulsar.jpg"></td></form>';
if($checked=='x')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="black.jpg"></td></form>';


if($checked=='d')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="deut.jpg"></td></form>';
if($checked=='h')
echo '<td><bild src="hstation.jpg"></td>';
if($checked=='b')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="nebel.jpg"></td></form>';
if($checked=='g')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="green.jpg"></td></form>';

if($checked=='e')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="erz.gif"></td></form>';
if($checked=='w')
echo '<form action="mapping.php?pw=klausx" method="post"><input type="hidden" name="deldo" value="',$x,'-',$y,'"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="wurmloch.jpg"></td></form>';



if($checked=='')
echo '<form action="mapping.php?pw=klausx&set=1&setx=',$x,'&sety=',$y,'" method="post"><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety,'"><td><input type="image" src="weltraum.jpg" border="0"></td></form>';
if($x==20+$getx) echo '</tr>';

}
}
}
echo '</table>';
}
if ($wirdset!=1) { echo '<form action="mapping.php?pw=klausx" method="post">x/y<br /><input type="text" name="x1" size="2" value="',$getx,'"> / <input type="text" name="y1" size="3" value="',$gety,'"><br /><br /><input type="submit" value="Startkoordinate einstellen"></form>';
echo '<br /><table>';
echo '<tr><td></td><form action="mapping.php?pw=klausx" method="post"><td><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety-20,'"><input type="submit" value="20 hoch"></td></form><td></td></tr>';
echo '<tr><form action="mapping.php?pw=klausx" method="post"><td><input type="hidden" name="x1" value="',$getx-20,'"><input type="hidden" name="y1" value="',$gety,'"><input type="submit" value="20 links"></td></form><td></td><form action="mapping.php?pw=klausx" method="post"><td><input type="hidden" name="x1" value="',$getx+20,'"><input type="hidden" name="y1" value="',$gety,'"><input type="submit" value="20 rechts"></td></form></tr>';
echo '<tr><td></td><form action="mapping.php?pw=klausx" method="post"><td><input type="hidden" name="x1" value="',$getx,'"><input type="hidden" name="y1" value="',$gety+20,'"><input type="submit" value="20 runter"></td></form><td></td></tr>';
}
include("foot.php");
}
?>
