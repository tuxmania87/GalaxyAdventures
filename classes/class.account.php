<?php
include_once dirname(__DIR__) . '/connect.php';

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
            // mysqli_query($verbindung, 'INSERT INTO logbuch () VALUES ()');
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

