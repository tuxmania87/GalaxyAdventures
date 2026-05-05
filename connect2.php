<?php
get_verbindung();
// DB-Selektion via get_verbindung()
include("pruefetext.php");
mysqli_query($verbindung, "OPTIMIZE TABLE `account` , `allianz` , `erforscht` , `forschung` , `kontakt` , `logbuch` , `login` , `mail` , `news` , `planet` , `planet2` , `schiffe` , `schiffsmodule` , `skn` , `ticklog` , `tom_team` , `umfrage` , `userlog`");

//letze Aktion
$action=date("Y-m-d H:i:s");
$selfid=$_SESSION["Id"];
if(intval(\$_SESSION["Id"])>0) mysqli_query($verbindung, "UPDATE account SET aktion='$action' WHERE id='$selfid'");

function isonline($aktion) {
$tmpvar1=$aktion[0].$aktion[1].$aktion[2].$aktion[3].$aktion[4].$aktion[5].$aktion[6].$aktion[7].$aktion[8].$aktion[9];
if($tmpvar1==date("Y-m-d")) {
$tmpvar2=date("H")*3600+date("i")*60+date("s");
$tmpvar3=$aktion[11]*10+$aktion[12]; $tmpvar3*=3600;
$tmpvar4=$aktion[14].$aktion[15]; $tmpvar3+=$tmpvar4*60;
$tmpvar4=$aktion[17].$aktion[18]; $tmpvar3+=$tmpvar4;
if($tmpvar3>=$tmpvar2-240 && $tmpvar3<=$tmpvar2) return true; else return false;
}
}

function getGeld()
{
$personid=$_SESSION["Id"];
$abfrage=mysqli_query($verbindung, "SELECT * FROM account WHERE id='$personid'");
while($account=mysqli_fetch_array($abfrage))
$geld = $account["geld"];
return $geld;
}

function id2name($id)
	{
	$postquery=mysqli_query($verbindung, "SELECT * FROM account WHERE id='$id'"); //id einsetzen
	while($account=mysqli_fetch_array($postquery)) 			//Abfrage der accountdaten
	{
	if($account["sponsor"]==1) $returnValue='<img src="star.gif" border="0" title="Sponsor von GA">'.$account["displaynick"]; else
	$returnValue=$account["displaynick"];
	$returnValue.=' (';
	if($account["id"]>1 && $account["id"]<10) $returnValue.="NPC"; else
	$returnValue.=$account["id"];
	$returnValue.=')';
	}
	return $returnValue;
	}
function gerdatum($datum)
{
echo $datum[8];echo $datum[9];echo '.';echo $datum[5];echo $datum[6];echo '.';echo $datum[0];echo $datum[1];echo $datum[2];echo $datum[3];echo '&nbsp;';echo $datum[11];echo $datum[12];echo ':';echo $datum[14];echo $datum[15];
}

function getSlot($id) {
$count=0;
$abfrage=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='s' AND besitzer='$id'");
while($schiff=mysqli_fetch_array($abfrage))
{
if($schiff["klasse"]=='Tanker') $count++;
if($schiff["klasse"]=='Erzfrachter') $count++;
if($schiff["klasse"]=='Oberth') $count++;
if($schiff["klasse"]=='Miranda') $count+=2;
if($schiff["klasse"]=='Constitution') $count+=3;
if($schiff["skillbase"]=='1') $count+=2;
}
return $count;
}


class forschungsklasse {
public $id;
public $besitzer;
public $rohstoff4;
public $rohstoff5;
public $waffen2;
public $hull1;
public $antrieb1;
public $krieg;
public $krieg2;
public $krieg3;
public $terra1;
public $terra2;
public $miranda;
public $consti;

function getData($id) {
$t1=mysqli_query($verbindung, "SELECT * FROM forschung WHERE besitzer='$id'");
while($t=mysqli_fetch_array($t1))
{
$this->id=$t["id"];
$this->besitzer=$t["besitzer"];
$this->rohstoff4=$t["rohstoff4"];
$this->rohstoff5=$t["rohstoff5"];
$this->waffen2=$t["waffen2"];
$this->hull1=$t["hull1"];
$this->antrieb1=$t["antrieb1"];
$this->krieg=$t["krieg"];
$this->krieg2=$t["krieg2"];
$this->krieg3=$t["krieg3"];
$this->terra1=$t["terra1"];
$this->terra2=$t["terra2"];
$this->miranda=$t["miranda"];
$this->consti=$t["consti"];
}
}

function setData($id) {
mysqli_query($verbindung, "UPDATE forschung SET miranda='$this->miranda',consti='$this->consti',terra2='$this->terra2',terra1='$this->terra1',krieg3='$this->krieg3',krieg2='$this->krieg2',krieg='$this->krieg',waffen2='$this->waffen2',hull1='$this->hull1',antrieb1='$this->antrieb1',rohstoff4='$this->rohstoff4',rohstoff5='$this->rohstoff5' WHERE besitzer='$id'");
}
}

class smodul {
public $id;
public $sid;
public $a1;
public $a2;
public $a3;
public $a4;
public $a5;
public $b1;
public $b2;
public $b3;
public $b4;
public $b5;
public $c1;
public $c2;
public $c3;
public $c4;
public $c5;

function getData($sid) {
$t1=mysqli_query($verbindung, "SELECT * FROM schiffsmodule WHERE sid='$sid'");
while($t=mysqli_fetch_array($t1))
{
$this->id=$t["id"];
$this->sid=$t["sid"];
$this->a1=$t["a1"];
$this->a2=$t["a2"];
$this->a3=$t["a3"];
$this->a4=$t["a4"];
$this->a5=$t["a5"];
$this->b1=$t["b1"];
$this->b2=$t["b2"];
$this->b3=$t["b3"];
$this->b4=$t["b4"];
$this->b5=$t["b5"];
$this->c1=$t["c1"];
$this->c2=$t["c2"];
$this->c3=$t["c3"];
$this->c4=$t["c4"];
$this->c5=$t["c5"];
}
}

function setData($sid) {
mysqli_query($verbindung, "UPDATE schiffsmodule SET a1='$this->a1',a2='$this->a2',a3='$this->a3',a4='$this->a4',a5='$this->a5',b1='$this->b1',b2='$this->b2',b3='$this->b3',b4='$this->b4',b5='$this->b5',c1='$this->c1',c2='$this->c2',c3='$this->c3',c4='$this->c4',c5='$this->c5' WHERE sid='$sid'");
}
}



class schiffgeneral {
public $id;
public $x;
public $y;
public $name;
public $alarmstufe;
public $hull;
public $schilde;
public $laser;
public $besitzer;
public $energie;
public $maxenergie;
public $img;
public $flotte;
public $lrs;
public $energieoutput;
public $maxhull;
public $maxschilde;
public $schildestatus;
public $rohstoffa;
public $rohstoffb;
public $rohstoffc;
public $rohstoffd;
public $tritanium;
public $isochips;
public $dili;
public $antimaterie;
public $deuterium;
public $npcfod;
public $npcrom;
public $npcborg;
public $npcfer;
public $typ;
public $lager;
public $orbit;
public $skillenergie;
public $skillbau;
public $skillbase;
public $skilldeut;
public $skillerz;
public $skilltarnung;
public $klasse;
public $quest;
public $tarnung;
public $skilltranswarp;

function getData($sid) {
$t1=mysqli_query($verbindung, "SELECT * FROM schiffe WHERE id='$sid'");
while($t=mysqli_fetch_array($t1))
{
$this->id=$t["id"];
$this->x=$t["x"];
$this->y=$t["y"];
$this->name=mysqli_real_escape_string($verbindung, $t["name"]);
$this->alarmstufe=$t["alarmstufe"];
$this->hull=$t["hull"];
$this->schilde=$t["schilde"];
$this->laser=$t["laser"];
$this->besitzer=$t["besitzer"];
$this->energie=$t["energie"];
$this->img=$t["img"];
$this->maxenergie=$t["maxenergie"];
$this->lrs=$t["lrs"];
$this->energieoutput=$t["energieoutput"];
$this->maxhull=$t["maxhull"];
$this->maxschilde=$t["maxschilde"];
$this->schildstatus=$t["schildstatus"];
$this->rohstoffa=$t["rohstoffa"];
$this->rohstoffb=$t["rohstoffb"];
$this->rohstoffc=$t["rohstoffc"];
$this->rohstoffd=$t["rohstoffd"];
$this->tritanium=$t["tritanium"];
$this->isochips=$t["isochips"];
$this->antimaterie=$t["antimaterie"];
$this->dili=$t["dili"];
$this->npcborg=$t["npcborg"];
$this->npcfod=$t["npcfod"];
$this->npcfer=$t["npcfer"];
$this->npcrom=$t["npcrom"];
$this->deuterium=$t["deuterium"];
$this->typ=$t["typ"];
$this->lager=$t["lager"];
$this->orbit=$t["orbit"];
$this->skillenergie=$t["skillenergie"];
$this->skillbau=$t["skillbau"];
$this->skillbase=$t["skillbase"];
$this->skilldeut=$t["skilldeut"];
$this->skillerz=$t["skillerz"];
$this->flotte=$t["flotte"];
$this->klasse=$t["klasse"];
$this->quest=$t["quest"];
$this->skilltarnung=$t["skilltarnung"];
$this->tarnung=$t["tarnung"];
$this->skilltranswarp=$t["skilltranswarp"];
}
}

function setData($sid) {
mysqli_query($verbindung, "UPDATE schiffe SET
tritanium='$this->tritanium',dili='$this->dili',antimaterie='$this->antimaterie',isochips='$this->isochips',skilltranswarp='$this->skilltranswarp',npcfer='$this->npcfer',npcfod='$this->npcfod',npcrom='$this->npcrom',npcborg='$this->npcborg',tarnung='$this->tarnung',skilltarnung='$this->skilltarnung',quest='$this->quest',klasse='$this->klasse',skillerz='$this->skillerz',skilldeut='$this->skilldeut',skillbase='$this->skillbase',flotte='$this->flotte',skillbau='$this->skillbau',skillenergie='$this->skillenergie',orbit='$this->orbit',lager='$this->lager',deuterium='$this->deuterium',rohstoffd='$this->rohstoffd',rohstoffc='$this->rohstoffc',rohstoffb='$this->rohstoffb',rohstoffa='$this->rohstoffa',schildstatus='$this->schildstatus',maxschilde='$this->maxschilde',maxhull='$this->maxhull',energieoutput='$this->energieoutput',maxenergie='$this->maxenergie',img='$this->img',energie='$this->energie',besitzer='$this->besitzer',x='$this->x',y='$this->y',name='$this->name',alarmstufe='$this->alarmstufe',hull='$this->hull',schilde='$this->schilde',laser='$this->laser'
WHERE id = '$this->id'") or die(mysqli_error($verbindung));
}
}

function schiff2trash($newtrash)
{
$newtrash->name='Tr&uuml;mmer';
$newtrash->maxhull=10;
$newtrash->hull=5;
$newtrash->maxschilde=0;
$newtrash->schilde=0;
$newtrash->flotte=0;
$newtrash->schildstatus=0;
$newtrash->besitzer=2;
$newtrash->laser=0;
$newtrash->alarmstufe='green';
$newtrash->img='trumm.gif';
}


function id2ally($id)
{
$question=mysqli_query($verbindung, "SELECT * FROM account WHERE id='$id'");
while($row=mysqli_fetch_array($question))
return $row["allianz"];
}


function checkforlastid($name)
{
$checkid=0;
$tt=mysqli_query($verbindung, "SELECT * FROM $name ");
while($t=mysqli_fetch_array($tt))
if($t["id"]>=$checkid) $checkid=$t["id"];
return $checkid;
}

class planetfeld
{
public $id;
public $pid;
public $feld2;
public $feld3;
public $feld4;
public $feld5;
public $feld6;
public $feld7;
public $feld8;
public $feld9;
public $feld10;
public $feld11;
public $feld12;
public $feld13;
public $feld14;
public $feld15;
public $feld16;
public $feld17;
public $feld18;
public $feld19;
public $feld20;
public $feld21;
public $feld22;
public $feld23;
public $feld24;
public $feld25;
public $feld26;
public $feld27;
public $feld28;
public $feld29;
public $feld30;
public $feld31;
public $feld32;
public $feld33;
public $feld34;
public $feld35;
public $feld36;
public $feld37;
public $feld38;
public $feld39;
public $feld40;
public $feld41;
public $feld42;
public $feld43;
public $feld44;
public $feld45;
public $feld46;
public $feld47;
public $feld48;
public $feld49;
public $feld50;

function getData($pid)
{
$tt=mysqli_query($verbindung, "SELECT * FROM planet2 WHERE pid='$pid'");
while($t=mysqli_fetch_array($tt))
{
$this->id=$t["id"];
$this->pid=$t["pid"];
$this->feld1=$t["feld1"];
$this->feld2=$t["feld2"];
$this->feld3=$t["feld3"];
$this->feld4=$t["feld4"];
$this->feld5=$t["feld5"];
$this->feld6=$t["feld6"];
$this->feld7=$t["feld7"];
$this->feld8=$t["feld8"];
$this->feld9=$t["feld9"];
$this->feld10=$t["feld10"];
$this->feld11=$t["feld11"];
$this->feld12=$t["feld12"];
$this->feld13=$t["feld13"];
$this->feld14=$t["feld14"];
$this->feld15=$t["feld15"];
$this->feld16=$t["feld16"];
$this->feld17=$t["feld17"];
$this->feld18=$t["feld18"];
$this->feld19=$t["feld19"];
$this->feld20=$t["feld20"];
$this->feld21=$t["feld21"];
$this->feld22=$t["feld22"];
$this->feld23=$t["feld23"];
$this->feld24=$t["feld24"];
$this->feld25=$t["feld25"];
$this->feld26=$t["feld26"];
$this->feld27=$t["feld27"];
$this->feld28=$t["feld28"];
$this->feld29=$t["feld29"];
$this->feld30=$t["feld30"];
$this->feld31=$t["feld31"];
$this->feld32=$t["feld32"];
$this->feld33=$t["feld33"];
$this->feld34=$t["feld34"];
$this->feld35=$t["feld35"];
$this->feld36=$t["feld36"];
$this->feld37=$t["feld37"];
$this->feld38=$t["feld38"];
$this->feld39=$t["feld39"];
$this->feld40=$t["feld40"];
$this->feld41=$t["feld41"];
$this->feld42=$t["feld42"];
$this->feld43=$t["feld43"];
$this->feld44=$t["feld44"];
$this->feld45=$t["feld45"];
$this->feld46=$t["feld46"];
$this->feld47=$t["feld47"];
$this->feld48=$t["feld48"];
$this->feld49=$t["feld49"];
$this->feld50=$t["feld50"];
}
}

function setData($pid)
{
mysqli_query($verbindung, "UPDATE planet2 SET feld1='$this->feld1',feld2='$this->feld2',feld3='$this->feld3',feld4='$this->feld4',feld5='$this->feld5',feld6='$this->feld6',feld7='$this->feld7',feld8='$this->feld8',feld9='$this->feld9',feld10='$this->feld10',feld11='$this->feld11',feld12='$this->feld12',feld13='$this->feld13',feld14='$this->feld14',feld15='$this->feld15',feld16='$this->feld16',feld17='$this->feld17',feld18='$this->feld18',feld19='$this->feld19',feld20='$this->feld20',feld21='$this->feld21',feld22='$this->feld22',feld23='$this->feld23',feld24='$this->feld24',feld25='$this->feld25',feld26='$this->feld26',feld27='$this->feld27',feld28='$this->feld28',feld29='$this->feld29',feld30='$this->feld30',feld31='$this->feld31',feld32='$this->feld32',feld33='$this->feld33',feld34='$this->feld34',feld35='$this->feld35',feld36='$this->feld36',feld37='$this->feld37',feld38='$this->feld38',feld39='$this->feld39',feld40='$this->feld40',feld41='$this->feld41',feld42='$this->feld42',feld43='$this->feld43',feld44='$this->feld44',feld45='$this->feld45',feld46='$this->feld46',feld47='$this->feld47',feld48='$this->feld48',feld49='$this->feld49',feld50='$this->feld50' WHERE pid='$pid'");
}
}

class konto
{
public $rohstoffa;
public $rohstoffb;
public $rohstoffc;
public $rohstoffd;
public $deuterium;

function getData($id)
{
$tt=mysqli_query($verbindung, "SELECT * FROM konto WHERE besitzer='$id'");
while($t=mysqli_fetch_array($tt))
{
$this->rohstoffa=$t["rohstoffa"];
$this->rohstoffb=$t["rohstoffb"];
$this->rohstoffc=$t["rohstoffc"];
$this->rohstoffd=$t["rohstoffd"];
$this->deuterium=$t["deuterium"];
}
}

function setData($id)
{
mysqli_query($verbindung, "UPDATE konto SET rohstoffd='$this->rohstoffd',rohstoffa='$this->rohstoffa',rohstoffb='$this->rohstoffb',rohstoffc='$this->rohstoffc',deuterium='$this->deuterium' WHERE besitzer='$id'");
}
}




function splitfeld($text,&$was,&$bauzeit,&$auf)
{
$eins=false; $zwei=false; $was=""; $bauzeit=""; $auf="";
for($i=0;$i<strlen($text);$i++)
{
if($text[$i] == '-') if(!$eins) $eins=true; else $zwei=true;
else
if(!$eins) $was.=$text[$i]; else if(!$zwei) $bauzeit.=$text[$i]; else $auf.=$text[$i];
}
}


?>
