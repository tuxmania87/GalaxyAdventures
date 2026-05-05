<?php
include("head.php");
include("navlogged.php");
include_once("connect.php");
include_once 'auth.php';
$userId = requireLogin();
$fromId = requireIntParam('from');
$tmp = mysqli_query($verbindung, "SELECT besitzer FROM schiffe WHERE id='$fromId'");
while ($testtmp = mysqli_fetch_array($tmp))
    if ($userId != $testtmp['besitzer']) exit('Fehler: Nicht dein Schiff.');
{
$fromid=$_GET["from"];
$toid=$_GET["to"];
$from=new schiff();
$from->getData($fromid);
$to=new schiffgeneral();
$to->getData($toid);
//daten geholt ^^^^^
$do=$_POST["do"];
if($from->y!=$to->y || $from->x!=$to->x) { echo '<span style="color:red;">Hinweis: Ziel ist nicht im Sektor! kein Vorgang m&ouml;glich!</span><br />'; $do=-1; }
if($to->schildstatus==1 && $schiff->besitzer!=$_SESSION["Id"]) { echo '<span style="color:yellow;">Ziel hat Schilde aktiviert! Beamen nicht m&ouml;glich.</span><br />'; $do=-1; }
if($do==1) {	//beamen!
//rostoff a
$amountA=$_POST["ra"];

//if($amoumtA=="") $amountA=0;
if($amountA > $from->rohstoffa) { echo '<span style="color:yellow;">Hinweis: ',$amountA,' angepasst an ',$from->rohstoffa,'!<br />'; $amountA = $from->rohstoffa; echo '</span><br />'; }
$break=false;
if($amountA+$to->rohstoffa+$to->rohstoffb+$to->rohstoffc+$to->rohstoffd+$to->deuterium>=$to->lager) $amountA=$to->lager-$to->rohstoffa-$to->rohstoffb-$to->rohstoffc-$to->rohstoffd-$to->deuterium;
echo 'Baustoff: ';
while(!$break)
{
$partamount=$amountA<20?$amountA:20;
if($partamount<=0) $break=true;
if(!$break) {
if($from->energie>0) { $amountA-=$partamount; echo $partamount,': <span style="color:green;">ok</span> '; $from->energie--; $from->rohstoffa-=$partamount; $to->rohstoffa+=$partamount; $from->setData($from->id); $to->setData($to->id); $from->getData($from->id); $to->getData($to->id); }
else
{ $break=true; echo $partamount,': <span style="color:red;">Nicht genug Energie!</span>'; }
}
}

//rohstoff b
$amountB=$_POST["rb"];

//if($amoumtA=="") $amountB=0;
if($amountB > $from->rohstoffb) { echo '<br /><span style="color:yellow;">Hinweis: ',$amountB,' angepasst an ',$from->rohstoffb,'!<br />'; $amountB = $from->rohstoffb; echo '</span>'; }
$break=false;
if($amountB+$to->rohstoffa+$to->rohstoffb+$to->rohstoffc+$to->rohstoffd+$to->deuterium>=$to->lager) $amountB=$to->lager-$to->rohstoffa-$to->rohstoffb-$to->rohstoffc-$to->rohstoffd-$to->deuterium;
echo '<br />Duranium: ';
while(!$break)
{
$partamount=$amountB<20?$amountB:20;
if($partamount<=0) $break=true;
if(!$break) {
if($from->energie>0) { $amountB-=$partamount; echo $partamount,': <span style="color:green;">ok</span> '; $from->energie--; $from->rohstoffb-=$partamount; $to->rohstoffb+=$partamount; $from->setData($from->id); $to->setData($to->id); $from->getData($from->id); $to->getData($to->id); }
else
{ $break=true; echo $partamount,': <span style="color:red;">Nicht genug Energie!</span>'; }
}
}

//rohstoff c
$amountC=$_POST["rc"];

//if($amoumtA=="") $amountB=0;
if($amountC > $from->rohstoffc) { echo '<br /><span style="color:yellow;">Hinweis: ',$amountC,' angepasst an ',$from->rohstoffc,'!<br />'; $amountC = $from->rohstoffC; echo '</span>'; }
$break=false;
if($amountC+$to->rohstoffa+$to->rohstoffb+$to->rohstoffc+$to->rohstoffd+$to->deuterium>=$to->lager) $amountC=$to->lager-$to->rohstoffa-$to->rohstoffb-$to->rohstoffc-$to->rohstoffd-$to->deuterium;
echo '<br />Erz: ';
while(!$break)
{
$partamount=$amountC<20?$amountC:20;
if($partamount<=0) $break=true;
if(!$break) {
if($from->energie>0) { $amountC-=$partamount; echo $partamount,': <span style="color:green;">ok</span> '; $from->energie--; $from->rohstoffc-=$partamount; $to->rohstoffc+=$partamount; $from->setData($from->id); $to->setData($to->id); $from->getData($from->id); $to->getData($to->id); }
else
{ $break=true; echo $partamount,': <span style="color:red;">Nicht genug Energie!</span>'; }
}
}


//deut
$amountD=$_POST["deut"];

//if($amoumtA=="") $amountB=0;
if($amountD > $from->deuterium) { echo '<br /><span style="color:yellow;">Hinweis: ',$amountD,' angepasst an ',$from->deuterium,'!<br />'; $amountD = $from->deuterium; echo '</span>'; }
$break=false;
if($amountD+$to->rohstoffa+$to->rohstoffb+$to->rohstoffc+$to->rohstoffd+$to->deuterium>=$to->lager) $amountD=$to->lager-$to->rohstoffa-$to->rohstoffb-$to->rohstoffc-$to->rohstoffd-$to->deuterium;
echo '<br />Deuterium: ';
while(!$break)
{
$partamount=$amountD<20?$amountD:20;
if($partamount<=0) $break=true;
if(!$break) {
if($from->energie>0) { $amountD-=$partamount; echo $partamount,': <span style="color:green;">ok</span> '; $from->energie--; $from->deuterium-=$partamount; $to->deuterium+=$partamount; $from->setData($from->id); $to->setData($to->id); $from->getData($from->id); $to->getData($to->id); }
else
{ $break=true; echo $partamount,': <span style="color:red;">Nicht genug Energie!</span>'; }
}
}

//rohstoffd
$amountDD=$_POST["rd"];

//if($amoumtA=="") $amountB=0;
if($amountDD > $from->rohstoffd) { echo '<br /><span style="color:yellow;">Hinweis: ',$amountDD,' angepasst an ',$from->rohstoffd,'!<br />'; $amountDD = $from->rohstoffd; echo '</span>'; }
$break=false;
if($amountDD+$to->rohstoffa+$to->rohstoffb+$to->rohstoffc+$to->rohstoffd+$to->deuterium>=$to->lager) $amountD=$to->lager-$to->rohstoffa-$to->rohstoffb-$to->rohstoffc-$to->rohstoffd-$to->deuterium;
echo '<br />Sorium: ';
while(!$break)
{
$partamount=$amountDD<20?$amountDD:20;
if($partamount<=0) $break=true;
if(!$break) {
if($from->energie>0) { $amountDD-=$partamount; echo $partamount,': <span style="color:green;">ok</span> '; $from->energie--; $from->rohstoffd-=$partamount; $to->rohstoffd+=$partamount; $from->setData($from->id); $to->setData($to->id); $from->getData($from->id); $to->getData($to->id); }
else
{ $break=true; echo $partamount,': <span style="color:red;">Nicht genug Energie!</span>'; }
}
}


}


if($do!=-1) {
echo '<h3>Transfer</h3>Energie: ',$from->energie,'<br />';
echo '<form action="beamto.php?from=',$from->id,'&to=',$to->id,'" method="post"><table class="bordered">';
echo '<tr><td></td><td>',$from->name,'</td><td></td><td>',$to->name,'</td></tr>';
echo '<tr><td>Baustoff</td><td><input type="text" size="6" name="ra">  (',$from->rohstoffa,')</td><td>--></td><td>',$to->rohstoffa,'</td></tr>';
echo '<tr><td>Duranium</td><td><input type="text" size="6" name="rb">  (',$from->rohstoffb,')</td><td>--></td><td>',$to->rohstoffb,'</td></tr>';
echo '<tr><td>Erz</td><td><input type="text" size="6" name="rc">  (',$from->rohstoffc,')</td><td>--></td><td>',$to->rohstoffc,'</td></tr>';
echo '<tr><td>Sorium</td><td><input type="text" size="6" name="rd">  (',$from->rohstoffd,')</td><td>--></td><td>',$to->rohstoffd,'</td></tr>';
echo '<tr><td>Deuterium</td><td><input type="text" size="6" name="deut">  (',$from->deuterium,')</td><td>--></td><td>',$to->deuterium,'</td></tr>';
echo '<tr><td></td><td>',$from->rohstoffa+$from->rohstoffb+$from->rohstoffc+$from->rohstoffd+$from->deuterium,'/',$from->lager,'</td><td></td><td>',$to->rohstoffa+$to->rohstoffb+$to->rohstoffc+$to->rohstoffd+$to->deuterium,'/',$to->lager,'</td></td></tr>';
echo '</table>';
echo '<input type="hidden" name="do" value="1"><input type="submit" value="transferieren"></form>';
}
if($to->besitzer==$_SESSION["Id"] && $to->typ=='s') echo '<br /><a href="schiffe.php?sid=',$to->id,'">vor zum Zielschiff</a><br />';
if($to->besitzer==$_SESSION["Id"] && ($to->typ=='m' || $to->typ=='l')) echo '<br /><a href="planet.php?pid=',$to->id,'">vor zum Zielplaneten</a><br />';
if($from->typ=='s') echo '<br /><a href="schiffe.php?sid=',$from->id,'">zur&uuml;ck zum Schiff</a>';
else echo '<br /><a href="planet.php?pid=',$from->id,'">zur&uuml;ck zum Planeten</a>';
}
include("foot.php");

