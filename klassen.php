<?php

include_once 'connect.php';

if (0 > version_compare(PHP_VERSION, '5')) {
    exit('This file was generated for PHP 5');
}

// lcar button class
// needs js functions highlight() downlight()
class Button
{
    public $url = '';
    public $label = '';
    public $tooltip = '';
    public $help = '';
    public static $id = 0;

    public function __construct($u, $l, $h = '')
    {
        $this->url = $u;
        $this->label = $l;
        $this->help = $h;
        ++Button::$id;
    }

    public function printme()
    {
        $verbindung = get_verbindung();
        if ($this->url == '') {
            echo '<img id="lcarl_'.Button::$id.'" src="images/misc/l.gif" style="border:none;" />';
            echo '<input type="submit" value="'.$this->label.'" class="lcar" id="lacr_'.Button::$id.'" onmouseover="highlight('.Button::$id.')" onmouseout="downlight('.Button::$id.')" >';
            if ($this->help != '') {
                $q = mysqli_query($verbindung, "select value from tooltip where `key` = '".$this->help."'");
                $r = mysqli_fetch_array($q);
                $tooltip = str_replace("\n", '<br />', $r['value']);

                echo '<img id="lcarh_'.Button::$id.'" src="images/misc/rhelp.gif" onmouseover="highlighthelp('.Button::$id.'); Tip(\''.$tooltip.'\');" onmouseout="UnTip();downlighthelp('.Button::$id.');" style="border:none;" />';
            } else {
                echo '<img id="lcarr_'.Button::$id.'" src="images/misc/r.gif" style="border:none;" />';
            }
        } else {
            echo '<img id="lcarl_'.Button::$id.'" src="images/misc/l.gif" style="border:none;" />';
            echo '<a class="lcar" id="lacr_'.Button::$id.'" href="'.$this->url.'" onmouseover="highlight('.Button::$id.')" onmouseout="downlight('.Button::$id.')" >';
            echo '<span class="lcar">'.$this->label.'</span></a>';

            if ($this->help != '') {
                $q = mysqli_query($verbindung, "select value from tooltip where `key` = '".$this->help."'");
                $r = mysqli_fetch_array($q);
                $tooltip = nl2br($r['value']);

                echo '<img onmouseover="Tip(\''.$tooltip.'\')" onmouseout="Untip()" id="lcarh_'.Button::$id.'" src="images/misc/rhelp.gif" style="border:none;" />';
            } else {
                echo '<img id="lcarr_'.Button::$id.'" src="images/misc/r.gif" style="border:none;" />';
            }
        }
    }

    public function setTooltip($t)
    {
        $this->tooltip = $t;
    }

    public function printform()
    {
    }
}

class Allianz
{
    public $id;
    public $name;
    public $leiter;
    public $tag = '';
    public $info = '';

    public function __construct($aid)
    {
        // if($aid<=0) return;
        $verbindung = get_verbindung();
        $abfrage = mysqli_query($verbindung, "SELECT * FROM allianz WHERE id='$aid'");
        while ($row = mysqli_fetch_array($abfrage)) {
            $this->id = $aid;
            $this->name = $row['name'];
            if (ctype_digit($row['leiter']) && $row['leiter'] > 0) {
                $this->leiter = $row['leiter'];
            }
            $this->tag = $row['tag'];
            $this->info = $row['info'];
        }
    }
}

class Quest
{
    public $id = 0;
    public $qid = 0;
    public $name = '';
    public $text = '';
    public $typ = 0;
    public $anzahl = 0;
    public $max = 0;
    public $uid = 0;
    public $geber = 0;
    public $abgeber = 0;
    public $zusatz = '';
    public $zusatz2 = '';
    public $erledigt = 0;
    public $level = 0;
    public $bschiffe = [];
    public $brohstoffe = [];

    public $abgabetext = "";


    public function __construct($qqid)
    {
        $verbindung = get_verbindung();
        $abfrage = mysqli_query($verbindung, "SELECT * FROM erfolge,quests WHERE erfolge.id='".$qqid."' AND quests.id=erfolge.qid") or exit("error");
        while ($row = mysqli_fetch_assoc($abfrage)) {
            $this->id = $qqid;
            $this->qid = $row['qid'];
            $this->name = $row['titel'];
            $this->text = $row['text'];
            $this->abgabetext = $row['abgabetext'];
            $this->typ = $row['typ'];
            $this->anzahl = $row['anzahl'];
            $this->zusatz = $row['zusatz'];
            $this->zusatz2 = $row['zusatz2'];
            $this->max = $row['max'];
            $this->geber = $row['geber'];
            $this->abgeber = $row['abgeber'];
            $this->uid = new Account($row['uid']);
            $this->erledigt = $row['erledigt'];
            $this->level = $row['level'];
            $this->bschiffe = explode(',', $row['bschiffe']);
            $this->brohstoffe = explode(',', $row['brohstoffe']);
        }
    }

    /*
        Typcodierung von Quests
        0=nicht vergeben
        1=Töte MAX schiffe vom spieler mit id ZUSATZ
        2=Sammle Rohstoffe vom typ ZUSATZ menge MAX
        3=Scanne MAX Sternensysteme
        4=Baue Gebauede ZUSATZ auf MAX planeten
        5=Baue Schiff ZUSATZ , anzahl MAX
        6=Sammle questitem ZUSATZ anzahl MAX dropbar von schiffe der rasse ZUSATZ2
        7=Baue MAX anzahl vom Rohstoff ZUSATZ ab
        8=LEERE QUEST ( sofort erfuellt ) SINN: story ueberleitungen
        */

    public function plus()
    {
        $verbindung = get_verbindung();
        if ($this->anzahl < $this->max) {
            ++$this->anzahl;
        }
        mysqli_query($verbindung, "UPDATE erfolge SET anzahl='".$this->anzahl."' WHERE id='".$this->id."'");
    }

    public function done()
    {
        $verbindung = get_verbindung();
        if ($this->anzahl == $this->max) {
            $this->erledigt = 1;
        }
        mysqli_query($verbindung, "UPDATE erfolge SET erledigt='".$this->erledigt."' WHERE id='".$this->id."'");
    }
}

class Logbuch
{
    public $id = 0;
    public $initiator = 0;
    public $betroffener = 0;
    public $x = 0;
    public $y = 0;
    public $system;
    public $text = '';
    public $typ = 'zivil';
    public $klasse = 'eingang';
    public $zeit = '';

    public function __construct($subject, $uid)
    {
        $verbindung = get_verbindung();
        $changed = false;
        $abfrage = mysqli_query($verbindung, "SELECT * FROM logbuch WHERE $subject='$uid'");
        while ($row = mysqli_fetch_array($abfrage)) {
            $changed = true;
            $this->id = $row['id'];
            $this->initiator = $row['initiator'];
            $this->betroffener = $row['betroffener'];
            $this->x = $row['x'];
            $this->y = $row['y'];
            $this->system = new System($row['system']);
            $this->text = $row['text'];
            $this->typ = $row['typ'];
            $this->klasse = $row['klasse'];
            $this->zeit = $row['zeit'];
        }
        if (!$changed) {
            $this->id = checkforlastid('logbuch') + 1;
            //mysqli_query($verbindung, 'INSERT INTO logbuch () VALUES ()');
        }
    }

    public function save()
    {
        $verbindung = get_verbindung();
        mysqli_query($verbindung, "UPDATE logbuch SET zeit='$this->zeit',klasse='$this->klasse',typ='$this->typ',initiator='$this->initiator',betroffener='$this->betroffener',x='$this->x',y='$this->y',`system`='".$this->system->id."',text='$this->text' WHERE id='$this->id'") or exit($verbindung->error);
    }
}

class Account
{
    public $id = 0;
    public $login;
    public $passwort;
    public $nickname;
    public $regdatum;
    public $regip = 0;
    public $newAttr = 0;
    public $bild;
    public $beschreibung;
    public $email;
    public $mitglied = 0;
    public $inaktiv = 0;
    public $forschungsstatus;
    public $urlaub = false;
    public $allianz;
    public $level = 0;
    public $mapper = false;
    public $beta = 0;
    public $aktion = 0;
    public $chat = 0;
    public $moderator = 0;
    public $gruppe = 0;
    public $gruppeinvite = false;
    public $wpunkte = 0;
    public $rasse = '';
    public $rasse_img = '';

    public function __construct($nid)
    {
        $verbindung = get_verbindung();
        if (ctype_digit($nid)) {
            $abfrage = mysqli_query($verbindung, "SELECT * FROM account WHERE id='$nid'");
            while ($row = mysqli_fetch_array($abfrage)) {
                $this->id = $nid;
                $this->login = $row['login'];
                $this->passwort = $row['passwort'];
                $this->nickname = '<a href="userinfo.php?id='.$row['id'].'">'.$row['nickname'].' ('.$row['id'].')</a>';
                if ($nid == 1) {
                    $this->nickname = '<a href="userinfo.php?id='.$row['id'].'"><img src="images/misc/star3.gif" border="0" /> '.$row['nickname'].' ('.$row['id'].')</a>';
                }
                $this->regdatum = $row['regdatum'];
                $this->regip = $row['regip'];
                $this->bild = $row['bild'];
                $this->rasse = $row['rasse'];
                // switch case rasse_img
                switch ($this->rasse) {
                    case 'borg':
                        $this->rasse_img = 'borg';
                        break;
                    case 'federation':
                        $this->rasse_img = 'federation';
                        break;
                    case 'klingon':
                        $this->rasse_img = 'klingon';
                        break;
                    case 'romulan':
                        $this->rasse_img = 'romulan';
                        break;
                    case 'cardassian':
                        $this->rasse_img = 'cardassian';
                        break;
                    case 'ferengi':
                        $this->rasse_img = 'ferengi';
                        break;
                }
                // endswitchcase
                $this->beschreibung = $row['beschreibung'];
                $this->email = $row['email'];
                $this->mitglied = $row['mitglied'];
                $this->inaktiv = $row['inaktiv'];
                $this->forschungsstatus = new Forschungen($nid);
                $this->urlaub = $row['urlaub'];
                $this->allianz = $row['allianz'] > 0 ? new Allianz($row['allianz']) : null;
                $this->level = $row['level'];
                $this->aktion = $row['aktion'];
                $this->mapper = $row['mapper'] == 1 ? true : false;
                $this->beta = $row['beta'] == 1 ? true : false;
                $this->chat = $row['chat'];
                $this->gruppe = $row['gruppe'];
                $this->moderator = $row['moderator'];
                $this->wpunkte = $row['wpunkte'];
                $this->gruppeinvite = $row['gruppeinvite'] == 0 ? false : true;
            }
        }
    }

    public function loeschen()
    {
        $verbindung = get_verbindung();
        if ($_SESSION['Id'] != $this->id) {
            echo 'Fehler: Fremdzugriff!<br />';

            return 1;
        }
        $abfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE typ='m' AND besitzer='$this->id'");
        while ($row = mysqli_fetch_array($abfrage)) {
            $planet = new Planeten($row['id']);
            $planet->sprengen();
        }
        exit('Account wurde erfolgreich geloescht!');
    }

    public function nickname_aendern($name)
    {
        $this->nickname = pruefetext($name);
    }

    

    public function vertrag($typ)
    {
        $verbindung = get_verbindung();
        $rueck = [];
        $abfrage = mysqli_query($verbindung, "SELECT * FROM vertrag WHERE $typ='1' AND valid='1' AND (initiator='$this->id' OR partner='$this->id')");
        while ($row = mysqli_fetch_array($abfrage)) {
            if ($row['initiator'] == $this->id) {
                $rueck[] = $row['partner'];
            }
            if ($row['partner'] == $this->id) {
                $rueck[] = $row['initiator'];
            }
        }

        return $rueck;
    }
}

class Angebote
{
    public $id;
    public $seller;
    public $sell;
    public $buy;
    public $datum;
    public $duration;

    public function __construct($id)
    {
        $verbindung = get_verbindung();
        $q = mysqli_query($verbindung, 'select * from ebay where id='.$id);
        while ($r = mysqli_fetch_array($q)) {
            $this->id = $id;
            $this->seller = new Account($r['anbieter']);
            $this->sell = new Frachtraum($r['sell'], 'dummy');
            $this->buy = new Frachtraum($r['buy'], 'dummy');
            $this->datum = $r['datum'];
        }
    }

    public static function getList()
    {
        $verbindung = get_verbindung();
        $l = [];
        $q = mysqli_query($verbindung, 'select id from ebay order by datum desc');
        while ($r = mysqli_fetch_array($q)) {
            $l[] = new Angebote($r['id']);
        }

        return $l;
    }
}

class MappedForschungen
{
    public $id;
    public $status;
    public $forsch;

    public function __construct($uid, $fid)
    {
        $verbindung = get_verbindung();
        $q = mysqli_query($verbindung, "select status from mapforschung where uid='".$uid."' and fid ='".$fid."'");
        while ($r = mysqli_fetch_array($q)) {
            $this->id = $fid;
            $this->status = $r['status'];
            $this->forsch = new Forschungen($fid);
        }
    }
}

class Forschungen
{
    public $id;
    public $name;
    public $pre;
    public $beschreibung;
    public $kosten;
    public $dauer;

    public function __construct($i)
    {
        $this->id = $i;
        $verbindung = get_verbindung();
        $q = mysqli_query($verbindung, 'select * from forschung where id='.$i);
        while ($r = mysqli_fetch_array($q)) {
            $this->name = $r['name'];
            $this->beschreibung = $r['beschreibung'];
            $this->kosten = $r['kosten'];
            $this->dauer = $r['dauer'];

            $t_var = explode('/', $r['pre']);
            for ($j = 0; $j < sizeof($t_var); ++$j) {
                if (ctype_digit($t_var[$j])) {
                    $this->pre[] = new Forschungen($t_var[$j]);
                }
            }
        }
    }

    public static function getList()
    {
        $l = [];
        $verbindung = get_verbindung();
        $q = mysqli_query($verbindung, 'select id from forschung');
        while ($r = mysqli_fetch_array($q)) {
            $l[] = new Forschungen($r['id']);
        }

        return $l;
    }
}

/* end of class Forschungen */

class Res
{
    public $name;
    public $id;
    public $bild;
    public $anzahl;
    public $wichtung = 1;
    public $max = -1;

    public static function getList()
    {
        $l = [];
        $verbindung = get_verbindung();
        $q = mysqli_query($verbindung, 'select * from res where id > 0 order by id');
        while ($r = mysqli_fetch_array($q)) {
            $tres = new Res();
            $tres->id = $r['id'];
            $tres->name = $r['name'];
            $tres->bild = $r['bild'];
            $l[] = $tres;
        }

        return $l;
    }
}

class Channel
{
    public $id;
    public $caption;
    public $description;
    public $founder;
    public $public = true;
    public $rpg = true;

    public function __construct($i)
    {
        $this->id = $i;
        $verbindung = get_verbindung();
        $q = mysqli_query($verbindung, 'select * from channel where id='.$i);
        while ($r = mysqli_fetch_array($q)) {
            $this->caption = $r['caption'];
            $this->description = $r['description'];
            $this->founder = new Account($r['founder']);
            $this->public = $r['public'] == 1 ? true : false;
            $this->rpg = $r['rpg'] == 1 ? true : false;
        }
    }

    public static function getList()
    {
        $l = [];
        $verbindung = get_verbindung();
        $q = mysqli_query($verbindung, "select id from channel where public=1 or id in (select cid from channelabo where status=1 and uid='".session_id()."') or   founder = ".$_SESSION['Id'].'            order by id') or exit(mysqli_error($verbindung));
        while ($r = mysqli_fetch_array($q)) {
            $dummy = new Channel($r['id']);
            $l[] = $dummy;
        }

        return $l;
    }
}

class Frachtraum
{
    public $max = 0;
    public $fracht = [];
    public $id2;
    public $typ;
    public $baustoff = 0;
    public $duranium = 0;
    public $erz = 0;
    public $sorium = 0;
    public $isochips = 0;
    public $antimaterie = 0;
    public $dili = 0;
    public $tritanium = 0;
    public $deuterium = 0;
    public $borg = 0;
    public $foderation = 0;
    public $ferengi = 0;
    public $klingonen = 0;
    public $cardassianer = 0;

    public function dump()
    {
        $r = '';
        for ($i = 0; $i < sizeof($this->fracht); ++$i) {
            $r .= $this->fracht[$i]->anzahl;
            $r .= ($i == sizeof($this->fracht) - 1 ? '' : '/');
        }

        return $r;
    }

    public function __construct($sid, $typ)
    {
        $verbindung = get_verbindung();
        if (ctype_digit($sid) || $typ == 'dummy') {
            if ($typ == 'schiff') {
                $abfrage = mysqli_query($verbindung, "SELECT maxfrachtraum,lager,frachtraum FROM schiffe s, bauplan b WHERE s.id='$sid' and s.klasse = b.klasse");
            }
            if ($typ == 'planet') {
                echo $sid.' '.$typ.'<br>';
                $abfrage = mysqli_query($verbindung, "SELECT maxfrachtraum,lager,frachtraum FROM planeten p WHERE id='$sid'") or exit(mysqli_error($verbindung));
            }
            if ($typ == 'konto') {
                $abfrage = mysqli_query($verbindung, "SELECT '' as maxfrachtraum,frachtraum,100000 AS lager FROM konto WHERE  besitzer = '$sid'");
            }

            if ($typ == 'dummy') {
                $sid = explode('/', $sid);

                $resource_ids_as_array = implode(",",array_diff($sid, [""]));

                if(strlen($resource_ids_as_array) > 0)
                {
                    

                    

                    $q = mysqli_query($verbindung, 'select * from res where id in ('.$resource_ids_as_array.') order by id');
                    while ($r = mysqli_fetch_array($q)) {
                        $res = new Res();
                        $res->bild = $r['bild'];
                        $res->id = $r['id'];
                        $res->name = $r['name'];
                        $res->wichtung = $r['punktwichtung'];
                        $res->anzahl = (int) $sid[$r['id']];
                        $this->fracht[] = $res;
                    }
                }
            } else {
                while ($row = mysqli_fetch_array($abfrage)) {
                    $fracht = explode('/', $row['frachtraum']);
                    $maxfracht = explode('/', $row['maxfrachtraum']);
                    $this->max = $row['lager'];
                    $this->id2 = $sid;
                    $this->typ = $typ;

                    $q = mysqli_query($verbindung, 'select * from res where id > 0 order by id');
                    while ($r = mysqli_fetch_array($q)) {
                        $res = new Res();
                        $res->bild = $r['bild'];
                        $res->id = $r['id'];
                        $res->name = $r['name'];
                        $res->wichtung = $r['punktwichtung'];
                        $res->anzahl = (int) $fracht[$r['id'] - 1];
                        $res->max = $maxfracht[$r['id'] - 1] == '' ? -1 : $maxfracht[$r['id'] - 1];
                        $this->fracht[] = $res;
                    }
                }
            }
        }
    }

    public function save()
    {
        $verbindung = get_verbindung();
        switch ($this->typ) {
            case 'planet':
                $saveplace = 'planeten';
                break;
            case 'schiff':
                $saveplace = 'schiffe';
                break;
            case 'konto':
                $saveplace = 'konto';
                break;
        }

        $dbstring = '';
        $dbstring2 = '';
        for ($i = 0; $i < sizeof($this->fracht); ++$i) {
            $dbstring .= $this->fracht[$i]->anzahl;
            $dbstring .= ($i == sizeof($this->fracht) - 1 ? '' : '/');
            $dbstring2 .= $this->fracht[$i]->max;
            $dbstring2 .= ($i == sizeof($this->fracht) - 1 ? '' : '/');
        }

        if ($saveplace == 'konto') {
            mysqli_query($verbindung, 'update '.$saveplace." set frachtraum='".$dbstring."' where besitzer =".$this->id2);
        } else {
            mysqli_query($verbindung, 'update '.$saveplace." set frachtraum='".$dbstring."' where id =".$this->id2);
        }
    }

    public function gesamt()
    {
        $t = 0;
        for ($i = 0; $i < sizeof($this->fracht); ++$i) {
            $t += $this->fracht[$i]->anzahl;
        }

        return $t;
    }

    public function rauswerfen()
    {
        $verbindung = get_verbindung();
        mysqli_query($verbindung, "INSERT INTO schiffe (energieoutput,lager,x,y,name,klasse,baustoff,duranium,erz,deuterium,isochips,antimaterie,dili,tritanium,energie,maxenergie,maxgondeln,hull,maxhull,besitzer) VALUES ('4','".$this->gesamt()."','".rand(1, 120)."','".rand(1, 120)."','Frachtcontainer','Container','".$this->baustoff."','".$this->duranium."','".$this->erz."','".$this->deuterium."','".$this->isochips."','".$this->antimaterie."','".$this->dili."','".$this->tritanium."','500','500','200','2','2','2')");
        $this->baustoff = 0;
        $this->duranium = 0;
        $this->erz = 0;
        $this->sorium = 0;
        $this->isochips = 0;
        $this->antimaterie = 0;
        $this->dili = 0;
        $this->tritanium = 0;
        $this->deuterium = 0;
        $this->borg = 0;
        $this->foderation = 0;
        $this->ferengi = 0;
        $this->klingonen = 0;
        $this->cardassianer = 0;
        $this->max = 1;
        // $this->id = $saveid;
        $this->save();
    }
}

/* end of class Frachtraum */

class Planeten extends Rohling
{
    public $heimat = 0;
    public array $feld = [];
    public $orbit = 0;
    public $klasse;
    public $defense;
    public $skill;
    public $fehler = [
        0 => '',
        1 => 'Das Schiff muss den Orbit erst verlassen!<br />',
        2 => 'Das Schiff ben&ouml;tigt 1 Energie um sich ein Feld bewegen zu k&ouml;nnen!<br />',
        3 => 'Das Schiff kann sich nur im Flottenverband bewegen!<br />',
        4 => 'Die Warpgondeln deines Schiffes sind &uuml;berhitzt! Warte einen Tick bis sie sich abgek&uuml;hlt haben!<br />',
        5 => 'Es wird 1 Energie ben&ouml;tigt um die Schilde zu aktivieren<br />',
        6 => 'Die Schilde m&uuml;ssen erst aufgeladen werden, bevor sie aktiviert werden k&ouml;nnen!<br />',
        7 => 'Nicht genug Energie vorhanden um Schilde aufzuladen!<br />',
        8 => 'Bei aktivierten Schilden kann nicht aufgeladen werden!<br />',
        9 => 'Schiff kann sich nicht tarnen!<br />',
        10 => 'Das Schiff ben&ouml;tigt 1 Energie um die Tarnvorrichtung zu aktivieren!<br />',
    ];

    public function __construct($nid)
    {
        $verbindung = get_verbindung();
        if (!ctype_digit($nid)) {
            echo 'Fehler: ID(', $nid, ') ist ung&uuml;ltig!<br />';

            return -1;
        }
        $abfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE id='$nid'");
        while ($row = mysqli_fetch_array($abfrage)) {
            $this->id = $nid;
            $this->heimat = $row['heimat'];
            $this->name = pruefetext($row['name']);
            $this->nameklartext = $row['name'];
            $this->besitzer = new Account($row['besitzer']);
            $this->frachtraum = new Frachtraum($nid, 'planet');
            $this->position = new Position($nid, 'planet');
            $this->energie = $row['energie'];
            $this->maxenergie = $row['maxenergie'];
            $this->energieoutput = $row['energieoutput'];
            $this->alarmstufe = $row['alarmstufe'];
            $this->typ = $row['typ'];
            $this->orbit = 1;
            $this->klasse = $row['typ'];
            $this->phaser = $row['phaser'];
            $this->maxphaser = 400000;
            $this->torpedo = $row['torpedo'];
            $this->bild = $row['bild'];
            $this->nachricht = $row['nachricht'];
            $this->laser = $row['laser'];
            $this->schilde = $row['schilde'];
            $this->maxschilde = $row['maxschilde'];
            $this->schildstatus = $row['schildstatus'];
            $this->feld[] = 'null';

            // $flimes = strpos($this->typ, "mond") > 0 ? 26 : 50;
            $flimes = 70;
            for ($i = 1; $i <= $flimes; ++$i) {
                $this->feld[] = new Gebaude($nid, $i);
            }

            return 0;
        }
        echo 'Fehler: Objekt nicht in der Datenbank lokalisierbar!<br />';

        return -1;
    }

    public function sprengen()
    {
        $verbindung = get_verbindung();
        $this->besitzer = 13;
        mysqli_query($verbindung, "UPDATE planeten SET heimat=0,besitzer='$this->besitzer' WHERE id = '$this->id'");
        include 'clear_lite.php';
        echo 'Planet wurde freigegeben';

        return 0;
    }
}

class Position
{
    public $x = 0;
    public $y = 0;
    public $orbit = 0;
    public $system;

    public function __construct($sid, $typ)
    {
        $verbindung = get_verbindung();
        if ($typ == 'planet') {
            $abfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE id='$sid'");
        }
        if ($typ == 'schiff') {
            $abfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE id='$sid'");
        }
        if ($typ) {
            while ($row = mysqli_fetch_array($abfrage)) {
                $this->x = $row['x'];
                $this->y = $row['y'];
                $this->orbit = $typ == 'planet' ? 1 : $row['orbit'];
                $this->system = new System($row['system']);
            }
        }
    }
}

/* end of class Position */

class System
{
    public $id = 0;
    public $name = '';
    public $x = 0;
    public $y = 0;
    public $bild = '';
    public $feld;

    public function __construct($sid)
    {
        $verbindung = get_verbindung();

        if (ctype_digit($sid)) {
            $abfrage = mysqli_query($verbindung, "SELECT * FROM systeme WHERE id='$sid'") or exit($verbindung->error);
            while ($row = mysqli_fetch_array($abfrage)) {
                $this->id = $sid;
                $this->name = $row['name'];
                $this->feld = new Systemfelder($row['typ']);
                $this->x = $row['x'];
                $this->y = $row['y'];
                $this->bild = &$this->feld->bild;
            }
        } else {
            $abfrage = mysqli_query($verbindung, "SELECT * FROM systeme WHERE x='".$sid[0]."' and y='".$sid[1]."'") or exit($verbindung->error);
            while ($row = mysqli_fetch_array($abfrage)) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->feld = new Systemfelder($row['typ']);
                $this->x = $row['x'];
                $this->y = $row['y'];
                $this->bild = &$this->feld->bild;
            }
        }
    }
}

class Rohling
{
    public $name = 0;
    public $nameklartext = 0;
    public $besitzer;
    public $frachtraum;
    public $position;
    public $energie = 0;
    public $maxenergie = 0;
    public $energieoutput = 0;
    public $schilde = 0;
    public $maxschilde = 0;
    public $schildstatus = 0;
    public $id = 0;
    public $alarmstufe;
    public $typ = '';
    public $laser = 0;
    public $phaser = 0;
    public $maxphaser = 0;
    public $maxtorpedohitze = 0;
    public $torpedohitze = 0;
    public $torpedo = 0;
    public $bild;
    public $nachricht = '';
    public $dock = 0;

    // has to be moved out of rohling 
    // or split up because kampftick is handling instances of
    // ship and planet
    public $klasse;
    public $tarnung;
    public $hull;

    public function kampftick($status, $ziel3) {

    }

    public function schilde()
    {
        $verbindung = get_verbindung();

        $cur_feld = new Weltraum($this->position->x, $this->position->y, $this->position->system->id, $this->position->system->id > 0);
        if ($cur_feld->feld->hide) {
            echo '<span class="error">Du kannst hier ('.$cur_feld->name.') keine Schilde aktivieren!</span>';

            return 0;
        }

        if ($this->energie <= 0) {
            return 5;
        }
        if ($this->schilde == 0) {
            return 6;
        }
        if ($this->dock > 0 && $this->schildstatus == 0) {
            $this->dock = 0;
        }
        $this->schildstatus = $this->schildstatus == 0 ? 1 : 0;
        --$this->energie;
        if ($this->typ == 's') {
            mysqli_query($verbindung, "UPDATE schiffe SET dock='$this->dock',energie=energie-1,schildstatus='$this->schildstatus' WHERE id='$this->id'");
        } else {
            mysqli_query($verbindung, "UPDATE planeten SET energie=energie-1,schildstatus='$this->schildstatus' WHERE id='$this->id'");
        }

        return 0;
    }

    public function schildaufladen($anzahl)
    {
        $verbindung = get_verbindung();

        if ($anzahl + $this->schilde > $this->maxschilde) {
            $anzahl = $this->maxschilde - $this->schilde;
        }
        if ($anzahl < 0) {
            $anzahl = 0;
        }
        if ($this->energie < $anzahl) {
            return 7;
        }
        if ($this->schildstatus == 1) {
            return 8;
        }
        $this->energie -= $anzahl;
        $this->schilde += $anzahl;
        if ($this->typ == 's') {
            mysqli_query($verbindung, "UPDATE schiffe SET energie='$this->energie',schilde='$this->schilde' WHERE id='$this->id'");
        } else {
            mysqli_query($verbindung, "UPDATE planeten SET energie='$this->energie',schilde='$this->schilde' WHERE id='$this->id'");
        }

        return 0;
    }

    public function beamen($modus, $feld, $ziel)
    {
        $verbindung = get_verbindung();
        if ($modus == 1) {
            for ($i = 0; $i < sizeof($this->frachtraum->fracht); ++$i) {
                $aus = false;
                if ($feld[$i] > 0) {
                    $ausgabe = '<br />Beamen: '.$this->frachtraum->fracht[$i]->name.': ';
                    $aus = true;
                }
                while (($this->energie >= 1 || $this->dock == $ziel->id) && $feld[$i] > 0 && $ziel->frachtraum->gesamt() < $ziel->frachtraum->max) {
                    if ($this->frachtraum->fracht[$i]->anzahl < $feld[$i]) {
                        $feld[$i] = $this->frachtraum->fracht[$i]->anzahl;
                    }
                    if ($feld[$i] < 20) {
                        $amount = $feld[$i];
                    } else {
                        $amount = 20;
                    }
                    if ($amount <= 0) {
                        $feld[$i] = 0;
                        break;
                    }
                    if ($ziel->frachtraum->gesamt() + $amount > $ziel->frachtraum->max) {
                        $amount = $ziel->frachtraum->max - $ziel->frachtraum->gesamt();
                    }

                    if ($ziel->frachtraum->fracht[$i]->max >= 0 && $ziel->frachtraum->fracht[$i]->anzahl + $amount > $ziel->frachtraum->fracht[$i]->max) {
                        $amount = $ziel->frachtraum->fracht[$i]->max - $ziel->frachtraum->fracht[$i]->anzahl;
                    }

                    if ($amount <= 0) {
                        $feld[$i] = 0;
                        break;
                    }

                    $ausgabe .= " $amount: ok ";
                    $feld[$i] -= $amount;
                    $this->frachtraum->fracht[$i]->anzahl -= $amount;
                    $ziel->frachtraum->fracht[$i]->anzahl += $amount;
                    if ($this->dock != $ziel->id && $this->dock == 0) {
                        --$this->energie;
                    }
                }
                if ($aus) {
                    echo $ausgabe;
                    $eintrag = new Logbuch('typ', 'neu');
                    $eintrag->x = $this->position->x;
                    $eintrag->y = $this->position->y;
                    $eintrag->zeit = date('Y-m-d H:i:s');
                    $eintrag->system = $this->position->system->id;
                    $eintrag->typ = 'Beamen';
                    $eintrag->klasse = 'Eingang';
                    $eintrag->text = $ausgabe;
                    $eintrag->initiator = $this->besitzer->id;
                    $eintrag->betroffener = $ziel->besitzer->id;
                    $eintrag->save();

                    $eintrag = new Logbuch('typ', 'neu');
                    $eintrag->x = $this->position->x;
                    $eintrag->y = $this->position->y;
                    $eintrag->zeit = date('Y-m-d H:i:s');
                    $eintrag->system = $this->position->system->id;
                    $eintrag->typ = 'Beamen';
                    $eintrag->klasse = 'Ausgang';
                    $eintrag->text = $ausgabe;
                    $eintrag->initiator = $this->besitzer->id;
                    $eintrag->betroffener = $ziel->besitzer->id;
                    $eintrag->save();
                }
            }
        } // ende modus 1

        if ($modus == 2) {
            if ($ziel->schildstatus == 1 && $ziel->besitzer->id != $this->besitzer->id && !in_array($this->besitzer->id, $ziel->besitzer->vertrag('handel'))) {
                return 11;
            }
            for ($i = 0; $i < sizeof($this->frachtraum->fracht); ++$i) {
                $aus = false;
                if ($feld[$i] > 0) {
                    $aus = true;
                    $ausgabe = '<br />Diebstahl: Beamen: '.$this->frachtraum->fracht[$i]->name.': ';
                }

                while (($this->energie >= 1 || $this->dock == $ziel->id) && $feld[$i] > 0 && $this->frachtraum->gesamt() < $this->frachtraum->max) {
                    if ($ziel->frachtraum->fracht[$i]->anzahl < $feld[$i]) {
                        $feld[$i] = $ziel->frachtraum->fracht[$i]->anzahl;
                    }
                    if ($feld[$i] < 20) {
                        $amount = $feld[$i];
                    } else {
                        $amount = 20;
                    }
                    if ($amount <= 0) {
                        $feld[$i] = 0;
                        break;
                    }
                    if ($this->frachtraum->gesamt() + $amount > $this->frachtraum->max) {
                        $amount = $this->frachtraum->max - $this->frachtraum->gesamt();
                    }

                    if ($this->frachtraum->fracht[$i]->max >= 0 && $this->frachtraum->fracht[$i]->anzahl + $amount > $this->frachtraum->fracht[$i]->max) {
                        $amount = $this->frachtraum->fracht[$i]->max - $this->frachtraum->fracht[$i]->anzahl;
                    }

                    if ($amount <= 0) {
                        $feld[$i] = 0;
                        break;
                    }

                    $ausgabe .= " $amount: ok ";
                    $feld[$i] -= $amount;
                    $this->frachtraum->fracht[$i]->anzahl += $amount;
                    $ziel->frachtraum->fracht[$i]->anzahl -= $amount;
                    if ($this->dock != $ziel->id && $this->dock == 0) {
                        --$this->energie;
                    }
                    // feuern
                    if (rand(0, 100) <= 50) {
                        $ziel->feuern($this, '1');
                    }
                    // endefeuern
                    if ($ziel->alarmstatus != 'green' && !in_array($ziel->besitzer->id, $this->besitzer->vertrag('handel'))) {
                        if ($ziel->energie >= 1 && $ziel->schildstatus == 0 && $ziel->schilde > 0) {
                            --$ziel->enerige;
                            $ziel->schildstatus = 1;
                            mysqli_query($verbindung, "UPDATE schiffe SET energie=energie-1,schildstatus=1 WHERE id='".$ziel->id."'");
                        }
                    }
                }
                if ($aus) {
                    echo $ausgabe;
                    $eintrag = new Logbuch('typ', 'neu');
                    $eintrag->x = $this->position->x;
                    $eintrag->y = $this->position->y;
                    $eintrag->zeit = date('Y-m-d H:i:s');
                    $eintrag->system = $this->position->system->id;
                    $eintrag->typ = 'Beamen';
                    $eintrag->klasse = 'Eingang';
                    $eintrag->text = $ausgabe;
                    $eintrag->initiator = $this->besitzer->id;
                    $eintrag->betroffener = $ziel->besitzer->id;
                    $eintrag->save();

                    $eintrag = new Logbuch('typ', 'neu');
                    $eintrag->x = $this->position->x;
                    $eintrag->y = $this->position->y;
                    $eintrag->zeit = date('Y-m-d H:i:s');
                    $eintrag->system = $this->position->system->id;
                    $eintrag->typ = 'Beamen';
                    $eintrag->klasse = 'Ausgang';
                    $eintrag->text = $ausgabe;
                    $eintrag->initiator = $this->besitzer->id;
                    $eintrag->betroffener = $ziel->besitzer->id;
                    $eintrag->save();
                }
            }
        } // ende modus 2

        if ($modus == 3) {  // FERG WEG
            if ($ziel->schildstatus == 1 && $ziel->besitzer->id != 3) {
                return 11;
            }
            $konto = new Konto($this->besitzer->id);
            for ($i = 0; $i < sizeof($ziel->frachtraum->fracht); ++$i) {
                $aus = false;
                if ($feld[$i] > 0) {
                    $aus = true;
                    $ausgabe = '<br />Beamen: '.$ziel->frachtraum->fracht[$i]->name.': ';
                }

                while (($this->energie >= 1 || $this->dock == $ziel->id) && $feld[$i] > 0 && $this->frachtraum->gesamt() < $this->frachtraum->max) {
                    if ($konto->frachtraum->fracht[$i]->anzahl < $feld[$i]) {
                        $feld[$i] = $konto->frachtraum->fracht[$i]->anzahl;
                    }
                    if ($feld[$i] < 20) {
                        $amount = $feld[$i];
                    } else {
                        $amount = 20;
                    }
                    if ($amount < 0) {
                        $feld[$i] = 0;
                        break;
                    }
                    if ($this->frachtraum->gesamt() + $amount > $this->frachtraum->max) {
                        $amount = $this->frachtraum->max - $this->frachtraum->gesamt();
                    }
                    $ausgabe .= " $amount: ok ";
                    $feld[$i] -= $amount;
                    $this->frachtraum->fracht[$i]->anzahl += $amount;
                    $konto->frachtraum->fracht[$i]->anzahl -= $amount;
                }
            }
        } // ende modus 3 ( ferg WEG )

        if ($modus == 4) { // FERG HIN
            $konto = new Konto($this->besitzer->id);
            for ($i = 0; $i < sizeof($this->frachtraum->fracht); ++$i) {
                $aus = false;
                if ($feld[$i] > 0) {
                    $ausgabe = '<br />Beamen: '.$this->frachtraum->fracht[$i]->name.': ';
                    $aus = true;
                }
                while (($this->energie >= 1) && $feld[$i] > 0) {
                    if ($this->frachtraum->fracht[$i]->anzahl < $feld[$i]) {
                        $feld[$i] = $this->frachtraum->fracht[$i]->anzahl;
                    }
                    if ($feld[$i] < 20) {
                        $amount = $feld[$i];
                    } else {
                        $amount = 20;
                    }
                    if ($amount < 0) {
                        $feld[$i] = 0;
                        break;
                    }
                    $ausgabe .= " $amount: ok ";
                    $feld[$i] -= $amount;
                    $this->frachtraum->fracht[$i]->anzahl -= $amount;
                    $konto->frachtraum->fracht[$i]->anzahl += $amount;
                }
            }
        } // ende modus 4 FERG HIN
        $this->frachtraum->save();
        $ziel->frachtraum->save();
        if ($konto->id > 0) {
            $konto->save();
        }
        // echo "UPDATE schiffe SET energie='$this->energie' WHERE id='$this->id'";
        if ($this->typ == 's') {
            mysqli_query($verbindung, "UPDATE schiffe SET energie='$this->energie' WHERE id='$this->id'");
        } else {
            mysqli_query($verbindung, "UPDATE planeten SET energie='$this->energie' WHERE id='$this->id'");
        }
        if ($ziel->typ == 's') {
            mysqli_query($verbindung, "UPDATE schiffe SET energie='$ziel->energie' WHERE id='$ziel->id'");
        } else {
            mysqli_query($verbindung, "UPDATE planeten SET energie='$ziel->energie' WHERE id='$ziel->id'");
        }
    }

    public function feuern($ziel, $modus)
    {
        $verbindung = get_verbindung();

        $cur_feld = new Weltraum($this->position->x, $this->position->y, $this->position->system->id, $this->position->system->id > 0);
        if ($cur_feld->feld->hide) {
            echo '<span class="error">Du kannst hier ('.$cur_feld->name.') nicht feuern!</span>';

            return 0;
        }

        /* Moduscodierung
          0= normales angreifen
          1= feuern ohne gegenwehr ( alarm rot )
          10= normales angreifen mit photonen
          11 feuern ohne gegenwehr mit photonen
          20 = normales angreifen mit quanten
          21 = feuern ohne gegenwerh mit quanten
          30= feuern mit phaser und quanten
          31 = feuern mit phaser und quanten ohne gegenwehr
         */
        // MODUS 0
        if ($ziel->klasse == 'Schlitten' && $this->klasse != 'Vorposten') {
            echo 'Zu Weihnachten wird nicht geballert!';

            return 0;
        }
        if ($modus == 0) {
            $this->feuern($ziel, 1);
            // ALLE im sektor die selbe allianz / vertrag / id haben
            //
            // $ziel->feuern($this,1);
            $this->kampftick('3', $ziel);
        }

        if ($modus == 10) {
            $this->feuern($ziel, 11);
            $this->kampftick('3', $ziel);
        }

        if ($modus == 20) {
            $this->feuern($ziel, 21);
            $this->kampftick('3', $ziel);
        }

        if ($modus == 1 || $modus == 11 || $modus == 21) {
            // haben wir torpedos?
            if ($modus == 21 && $this->frachtraum->fracht[11]->anzahl <= 0) {
                echo 'Keine Quantentorpedos an Board. Feuere daher mit Photonentorpedos!<br />';
                $modus = 11;
            }

            if ($modus == 11 && $this->frachtraum->fracht[10]->anzahl <= 0) {
                echo 'Keine Photonenorpedos an Board. Feuere daher mit Phaser!<br />';
                $modus = 1;
            }

            // if($ziel->typ!='s') { echo 'falscher Feuermodus<br />'; return 0; }
            if ($this->besitzer->id != $ziel->besitzer->id && $ziel->besitzer->id != 2 && $this->besitzer->id != 2 && $this->energie >= 1 && (($this->phaser < $this->maxphaser && $modus == 1) || ($this->torpedohitze < $this->maxtorpedohitze && $modus == 11) || ($this->torpedohitze < $this->maxtorpedohitze && $modus == 21)) && $this->tarnung == 0 && $this->position->x == $ziel->position->x && $this->position->y == $ziel->position->y && $this->position->orbit == $ziel->position->orbit && $this->position->system->id == $ziel->position->system->id && (($this->besitzer->allianz->id != $ziel->besitzer->allianz->id) || $this->besitzer->allianz->id == 0) && !in_array($this->besitzer->id, $ziel->besitzer->vertrag('nap')) && !in_array($this->besitzer->id, $ziel->besitzer->vertrag('frieden')) && (($ziel->besitzer->level >= 4 && $this->besitzer->level >= 4) || $ziel->klasse = 'Vorposten')) {
                $ausgabe = '';
                $change = $ziel->schilde;
                // echo "<br />";
                if ($this->typ == 's') {
                    $objekt = 'Das Schiff';
                } else {
                    $objekt = 'Der Planet';
                }

                if ($modus == 1) {
                    $amount = $this->laser;
                    $waffenname = 'Phaser';
                } elseif ($modus == 11) {
                    $amount = 20;
                    $waffenname = 'Photonentorpedo';
                } elseif ($modus == 21) {
                    $amount = 30;
                    $waffenname = 'Quantentorpedo';
                }

                --$this->energie;

                if ($modus == 1) {
                    ++$this->phaser;
                }

                if ($modus == 11) {
                    ++$this->torpedohitze;
                    --$this->frachtraum->fracht[10]->anzahl;
                }

                if ($modus == 21) {
                    ++$this->torpedohitze;
                    --$this->frachtraum->fracht[11]->anzahl;
                }

                while ($amount > 0 && $ziel->schilde > 0 && $ziel->schildstatus == 1) {
                    $ausgabe = "Angriff: $objekt '$this->name ($this->id)' greift das Schiff '$ziel->name ($ziel->id)' mit ".$waffenname.' an.';
                    --$amount;
                    --$ziel->schilde;
                    if ($ziel->schilde <= 0) {
                        $ziel->schildstatus = 0;
                        $ziel->schilde = 0;
                    }
                }
                if ($ziel->schilde == 0) {
                    $ziel->schildstatus = 0;
                }
                if ($change != $ziel->schilde) {
                    $ausgabe .= "Schilde von $change gesunken auf $ziel->schilde. ";
                }
                $change = $ziel->hull;
                while ($amount > 0 && $ziel->hull > 0 && $ziel->schildstatus == 0 && $ziel->typ == 's') {
                    $ausgabe = "Angriff: $objekt '$this->name ($this->id)' greift das Schiff '$ziel->name ($ziel->id)' mit ".$waffenname.' an.';
                    --$amount;
                    --$ziel->hull;
                }
                if ($change != $ziel->hull) {
                    $ausgabe .= "H&uuml;lle von $change gesunken auf $ziel->hull. ";
                }
                // $ausgabe ins log eintragen sowie 2 zeilen nach hier bearbeiten
                echo '<span class="error">'.$ausgabe.'</span><br />';
                if ($ziel->hull == 0 && $ziel->typ == 's') {
                    // KILL
                    mysqli_query($verbindung, "UPDATE account SET kills=kills+1 WHERE id='".$this->besitzer->id."'");
                    mysqli_query($verbindung, "UPDATE schiffe SET kills=kills+1 WHERE id='$this->id'");
                    echo 'Zerst&ouml;rung: Schiff '.$ziel->name.' ('.$ziel->id.') wurde von '.$this->name.' ('.$this->id.') vernichtet. Besitzer des Vernichters: '.$this->besitzer->nickname.' ('.$this->besitzer->id.')<br />';
                    // pruefen auf questerf�llung
                    // berechnung gruppenstring
                    $usrfeld = [];
                    $gruppenabfrage = mysqli_query($verbindung, "SELECT id FROM account WHERE gruppe>0 AND gruppe='".$this->besitzer->gruppe."'");
                    while ($gruppenrow = mysqli_fetch_array($gruppenabfrage)) {
                        $usrfeld[] = $gruppenrow[0];
                    }
                    // ********

                    $ziel->zerstoerung();
                }
                // echo "<br />";
                if ($ziel->schildstatus == 0 && $ziel->energie >= 1 && $ziel->schilde > 0) {
                    $ziel->dock = 0;
                    $ziel->schildstatus = 1;
                    --$ziel->energie;
                }
                $this->frachtraum->save();
                mysqli_query($verbindung, "UPDATE schiffe SET torpedohitze='$this->torpedohitze',phaser='$this->phaser',energie='$this->energie',hull='$this->hull',schilde='$this->schilde',schildstatus='$this->schildstatus' WHERE id='$this->id'");
                if ($ziel->typ == 's') {
                    mysqli_query($verbindung, "UPDATE schiffe SET dock='$ziel->dock',phaser='$ziel->phaser',energie='$ziel->energie',hull='$ziel->hull',schilde='$ziel->schilde',schildstatus='$ziel->schildstatus' WHERE id='$ziel->id'") or exit($verbindung->error);
                } else {
                    mysqli_query($verbindung, "UPDATE planeten SET phaser='$ziel->phaser',energie='$ziel->energie',hull='$ziel->hull',schilde='$ziel->schilde',schildstatus='$ziel->schildstatus' WHERE id='$ziel->id'") or exit($verbindung->error);
                }

                $eintrag = new Logbuch('typ', 'neu');
                $eintrag->x = $this->position->x;
                $eintrag->y = $this->position->y;
                $eintrag->zeit = date('Y-m-d H:i:s');
                $eintrag->system = $this->position->system->id;
                $eintrag->typ = 'Kampf';
                $eintrag->klasse = 'Eingang';
                $eintrag->text = 'Schiff '.$this->name.' ('.$this->id.') feuert.';
                if ($this->typ != 's') {
                    $eintrag->text = 'Planet '.$this->name.' ('.$this->id.') feuert.';
                }
                $eintrag->initiator = $this->besitzer->id;
                $eintrag->betroffener = $ziel->besitzer->id;
                $eintrag->save();

                $eintrag = new Logbuch('typ', 'neu');
                $eintrag->x = $this->position->x;
                $eintrag->y = $this->position->y;
                $eintrag->zeit = date('Y-m-d H:i:s');
                $eintrag->system = $this->position->system->id;
                $eintrag->typ = 'Kampf';
                $eintrag->klasse = 'Ausgang';
                $eintrag->text = 'Schiff '.$this->name.' ('.$this->id.') feuert.';
                if ($this->typ != 's') {
                    $eintrag->text = 'Planet '.$this->name.' ('.$this->id.') feuert.';
                }
                $eintrag->initiator = $this->besitzer->id;
                $eintrag->betroffener = $ziel->besitzer->id;
                $eintrag->save();
            }
        }
        // zurueckschiessen
    }
}

/* end of class Rohling */

class Bauplan_Schiffe
{
    public $id;
    public $skill;
    public $lrs = 0;
    public $klasse = '';
    public $maxwarpkern = 0;
    public $maxgondeln = 0;
    public $flugkosten = 1;
    public $maxhull = 0;
    public $maxdock = 0;
    public $maxenergie = 0;
    public $maxphaser = 0;
    public $maxtorpedohitze = 0;
    public $energieoutput = 0;
    public $bild = 0;
    public $maxschilde = 0;
    public $lager = 0;
    public $laser = 0;
    public $siedler = false;
    public $dummy_maxfracht;
    public $bauzeit = 0;
    public $kosten;

    public function __construct($id)
    {
        $verbindung = get_verbindung();
        if (ctype_digit($id)) {
            $this->id = $id;
            $q = mysqli_query($verbindung, 'select * from bauplan where id ='.$id);
        } else {
            $this->klasse = $id;
            $q = mysqli_query($verbindung, "select * from bauplan where klasse ='".$id."'");
        }
        while ($r = mysqli_fetch_array($q)) {
            $this->id = $r['id'];
            $this->siedler = (bool) $r['siedler'];
            $this->klasse = $r['klasse'];
            $this->lrs = $r['lrs'];
            $this->skill = new Skill();
            $this->skill->basis = (bool) $r['skillbase'];
            $this->skill->bauen = (bool) $r['skillbau'];
            $this->skill->deuterium = (bool) $r['skilldeut'];
            $this->skill->erz = (bool) $r['skillerz'];
            $this->skill->tarnung = (bool) $r['skilltarnung'];
            $this->skill->transwarp = (bool) $r['skilltranswarp'];
            $this->maxwarpkern = $r['maxwarpkern'];
            $this->maxgondeln = $r['maxgondeln'];
            $this->maxenergie = $r['maxenergie'];
            $this->maxphaser = $r['maxphaser'];
            $this->maxtorpedohitze = $r['maxtorpedohitze'];
            $this->energieoutput = $r['energieoutput'];
            $this->flugkosten = $r['flugkosten'];
            $this->maxhull = $r['maxhull'];
            $this->maxdock = $r['maxdock'];
            $this->bild = $r['bild'];
            $this->maxschilde = $r['maxschilde'];
            $this->lager = $r['lager'];
            $this->laser = $r['laser'];
            $this->dummy_maxfracht = explode('/', $r['maxfrachtraum']);
            $this->bauzeit = $r['bauzeit'];
            $this->kosten = new Frachtraum($r['baukosten'], 'dummy');
        }
    }

    public static function getList()
    {
        $verbindung = get_verbindung();
        $l = [];
        $q = mysqli_query($verbindung, 'select id from bauplan order by siedler desc, id asc');
        while ($r = mysqli_fetch_array($q)) {
            $s = new Bauplan_Schiffe($r['id']);
            $l[] = $s;
        }

        return $l;
    }
}

class Schiffe extends Rohling
{
    public $skill;
    public $tarnung = 0;
    public $flotte = 0;
    public $lrs = 0;
    public $klasse = '';
    public $warpkern = 0;
    public $maxwarpkern = 0;
    public $warpkernstatus = 0;
    public $gondeln = 0;
    public $maxgondeln = 0;
    public $flugkosten = 1;
    public $hull = 0;
    public $qitems = [];
    public $maxhull = 0;
    public $dock = 0;
    public $maxdock = 0;
    public $loot = 0;
    public $quest = 0;
    public $lager = 0;
    public $defense = 0;
    public Bauplan_Schiffe $bau;
    public $fehler = [
        0 => '',
        1 => '<span style="color:red;font-weight:bold;">Fehler S1: Das Schiff muss den Orbit erst verlassen!</span><br />',
        2 => '<span style="color:red;font-weight:bold;">Fehler S2: Das Schiff ben&ouml;tigt 1 Energie um sich ein Feld bewegen zu k&ouml;nnen!</span><br />',
        3 => '<span style="color:red;font-weight:bold;">Fehler S3: Das Schiff kann sich nur im Flottenverband bewegen!</span><br />',
        4 => '<span style="color:red;font-weight:bold;">Fehler S4: Die Warpgondeln deines Schiffes sind &uuml;berhitzt! Warte einen Tick bis sie sich abgek&uuml;hlt haben!</span><br />',
        5 => '<span style="color:red;font-weight:bold;">Fehler S5: Es wird 1 Energie ben&ouml;tigt um die Schilde zu aktivieren</span><br />',
        6 => '<span style="color:red;font-weight:bold;">Fehler S6: Die Schilde m&uuml;ssen erst aufgeladen werden, bevor sie aktiviert werden k&ouml;nnen!</span><br />',
        7 => '<span style="color:red;font-weight:bold;">Fehler S7: Nicht genug Energie vorhanden um Schilde aufzuladen!</span><br />',
        8 => '<span style="color:red;font-weight:bold;">Fehler S8: Bei aktivierten Schilden kann nicht aufgeladen werden!</span><br />',
        9 => '<span style="color:red;font-weight:bold;">Fehler S9: Schiff kann sich nicht tarnen!</span><br />',
        10 => '<span style="color:red;font-weight:bold;">Fehler S10: Das Schiff ben&ouml;tigt 1 Energie um die Tarnvorrichtung zu aktivieren!</span><br />',
        11 => '<span style="color:red;font-weight:bold;">Fehler S11: Bei aktivierten Schilden kann nicht gebeamt werden!</span><br />',
        12 => '<span style="color:red;font-weight:bold;">Fehler S12: Dem Schiff fehlt die F&auml;higkeit Deuterium / Erz einzusaugen!</span><br />',
        13 => '<span style="color:red;font-weight:bold;">Fehler S13: Dem Schiff fehlt Energie!</span><br />',
        14 => '<span style="color:red;font-weight:bold;">Fehler S14: !</span>',
        15 => '<span style="color:red;font-weight:bold;">Fehler S15: []</span>',
        16 => '<span style="color:red;font-weight:bold;">Fehler S16: Um anzudocken musst du dich direkt bei der Station befinden!</span><br />',
        17 => '<span style="color:red;font-weight:bold;">Fehler S17: Du brauchst 1 Energie zum andocken!</span><br />',
        18 => '<span style="color:red;font-weight:bold;">Fehler S18: Du versuchst von der falschen Station abzudocken</span><br />',
        19 => '<span style="color:red;font-weight:bold;">Fehler S19: Die Station hat die maximalen Dockingpl&auml;tze ausgesch&ouml;pft!</span><br />',
        20 => '<span style="color:red;font-weight:bold;">Fehler S20: Du kannst nicht an- oder abdocken wenn die Schilde aktiviert sind!</span><br />',
        21 => '<span style="color:red;font-weight:bold;">Fehler S21: Du bist noch an einer Station angedockt. Docke ab um zu navigieren!</span><br />',
        22 => '<span style="color:red;font-weight:bold;">Fehler S22: Nur gemarkte Tr&uuml;mmer sind pl&uuml;nderbar!</span><br />',
    ];

    public function __construct($nid)
    {
        $verbindung = get_verbindung();
        if (!ctype_digit($nid)) {
            echo 'Fehler: ID (', $nid, ') ist ung&uuml;ltig!<br />';

            return -1;
        }
        $abfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE id='$nid' LIMIT 1");
        while ($row = mysqli_fetch_array($abfrage)) {
            $this->id = $nid;
            $this->bau = new Bauplan_Schiffe($row['klasse']);
            $this->skill = $this->bau->skill;
            $this->name = pruefetext($row['name']);
            $this->nameklartext = $row['name'];
            $this->besitzer = new Account($row['besitzer']);
            $this->frachtraum = new Frachtraum($nid, 'schiff');
            $this->position = new Position($nid, 'schiff');
            $this->energie = $row['energie'];
            $this->maxenergie = &$this->bau->maxenergie;
            $this->energieoutput = &$this->bau->energieoutput;
            $this->alarmstufe = $row['alarmstufe'];
            $this->flotte = $row['flotte'];
            $this->typ = $row['typ'];
            $this->flugkosten = &$this->bau->flugkosten;
            $this->lrs = &$this->bau->lrs;
            $this->klasse = &$this->bau->klasse;
            $this->warpkern = $row['warpkern'];
            $this->maxwarpkern = &$this->bau->maxwarpkern;
            $this->warpkernstatus = $row['warpkernstatus'];
            $this->phaser = $row['phaser'];
            $this->maxphaser = &$this->bau->maxphaser;
            $this->torpedohitze = $row['torpedohitze'];
            $this->maxtorpedohitze = &$this->bau->maxtorpedohitze;
            $this->gondeln = $row['gondeln'];
            $this->lager = &$this->bau->lager;
            $this->maxgondeln = &$this->bau->maxgondeln;
            $this->hull = $row['hull'];
            $this->maxhull = &$this->bau->maxhull;
            $this->bild = &$this->bau->bild;
            $this->nachricht = $row['nachricht'];
            $this->laser = &$this->bau->laser;
            $this->schilde = $row['schilde'];
            $this->maxschilde = &$this->bau->maxschilde;
            $this->schildstatus = $row['schildstatus'];
            $this->dock = $row['dock'];
            $this->maxdock = &$this->bau->maxdock;
            $this->loot = $row['loot'];
            $this->quest = $row['quest'];
            $this->defense = $row['defend'];

            return 0;
        }
        echo 'Fehler: Objekt nicht in der Datenbank lokalisierbar!<br />';

        return -1;
    }

    public function filtername()
    {
        $tempstring = $this->name;
        $outstring = '';
        $ignore = false;
        for ($i = 0; $i < strlen($tempstring); ++$i) {
            $weiter = true;
            if ($tempstring[$i] == '[') {
                $ignore = true;
                $weiter = false;
            }
            if ($tempstring[$i] == ']') {
                $ignore = false;
                $weiter = false;
            }

            if ($weiter) {
                $outstring .= $tempstring[$i];
            }
        }

        return $outstring;
    }

    public function looten($ziel)
    {
        $verbindung = get_verbindung();
        $delme = false;
        if ($ziel->besitzer->id >= 10 || $ziel->klasse != 'Tr&uuml;mmer') {
            return 22;
        }
        if ($ziel->position->x != $this->position->x || $ziel->position->y != $this->position->y || $ziel->position->orbit != $this->position->orbit || $ziel->position->system->id != $this->position->system->id) {
            return 22;
        }
        echo 'gepl&uuml;ndert:<br />';
        for ($i = 0; $i < sizeof($ziel->frachtraum->fracht); ++$i) {
            $menge = $ziel->frachtraum->fracht[$i]->anzahl;
            if ($this->frachtraum->gesamt() + $menge > $this->frachtraum->max) {
                $menge = $this->frachtraum->max - $this->frachtraum->gesamt();
            }
            if ($menge < 0) {
                $menge = 0;
            }
            if ($menge > 0) { // transferieren
                $ziel->frachtraum->fracht[$i]->anzahl -= $menge;
                $this->frachtraum->fracht[$i]->anzahl += $menge;
                $ziel->frachtraum->save();
                $this->frachtraum->save();
                echo $ziel->frachtraum->fracht[$i]->name, ': ', $menge, '<br />';
            }
        }

        // QUESTITEMS
        for ($i = 0; $i < sizeof($ziel->qitems); ++$i) {
            $abfrage = mysqli_query($verbindung, "SELECT erfolge.id FROM erfolge,quests WHERE erfolge.erledigt=0 AND erfolge.uid='".$this->besitzer->id."' AND erfolge.qid='".$ziel->qitems[$i]."' AND erfolge.qid=quests.id AND erfolge.anzahl<quests.max");
            while ($row = mysqli_fetch_array($abfrage)) {
                $qst = new Quest($row[0]);
                $qst->plus();
                if ($qst->anzahl >= $qst->max) {
                    $qst->done();
                }
                $this->qitems[] = $ziel->qitems[$i];
                unset($ziel->qitems[$i]);
                $ziel->qitems = array_values($ziel->qitems);
                echo 'Gegenstand erhalten: ', $qst->zusatz, '<br />';
            }
        }
        $this->savequest();
        $ziel->savequest();
        // ENDE QUESTITEMS

        $gefunden = false;
        if ($this->frachtraum->gesamt() > 0) {
            $gefunden = true;
        }

        if ((!$gefunden && (sizeof($ziel->qitems) == 0)) || $delme) {
            mysqli_query($verbindung, "DELETE FROM schiffe WHERE id='".$ziel->id."'");
            $ziel = null;
        }

        return 0;
    }

    public function savequest()
    {
        $verbindung = get_verbindung();
        $tempkey = implode(',', $this->qitems);
        mysqli_query($verbindung, "UPDATE schiffe SET qitems='$tempkey' WHERE id='".$this->id."'");
    }

    public function kampftick($status, $ziel3)
    {
        $verbindung = get_verbindung();

        $cfeld = new Weltraum($this->position->x, $this->position->y, $this->position->system->id, $this->position->system->id > 0);
        /* Statuscodierung
          0=Alarmstufe Rot
          1=Alarmstufe gelb
          2 = reaktion beim beamen / scannen / etc
          3 = gewaltsamer Fall 0 ( ohne bedingung rot )
         *
         * defense = 0 phaser
         * defense = 1 photonen > phaser
         * defense = 2 quanten > photonen > phaser
         */
        if ($cfeld->feld->hide) {
            return 0;
        }

        if ($status == 0) { // Alarmstufe Rot
            $sarray = [];
            $parray = [];

            $l = $cfeld->getShips();
            for ($i = 0; $i < sizeof($l); ++$i) {
                if (
                    $l[$i]->energie >= 1 && $l[$i]->besitzer->id != $this->besitzer->id
                    && $l[$i]->besitzer->id != 2 && $l[$i]->phaser < $l[$i]->maxphaser
                    && $l[$i]->laser > 0 && $l[$i]->alarmstufe == 'red'
                ) {
                    $sarray[] = $l[$i];
                }
            }

            $abfrage = mysqli_query($verbindung, "SELECT id FROM planeten WHERE x='".$this->position->x."' AND y='".$this->position->y."' AND `system`='".$this->position->system->id."' AND energie>=1 AND besitzer!=2 AND besitzer!='".$this->besitzer->id."' AND laser>0 AND alarmstufe='red'");
            while ($row = mysqli_fetch_array($abfrage)) {
                $parray[] = $row['id'];
            }

            for ($i = 0; $i < sizeof($parray); ++$i) {
                $shp = new Planeten($parray[$i]);
                if (($shp->besitzer->allianz->id == 0 || ($shp->besitzer->allianz->id > 0 && $shp->besitzer->allianz->id != $this->besitzer->allianz->id)) && !in_array($this->besitzer->id, $shp->besitzer->vertrag('nap')) && !in_array($this->besitzer->id, $shp->besitzer->vertrag('verteidigung'))) {
                    $shp->feuern($this, 1 + $shp->defense * 10);
                }
            }
            for ($i = 0; $i < sizeof($sarray); ++$i) {
                $shp = &$sarray[$i];
                if (($shp->besitzer->allianz->id == 0 || ($shp->besitzer->allianz->id > 0 && $shp->besitzer->allianz->id != $this->besitzer->allianz->id)) && !in_array($this->besitzer->id, $shp->besitzer->vertrag('nap')) && !in_array($this->besitzer->id, $shp->besitzer->vertrag('verteidigung'))) {
                    $shp->feuern($this, 1 + $shp->defense * 10);
                }
            }
        }   // ENDE ALARM ROT

        if ($status == 3) { // Alarmstufe Rot 3
            $sarray = [];
            $parray = [];
            // alle moegloichen schiffe ( in dem sektor ) in einem array sammelm
            $t_schiffe = &$cfeld->getShips();
            for ($i = 0; $i < sizeof($t_schiffe); ++$i) {
                if ($t_schiffe[$i]->energie > 0 && $t_schiffe[$i]->besitzer->id != 2 && $t_schiffe[$i]->besitzer->id != $this->besitzer->id && $t_schiffe[$i]->phaser < $t_schiffe[$i]->maxphaser && $t_schiffe[$i]->laser > 0) {
                    $sarray[] = $t_schiffe[$i];
                }
            }

            $abfrage = mysqli_query($verbindung, "SELECT id FROM planeten WHERE x='".$this->position->x."' AND y='".$this->position->y."' AND `system`='".$this->position->system->id."' AND energie>=1 AND besitzer!=2 AND besitzer!='".$this->besitzer->id."' AND laser>0");
            while ($row = mysqli_fetch_array($abfrage)) {
                $parray[] = $row['id'];
            }

            for ($i = 0; $i < sizeof($parray); ++$i) {
                $shp = new Planeten($parray[$i]);

                if (($shp->besitzer->allianz->id == 0 || ($shp->besitzer->allianz->id > 0 && $shp->besitzer->allianz->id != $this->besitzer->allianz->id)) && !in_array($this->besitzer->id, $shp->besitzer->vertrag('nap')) && !in_array($this->besitzer->id, $shp->besitzer->vertrag('verteidigung')) && (in_array($ziel3->besitzer->id, $shp->besitzer->vertrag('verteidigung')) || ($shp->besitzer->allianz->id > 0 && $shp->besitzer->allianz->id == $ziel3->besitzer->allianz->id) || ($shp->besitzer->id == $ziel3->besitzer->id))) {
                    $shp->feuern($this, 1 + $shp->defense * 10);
                }
            }
            for ($i = 0; $i < sizeof($sarray); ++$i) {
                $shp = &$sarray[$i];
                if (($shp->besitzer->allianz->id == 0 || ($shp->besitzer->allianz->id > 0 && $shp->besitzer->allianz->id != $this->besitzer->allianz->id)) && !in_array($this->besitzer->id, $shp->besitzer->vertrag('nap')) && !in_array($this->besitzer->id, $shp->besitzer->vertrag('verteidigung')) && (in_array($ziel3->besitzer->id, $shp->besitzer->vertrag('verteidigung')) || ($shp->besitzer->allianz->id > 0 && $shp->besitzer->allianz->id == $ziel3->besitzer->allianz->id) || ($shp->besitzer->id == $ziel3->besitzer->id))) {
                    $shp->feuern($this, 1 + $shp->defense * 10);
                }
            }
        }   // ENDE ALARM ROT 3
    }

    /**
     * Short description of method selbstzerstoerung.
     *
     * @author firstname and lastname of author, <author@example.org>
     *
     * @return int
     */
    public function zerstoerung()
    {
        $verbindung = get_verbindung();
        // pruefen auf quest(ab)geber
        $abfrage = mysqli_query($verbindung, "SELECT * FROM quests WHERE geber='".$this->id."' OR abgeber='".$this->id."'");
        if (mysqli_num_rows($abfrage) > 0) {
            echo 'Questgeber sind nicht zerst&ouml;bar!<br />';
            $this->hull = 1;
        } else {
            // ende pruefen auf qgeber
            $this->maxhull = 10;
            $this->hull = 5;
            $this->maxschilde = 0;
            $this->schilde = 0;
            $this->flotte = 0;
            $this->schildstatus = 0;
            $this->besitzer = new Account('2');
            $this->laser = 0;
            $this->alarmstufe = 'green';
            $this->bild = 'trumm.gif';
            $this->klasse = 'Tr&uuml;mmer';
            $this->loot = 1;

            // create frachtcontainer

            if ($this->frachtraum->gesamt() > 0) {
                $t_fracht = new Bauplan_Schiffe('Frachtcontainer');

                $randx = rand($this->position->x - 10, $this->position->x + 10);
                $randy = rand($this->position->y - 10, $this->position->y + 10);

                mysqli_query($verbindung, "insert into schiffe (energie,hull,warpkern,frachtraum,name,x,y,typ,system,klasse,besitzer) values ('".$t_fracht->maxenergie."','".$t_fracht->maxhull."','".$t_fracht->maxwarpkern."','".$this->frachtraum->dump()."','verlorene Fracht','".$randx."','".$randy."','s','".$this->position->system->id."','".$t_fracht->klasse."','2')") or exit($verbindung->error);
            }
        }
        mysqli_query($verbindung, "UPDATE schiffe SET dock=0 WHERE dock='$this->id'");
        mysqli_query($verbindung, "UPDATE schiffe SET loot=1,klasse='$this->klasse',hull='$this->hull',name='$this->name',besitzer=".$this->besitzer->id.",schildstatus='$this->schildstatus',schilde='$this->schilde',frachtraum='' WHERE id='$this->id'") or exit($verbindung->error);

        return 0;
    }

    private function einflugkoordinaten()
    {
        //	echo ' KOORDINATENZUWEISUNG ';
        $x = rand(1, 20);
        if ($x > 1 && $x < 20) {
            $y = rand(1, 2);
            if ($y == 2) {
                $y = 20;
            }
            $ruck = [$x, $y];

            return $ruck;
        }
        $y = rand(1, 20);
        $ruck = [$x, $y];

        return $ruck;
    }

    public function navigieren($richtung, $flotte, $test)
    {
        $verbindung = get_verbindung();
        if ($this->position->orbit == 1 && $richtung != 'v') {
            return 1;
        }
        if ($this->flotte > 0 && $flotte === false) {
            return 3;
        }
        if ($this->gondeln >= $this->maxgondeln) {
            return 4;
        }
        if ($this->dock > 0) {
            return 21;
        }

        // ENERGIEBEHANDLUNG
        $grundbetrag = $this->position->system->id == 0 ? 1 : 0.2;

        $grundbetrag = $grundbetrag * $this->flugkosten;

        // erzeuge alle 4 Felder oben unten links rechts
        $feldoben = new Weltraum($this->position->x, $this->position->y - 1, $this->position->system->id, $this->position->system->id > 0);
        $feldunten = new Weltraum($this->position->x, $this->position->y + 1, $this->position->system->id, $this->position->system->id > 0);
        $feldlinks = new Weltraum($this->position->x - 1, $this->position->y, $this->position->system->id, $this->position->system->id > 0);
        $feldrechts = new Weltraum($this->position->x + 1, $this->position->y, $this->position->system->id, $this->position->system->id > 0);
        // abhandlungen
        // auswahl switchvar
        if ($richtung == 'o') {
            $switchvar = $feldoben;
        }
        if ($richtung == 'u') {
            $switchvar = $feldunten;
        }
        if ($richtung == 'l') {
            $switchvar = $feldlinks;
        }
        if ($richtung == 'r') {
            $switchvar = $feldrechts;
        }

        $need = 0;

        if ($richtung == 'o' || $richtung == 'l' || $richtung == 'u' || $richtung == 'r') {
            $need = $grundbetrag * $switchvar->feld->einflugkosten;
        }

        if (!isset($need)) {
            $need = $grundbetrag;
        }
        if (!$switchvar->feld->passierbar && isset($switchvar)) {
            echo '<span class="error">Nicht passierbar!</span><br />';

            return '-1';
        }
        // ENERGIEBEHANDLUNG
        if ($this->energie < $need) {
            echo '<span class="error">Es werden ', $need, ' Energie ben&ouml;tigt um in das Feld einzufliegen!</span><br />';

            return '-1';
        }

        if ($test == 0) {
            if ($richtung == 'o' && $this->position->y > 1) {
                --$this->position->y;
                $this->energie -= $need;
                ++$this->gondeln;
            }
            if ($richtung == 'l' && $this->position->x > 1) {
                --$this->position->x;
                $this->energie -= $need;
                ++$this->gondeln;
            }
            if ($richtung == 'u' && (($this->position->y < 20 && $this->position->system > 0) || $this->position->system->id == 0)) {
                ++$this->position->y;
                $this->energie -= $need;
                ++$this->gondeln;
            }
            if ($richtung == 'r' && (($this->position->x < 20 && $this->position->system > 0) || $this->position->system->id == 0)) {
                ++$this->position->x;
                $this->energie -= $need;
                ++$this->gondeln;
            }
            if ($richtung == 'v') {
                $this->position->orbit = $this->position->orbit == 1 ? 0 : 1;
                $this->energie -= $need;
                ++$this->gondeln;
            }
            if ($richtung == 's') {
                if ($this->position->orbit == 1) {
                    return 1;
                }
                $erledigt = false;
                if ($this->position->system->id == 0 && !$erledigt) {
                    $abfrage = mysqli_query($verbindung, "SELECT * FROM systeme WHERE x='".$this->position->x."' AND y='".$this->position->y."' LIMIT 1");
                    while ($row = mysqli_fetch_array($abfrage)) {
                        $this->energie -= $need;
                        $this->position->system = new System($row['id']);
                        ++$this->gondeln;
                        // Flottenpruefung
                        if ($flotte === true) {
                            $fx = 0;
                            $fy = 0;
                            $testvar1 = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE flotte='".$this->flotte."' AND id!='".$this->id."' AND `system`='".$row['id']."' LIMIT 1");
                            while ($testrow1 = mysqli_fetch_array($testvar1)) {
                                $fx = $testrow1['x'];
                                $fy = $testrow1['y'];
                            }
                        }
                        if ($fx == 0 && $fy == 0) {
                            $flyin = $this->einflugkoordinaten();
                            $this->position->x = $flyin[0];
                            $this->position->y = $flyin[1];
                        } else {
                            $this->position->x = $fx;
                            $this->position->y = $fy;
                        }
                        $erledigt = true;
                    }
                }
                if ($this->position->system->id > 0 && !$erledigt) {
                    ++$this->gondeln;
                    $this->energie -= $need;
                    $this->position->x = $this->position->system->x;
                    $this->position->y = $this->position->system->y;
                    $this->position->system = new System('0');
                    $this->position->system->id = 0;
                    $erledigt = true;
                }
            }

            mysqli_query($verbindung, "UPDATE schiffe SET gondeln='".$this->gondeln."',energie='".$this->energie."',`system`='".$this->position->system->id."',x=".$this->position->x.',y='.$this->position->y.',orbit='.$this->position->orbit." WHERE id='$this->id'") or exit($verbindung->error);
            mysqli_query($verbindung, 'UPDATE counter SET count=count+1 WHERE id=1') or exit($verbindung->error);
            /* $abfrage2=mysql_query("SELECT * FROM schiffe WHERE id!='$this->id' AND `system`='".$this->position->system->id."' AND x=".$this->position->x." AND y=".$this->position->y." AND orbit=".$this->position->orbit);
              while($row2=mysql_fetch_array($abfrage2))
              {
              $eintrag=new Logbuch("typ","neu");
              $eintrag->x=$this->position->x;
              $eintrag->y=$this->position->y;
              $eintrag->zeit=date("Y-m-d H:i:s");
              $eintrag->`system`=$this->position->system->id;
              $eintrag->typ="Bewegung";
              $eintrag->klasse="Eingang";
              $eintrag->text="Einflug des Schiffes ".$this->name." (".$this->id.")";
              $eintrag->initiator=$this->besitzer->id;
              $eintrag->betroffener=$row2["besitzer"];
              $eintrag->save();
              }
              //Horchposten
              /*
              $lovevar=$_SESSION["Id"];
              $horcharray=array();
              $horchquery=mysql_query("SELECT * FROM schiffe WHERE besitzer!='$lovevar' AND klasse='Horchposten' AND x>=".($this->position->x-5)." AND x<= ".($this->position->x+5) ." AND y>= ".($this->position->y-5) ." AND y<=".($this->position->y+5));
              while($treffer=mysql_fetch_array($horchquery)) array_push($horcharray,$treffer["id"]);

              $datum=date("Y-m-d H:i:s");
              for($a=0;$a<sizeof($horcharray);$a++) { $ich=$horcharray[$a]; mysql_query("INSERT INTO horchlog (datum,img,besitzer,klasse,ich,x,y) VALUES ('$datum','$schiff->img','$schiff->besitzer','$schiff->klasse','$ich','$schiff->x','$schiff->y')") or die(mysql_error()); }

              $abfrage=mysql_query("SELECT * FROM weltraum WHERE (typ='b' OR typ='rs' OR typ='bs') AND x=".$this->position->x." AND y=".$this->position->y." AND `system`=".$this->position->system->id);
              while($tmp=mysql_fetch_array($abfrage)) $nebel=true;

              $sonnenquery=mysql_query("SELECT * FROM weltraum WHERE (typ='p' OR typ='x' OR typ='r1' OR typ='r2' OR typ='r3' OR typ='r4') AND `system`=".$this->position->system->id." AND x=".$this->position->x." AND y=".$this->position->y);
              while($sonne=mysql_fetch_array($sonnenquery))
              {
              echo 'Dein Schiff wurde zerst&ouml;rt!'; $this->zerstoerung();
              }

              mysql_query("UPDATE schiffe SET schildstatus='$this->schildstatus',energie='$this->energie',phaser='$this->phaser',gondeln='$this->gondeln',schilde='$this->schilde' WHERE id='$this->id'");
             */
            if (!$flotte) {
                $this->kampftick('0', null);
            }

            if (isset($switchvar) && $switchvar->feld->hide && $this->schildstatus == 1) {
                $this->schildstatus = 0;
                mysqli_query($verbindung, 'update schiffe set schildstatus=0 where id='.$this->id);
                echo '<span class="error">Du bist in '.$switchvar->name.' eingeflogen: Schilde ausgefallen.</span>';
            }
            if (isset($switchvar) && $switchvar->feld->deadly) {
                if ($this->besitzer->level == 5) {
                    echo 'Du bist in '.$switchvar->name.' eingeflogen. Dein Schiff wurde zerstört, zerquetscht und zermalmt.<br /><br />';
                    $this->zerstoerung();
                    $bu = new Button('schiffchoice.php', 'zur Schiffsübersicht');
                    $bu->printme();
                    exit;
                } else {
                    echo '<span class="error">Normalerweise wäre dein Schiff jetzt zerstört, da du aber noch nicht Level 5 erreicht hast, werden dem Schiff nur 20% der aktuellen Energie abgezogen</span>';
                    $this->energie = floor(($this->energie * 0.2) * 100) / 100;
                    mysqli_query($verbindung, 'update schiffe set energie='.$this->energie.' where id='.$this->id);
                }
            }
        }

        return 0;
    }

    public function docken($target)
    {
        $verbindung = get_verbindung();
        if ($this->position->x != $target->position->x || $this->position->y != $target->position->y || $this->position->orbit != $target->position->orbit || $this->position->system->id != $target->position->system->id) {
            return 16;
        }
        if ($this->energie == 0 && $this->dock == 0) {
            return 17;
        }
        if ($this->dock > 0 && $this->dock != $target->id) {
            return 18;
        }
        if (sizeof($target->dockfeld()) == $target->maxdock) {
            return 19;
        }
        if ($this->energie < 1) {
            return 0;
        }
        if ($target->schildstatus == 1) {
            return 20;
        }
        // berechnung
        if ($this->dock == $target->id) {
            $this->dock = 0;
            mysqli_query($verbindung, "UPDATE schiffe SET dock=0 WHERE id='".$this->id."'");
            echo 'abgedockt<br />';

            return 0;
        }
        if ($this->dock == 0) {
            $this->dock = $target->id;
            --$this->energie;
            mysqli_query($verbindung, "UPDATE schiffe SET dock='".$this->dock."',energie=energie-1 WHERE id='".$this->id."'");

            return 0;
        }

        return 14;
    }

    public function tarnen()
    {
        $verbindung = get_verbindung();
        if ($this->skill->tarnung != 1) {
            return 9;
        }
        if ($this->energie <= 0 && $this->tarnung == 0) {
            return 10;
        }
        $this->tarnung = $this->tarnung == 0 ? 1 : 0;
        if ($this->tarnung == 1) {
            --$this->energie;
        }
        $this->schildstatus = 0;
        mysqli_query($verbindung, "UPDATE schiffe SET energie='$this->energie',tarnung='$this->tarnung',schildstatus='$this->schildstatus' WHERE id = '$this->id'");

        return 0;
    }

    public function transwarp($x, $y)
    {
        $returnValue = (int) 0;

        // section -64--88--78-35--2b61c75d:11af3191905:-8000:0000000000000896 begin
        // section -64--88--78-35--2b61c75d:11af3191905:-8000:0000000000000896 end

        return (int) $returnValue;
    }

    public function einsaugen($was, $anzahl)
    {
        $verbindung = get_verbindung();
        if ($this->skill->deuterium == 0 && $was == 'deuterium') {
            return 12;
        }
        if ($this->skill->erz == 0 && $was == 'erz') {
            return 12;
        }
        if ($this->energie == 0) {
            return 13;
        }
        // XXXX
        // determine on what field we stand

        $cur_field = new Weltraum($this->position->x, $this->position->y, $this->position->system->id, $this->position->system->id > 0);

        if (!ctype_digit($anzahl)) {
            $anzahl = $this->energie;
        }
        switch ($was) {
            case 'erz':
                $rverteiler = 2;
                break;
            case 'deuterium':
                $rverteiler = 8;
                break;
            default:
                $limes = 0;
                break;
        }

        $limes = $cur_field->feld->deut;

        if ($anzahl > $this->energie) {
            $anzahl = $this->energie;
        }
        if ($anzahl * $limes > $this->frachtraum->max - $this->frachtraum->gesamt()) {
            $anzahl = ceil(($this->frachtraum->max - $this->frachtraum->gesamt()) / $limes);
        }
        if ($anzahl < 0) {
            $anzahl = 0;
        }

        $this->energie -= $anzahl;
        $this->frachtraum->fracht[$rverteiler]->anzahl += ($anzahl * $limes);

        mysqli_query($verbindung, "UPDATE schiffe SET energie='".$this->energie."' WHERE id='".$this->id."'");
        $this->frachtraum->save();

        // QUEST abfrage

        echo $anzahl * $limes, ' ', $rverteiler == 2 ? 'Erz' : 'Deuterium', ' einesaugt!<br />';
        // XXXX
    }

    // angedockt
    public function dockfeld()
    {
        $verbindung = get_verbindung();
        $returnarray = [];
        if ($this->skill->basis == 0) {
            return 15;
        }
        $dockabfrage = mysqli_query($verbindung, "SELECT id FROM schiffe WHERE dock='".$this->id."'");
        while ($dockrow = mysqli_fetch_array($dockabfrage)) {
            $returnarray[] = $dockrow[0];
        }

        return $returnarray;
    }
}

/* end of class Schiffe */

// Ticket klasse
class Ticket
{
    public $id;
    public $titel;
    public $nachricht;
    public $assignedTo;
    public $createdBy;
    public $status;
    public $comments = [];
    public $statusArr = [
        'geschlossen',
        'unbeantwortet',
        'wird demn&auml;chst repariert',
        'wird sp&auml;ter repariert',
        'bekannter Bug (geschlossen)',
    ];

    /*
     * 0 = closed ( fixed )
     * 1 = open ( no new answer )
     * 2 = recognized ( will fix soon )
     * 3 = recognized ( will fix later )
     * 4 = known issue ( will fix someday )
     */

    public function __construct($i)
    {
        $verbindung = get_verbindung();
        if ($i == 0 || $i == '') {
            // create new ticket
            mysqli_query($verbindung, "INSERT INTO tickets (titel,nachricht,ast,cr,status,comments) VALUES ('','',0,0,0,'')") or exit($verbindung->error);
            $this->id = mysqli_insert_id($verbindung);

            return;
        }
        if (ctype_digit($i) && $i > 0) {
            // init stuff
            $this->id = $i;
            $q = mysqli_query($verbindung, 'SELECT * FROM tickets WHERE id = '.$this->id);
            while ($r = mysqli_fetch_array($q)) {
                $this->titel = $r['titel'];
                $this->nachricht = $r['nachricht'];
                $this->assignedTo = new Account($r['ast']);
                $this->createdBy = new Account($r['cr']);
                $this->status = $r['status'];
                $this->comments = explode('|', $r['comments']);
            }
        }
    }

    public function reassign($re_id)
    {
        // TODO
    }

    public function saveToDB()
    {
        $verbindung = get_verbindung();
        $commentstring = '';
        mysqli_query($verbindung, "UPDATE tickets SET titel='".$this->titel."',nachricht='".$this->nachricht."',ast='".$this->assignedTo->id."',cr='".$this->createdBy->id."',status='".$this->status."',comments='".$commentstring."' WHERE id = ".$this->id) or exit($verbindung->error);
    }
}

// Feld klasse
class Feld
{
    public $x = 0;
    public $y = 0;
    public $system = 0;
    public $was = '';
    public $typ = '';

    public function __construct($x, $y, $system)
    {
        $verbindung = get_verbindung();
        if ($system == 0) {
            $abfrage = mysqli_query($verbindung, "SELECT * FROM systeme WHERE x='$x' AND y='$y'");
            while ($row = mysqli_fetch_array($abfrage)) {
                $this->was = 'System';
            }
            $abfrage = mysqli_query($verbindung, "SELECT * FROM weltraum WHERE x='$x' AND y='$y' AND `system`=0");
            while ($row = mysqli_fetch_array($abfrage)) {
                $this->was = 'Weltraum';
                $this->typ = $row['typ'];
            }
        }
        if ($system > 0) {
            $abfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE x='$x' AND y='$y' AND `system`='$system'");
            while ($row = mysqli_fetch_array($abfrage)) {
                $this->was = 'Planet';
            }
            $abfrage = mysqli_query($verbindung, "SELECT * FROM weltraum WHERE x='$x' AND y='$y' AND `system`='$system'");
            while ($row = mysqli_fetch_array($abfrage)) {
                $this->was = 'Weltraum';
                $this->typ = $row['typ'];
            }
        }
    }
}

class Vertrage
{
    public $initiator;
    public $partner;
    public $nap = false;
    public $def = false;
    public $handel = false;
    public $krieg = false;

    public function aufloesen()
    {
        $returnValue = (int) 0;

        // section -64--88--78-35--5cf53369:11af376d924:-8000:00000000000008D4 begin
        // section -64--88--78-35--5cf53369:11af376d924:-8000:00000000000008D4 end

        return (int) $returnValue;
    }

    public function annehmen()
    {
        $returnValue = (int) 0;

        // section -64--88--78-35--5cf53369:11af376d924:-8000:00000000000008D6 begin
        // section -64--88--78-35--5cf53369:11af376d924:-8000:00000000000008D6 end

        return (int) $returnValue;
    }

    public function ablehnen()
    {
        $returnValue = (int) 0;

        // section -64--88--78-35--5cf53369:11af376d924:-8000:00000000000008D9 begin
        // section -64--88--78-35--5cf53369:11af376d924:-8000:00000000000008D9 end

        return (int) $returnValue;
    }

    public function anbieten($vertrag)
    {
        $returnValue = (int) 0;

        // section -64--88--78-35--5cf53369:11af376d924:-8000:00000000000008DB begin
        // section -64--88--78-35--5cf53369:11af376d924:-8000:00000000000008DB end

        return (int) $returnValue;
    }
}

/* end of class Vertrage */

class Skill
{
    public $deuterium = 0;
    public $erz = 0;
    public $transwarp = 0;
    public $tarnung = 0;
    public $basis = 0;
    public $bauen = 0;
}

class Warenkonto extends Frachtraum
{
    // --- ATTRIBUTES ---
    // --- OPERATIONS ---
}

/* end of class Warenkonto */

class Planetenfeld
{
    public $id;
    public $name;
    public $bild;

    public function __construct($id)
    {
        $verbindung = get_verbindung();
        $this->id = $id;
        $q = mysqli_query($verbindung, 'select * from planetenfelder where id ='.$id);
        while ($r = mysqli_fetch_array($q)) {
            $this->name = $r['name'];
            $this->bild = $r['bild'];
        }
    }
}

class Planetenfelder
{
    public $liste;

    public function __construct()
    {
        $verbindung = get_verbindung();
        $res = [];
        $q = mysqli_query($verbindung, 'select * from planetenfelder');
        while ($r = mysqli_fetch_array($q)) {
            $t = new Planetenfeld($r["id"]);
            #$t->id = $r['id'];
            #$t->name = $r['name'];
            #$t->bild = $r['bild'];
            #$res[] = $t;
        }
    }
}

class Bauplan_Gebaude
{
    public $id = 0;
    public $name;
    public $bild;
    public $untergrund;
    public $baukosten;
    public $braucht;
    public $produziert;
    public $bauzeit;
    public $lager;
    public $epslager;
    public $schilde;
    public $laser;
    public $wichtung = 1;
    public $prequest;
    public $prelevel;
    public $forschung = false;
    public $preforschung;
    public $werft = false;
    public $sonstiges;

    public function __construct($id)
    {
        $verbindung = get_verbindung();
        $q = mysqli_query($verbindung, 'select * from gebaude where id='.$id) or exit(mysqli_error($verbindung));
        while ($r = mysqli_fetch_array($q)) {
            $this->id = $id;
            $this->name = $r['name'];

            $this->wichtung = $r['punktwichtung'];
            $this->lager = $r['lager'];
            $this->epslager = $r['epslager'];
            $this->schilde = $r['schilde'];
            $this->laser = $r['laser'];
            $this->sonstiges = $r['sonstiges'];
            $this->forschung = (bool) $r['forschung'];
            $this->werft = (bool) $r['werft'];

            $this->prequest = explode('/', $r['prequest']);
            $this->preforschung = explode('/', $r['preforschung']);
            $this->prelevel = $r['prelevel'];

            $tvar = explode('/', $r['bild']);
            for ($i = 0; $i < sizeof($tvar); ++$i) {
                $this->bild[] = $tvar[$i];
            }

            $tvar = explode('/', $r['untergrund']);
            for ($i = 0; $i < sizeof($tvar); ++$i) {
                $this->untergrund[] = new Planetenfeld($tvar[$i]);
            }

            $this->baukosten = new Frachtraum($r['baukosten'], 'dummy');
            $this->braucht = new Frachtraum($r['braucht'], 'dummy');
            $this->produziert = new Frachtraum($r['produziert'], 'dummy');
            $this->bauzeit = $r['bauzeit'];
        }
    }

    public static function getListe($untergrund)
    {
        $verbindung = get_verbindung();
        // donts, gebäude die nicht gebaut werden können
        // Basiskupel,Kolozentrale, (NPC Gebäude)
        $user = new Account($_SESSION['Id']);

        $donts = [18, 19];
        $list = [];
        $q = mysqli_query($verbindung, "select id from gebaude where prelevel < 6 and (untergrund like '%/".$untergrund."/%' or untergrund like '".$untergrund."/%' or untergrund like '%/".$untergrund."' or untergrund like '".$untergrund."') and ( preforschung = '' || preforschung = '0' || ( preforschung in (select fid from mapforschung where status=1 and uid='".$user->id."') ) ) ");

        while ($r = mysqli_fetch_array($q)) {
            if (!in_array($r['id'], $donts)) {
                $temp = new Bauplan_Gebaude($r['id']);
                $list[] = $temp;
            }
        }

        return $list;
    }

    public static function getCompleteListe()
    {
        $verbindung = get_verbindung();
        $user = new Account($_SESSION['Id']);

        $list = [];
        $q = mysqli_query($verbindung, 'select id from gebaude where prelevel < '.($user->level + 1));
        while ($r = mysqli_fetch_array($q)) {
            $temp = new Bauplan_Gebaude($r['id']);
            $list[] = $temp;
        }

        return $list;
    }
}

class Menge
{
    public $content;
    public $sentinel = 0;

    public function add($elem)
    {
        if (!$this->in_Menge($elem)) {
            $this->content[$this->sentinel] = $elem;
            ++$this->sentinel;
        }
    }

    public function del($elem)
    {
        $index = -1;
        // search place of old elem
        for ($i = 0; $i < $this->sentinel; ++$i) {
            if ($this->content[$i] == $elem) {
                $index = $i;
            }
        }

        if ($index > -1) {
            $this->content[$index] = $this->content[$this->sentinel - 1];
            --$this->sentinel;

            return true;
        } else {
            return false;
        }
    }

    public function in_Menge($elem)
    {
        for ($i = 0; $i < $this->sentinel; ++$i) {
            if ($this->content[$i] == $elem) {
                return true;
            }
        }

        return false;
    }

    public function is_Empty()
    {
        return $this->sentinel == 0;
    }

    public function dump()
    {
        echo '[';
        for ($i = 0; $i < $this->sentinel; ++$i) {
            echo $this->content[$i].',';
        }
        echo ']';
    }
}

class node
{
    public $data;
    public $next;
}

class Systemfelder
{
    public $id;
    public $name;
    public $bild;

    public function __construct($index)
    {
        $verbindung = get_verbindung();
        $q = 0;
        if (ctype_digit($index)) {
            $q = mysqli_query($verbindung, 'select * from systemfelder where id='.$index);
        } else {
            $q = mysqli_query($verbindung, "select * from systemfelder where name='".$index."'");
        }
        while ($r = mysqli_fetch_array($q)) {
            $this->id = $r['id'];
            $this->name = $r['name'];
            $this->bild = $r['bild'];
        }
    }

    public static function getList()
    {
        $verbindung = get_verbindung();
        $l = [];
        $q = mysqli_query($verbindung, 'select id from systemfelder');
        while ($r = mysqli_fetch_array($q)) {
            $l[] = new Systemfelder($r['id']);
        }

        return $l;
    }
}

class Weltraum
{
    public $id;
    public $x;
    public $y;
    public $system;
    public $shipsystem = false;
    public $tooltip;
    public $bild;
    public $feld;
    public $name;
    public $ziel;

    public function __construct($x, $y, $system, $shipsystem)
    {
        $verbindung = get_verbindung();
        $this->shipsystem = $shipsystem;
        $this->x = $x;
        $this->y = $y;
        $this->system = $system;

        $q = mysqli_query($verbindung, 'select typ,zielx,ziely,zielsystem from weltraum where x='.$this->x.' and y='.$this->y.' and `system`='.$this->system);
        while ($r = mysqli_fetch_array($q)) {
            $this->feld = new Weltraumfelder($r['typ']);
            if ($this->feld->wurmloch) {
                $this->ziel = new Position(null, null);
                $this->ziel->system = $r['zielsystem'];
                $this->ziel->x = $r['zielx'];
                $this->ziel->y = $r['ziely'];
            }
        }
        if (!$this->feld) {
            $this->feld = new Weltraumfelder(0);
        }

        // planeten
        $q = mysqli_query($verbindung, 'select besitzer,bild,typ from planeten where x='.$this->x.' and y='.$this->y.' and `system`='.$this->system);
        while ($r = mysqli_fetch_array($q)) {
            $this->bild = 'misc/'.$r['bild'];
            $this->name = 'Klasse ';
            if (strlen($r['typ']) == 2) {
                // mond
                $this->name .= strtoupper($r['typ'][0]).' Mond';
            } else {
                // Planet
                $this->name .= strtoupper($r['typ']).' Planet';
            }

            $this->tooltip = ($r['besitzer'] == 2 ? '<span style=\\\'color:green;font-weight:bold;\\\'>unbesiedelt</span>' : '<span style=\\\'color:red;font-weight:bold;\\\'>besiedelt</span>');
            // mond oder planet
        }

        // systeme

        if (!$this->shipsystem) {
            $q = mysqli_query($verbindung, 'select id from systeme where x='.$this->x.' and y='.$this->y);
            while ($r = mysqli_fetch_array($q)) {
                $sys = new System($r['id']);
                $this->bild = 'systems/'.$sys->bild;
                $this->name = $sys->name;
                $this->tooltip = 'Enter me!';
            }
        }

        if ($this->feld->id == 0) {
            $first = false;
            $q = mysqli_query($verbindung, "select id from schiffe where klasse='Handelsschiff' and x=".$this->x.' and y='.$this->y.' and `system`='.$this->system);
            if (mysqli_num_rows($q) > 0) {
                $this->bild = 'ships/allferengi.png';
                $this->name = 'Ferengi-Handelsschiff';
                $this->tooltip = 'Warenbörse';
                $first = true;
            }

            $q = mysqli_query($verbindung, "select id from schiffe where klasse='Raumdock' and x=".$this->x.' and y='.$this->y.' and `system`='.$this->system);
            if (mysqli_num_rows($q) > 0) {
                if (!$first) {
                    $this->bild = 'ships/fedplace3.png';
                    $this->name = 'Föderations-Raumdock';
                    $this->tooltip = 'Aushilfe';
                } else {
                    $this->bild = 'ships/fedfergplace3.png';
                    $this->name = 'Raumdock mit Handelsschiff';
                    $this->tooltip = 'Warenbörse und Aushilfe';
                }
            }
        }

        if (!$this->tooltip) {
            $this->tooltip = $this->feld->tooltip;
        }

        if (!$this->bild) {
            $this->bild = $this->feld->bild;
        }

        if (!$this->name) {
            $this->name = $this->feld->name;
        }
    }

    public function getShips()
    {
        $verbindung = get_verbindung();
        $l = [];
        $q = mysqli_query($verbindung, "select id from schiffe where typ='s' and x=".$this->x.' and y='.$this->y.' and `system`='.$this->system);
        while ($r = mysqli_fetch_array($q)) {
            $s = new Schiffe($r['id']);
            $l[] = $s;
        }

        return $l;
    }

    public function getNumberofShips()
    {
        $verbindung = get_verbindung();

        // $add forschung level quests?
        $add = ' and tarnung = 0';

        $q = mysqli_query($verbindung, 'select * from schiffe where x='.$this->x.' and y='.$this->y.' and `system`='.$this->system.$add);

        return mysqli_num_rows($q);
    }
}

class Weltraumfelder
{
    public $id;
    public $name;
    public $bild;
    public $einflugkosten;
    public $passierbar;
    public $beschreibung;
    public $tooltip;
    public $erz;
    public $deut;
    public $bebaubar;
    public $deadly;
    public $energieverlust;
    public $hide;
    public $wurmloch = false;

    public function __construct($id)
    {
        $verbindung = get_verbindung();
        $this->id = $id;
        $q = mysqli_query($verbindung, 'select * from weltraumfelder where id ='.$id);
        while ($r = mysqli_fetch_array($q)) {
            $this->name = $r['name'];
            $this->bild = $r['bild'];
            $this->einflugkosten = $r['einflugkosten'];
            $this->passierbar = (bool) $r['passierbar'];
            $this->beschreibung = $r['beschreibung'];
            $this->tooltip = $r['tooltip'];
            $this->erz = $r['erz'];
            $this->deut = $r['deut'];
            $this->bebaubar = (bool) $r['bebaubar'];
            $this->deadly = (bool) $r['deadly'];
            $this->energieverlust = $r['energieverlust'];
            $this->hide = (bool) $r['hide'];
            $this->wurmloch = (bool) $r['wurmloch'];
        }
    }

    public static function getList()
    {
        $verbindung = get_verbindung();
        $l = [];
        $q = mysqli_query($verbindung, 'select * from weltraumfelder order by id');
        while ($r = mysqli_fetch_array($q)) {
            $dummy = new Weltraumfelder($r['id']);
            $l[] = $dummy;
        }

        return $l;
    }
}

class Gebaude
{
    public $id = 0; // PID
    public $fid = 0;
    public $name;
    public $bau;
    public $bild;
    public $untergrund;
    public $aktiv = 0;
    public $hull = 50;
    public $rest_bauzeit = 0;
    public $t_index = -1;

    public function __construct($pid, $fid)
    {
        $verbindung = get_verbindung();
        $this->fid = $fid;
        $this->id = $pid;
        $q = mysqli_query($verbindung, 'select feld'.$fid.' from planet2 where id ='.$pid);
        $rawdata = [];
        while ($r = mysqli_fetch_array($q)) {
            $rawdata = explode('/', $r['feld'.$fid]);
        }

        $this->untergrund = new Planetenfeld($rawdata[1]);
        $this->bau = new Bauplan_Gebaude($rawdata[0]);
        $this->rest_bauzeit = $rawdata[2];
        $this->hull = $rawdata[3];
        $this->aktiv = $rawdata[4];

        $this->name = ($this->bau->id == null ? $this->untergrund->name : $this->bau->name);

        if ($this->bau->id == null) {
            $this->bild = $this->untergrund->bild;
        } else {
            // get correct imgae index
            $t_index = -1;
            for ($i = 0; $i < sizeof($this->bau->untergrund); ++$i) {
                if ($this->bau->untergrund[$i]->id == $this->untergrund->id) {
                    $t_index = $i;
                }
            }
            $this->bild = $this->bau->bild[$t_index];
            $this->t_index = $t_index;
        }
    }

    public function save()
    {
        $verbindung = get_verbindung();
        $dbstring = $this->bau->id.'/'.$this->untergrund->id.'/'.$this->rest_bauzeit.'/'.$this->hull.'/'.$this->aktiv;
        mysqli_query($verbindung, 'update planet2 set feld'.$this->fid."='".$dbstring."' where id =".$this->id);
    }
}

/* class Planetenfeld {

  public $pid = 0;
  public $fid = 0;
  public $was = 0;
  public $bauzeit = 0;
  public $untergrund = "";
  public $hull = 60;
  public $aktiv = 0;

  function __construct($pid, $fid) {
  $abfrage = mysql_query("SELECT * FROM planet2 WHERE id='$pid'");
  while ($row = mysql_fetch_array($abfrage)) {
  $this->pid = $pid;
  $this->fid = $fid;
  splitfeld($row["feld" . (int) $fid], $a, $b, $c, $d, $e);
  $this->was = $a;
  $this->bauzeit = $b;
  $this->untergrund = $c;
  $this->hull = $d;
  $this->aktiv = $e;
  }
  }

  function save() {
  mysql_query("UPDATE planet2 SET feld" . $this->fid . " = '" . $this->was . "-" . $this->bauzeit . "-" . $this->untergrund . "-" . $this->hull . "-" . $this->aktiv . "' WHERE id='" . $this->pid . "'") or die(mysql_error());
  //echo "UPDATE planet2 SET feld".$this->fid." = '".$this->was."-".$this->bauzeit."-".$this->untergrund."-".$this->hull."-".$this->aktiv." WHERE pid='".$this->pid."'";
  }

  } */

class Konto
{
    public $id;
    public $frachtraum;

    public function __construct($id)
    {
        // $tt = mysql_query("SELECT * FROM konto WHERE besitzer='$id'");
        // while ($t = mysql_fetch_array($tt)) {
        $this->id = $id;
        $this->frachtraum = new Frachtraum($id, 'konto');
        // }
    }

    public function save()
    {
        $this->frachtraum->save();
    }
}
