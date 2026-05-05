<?php
include_once dirname(__DIR__) . '/connect.php';

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

    public $abgabetext = '';

    public function __construct($qqid)
    {
        $verbindung = get_verbindung();
        $abfrage = mysqli_query($verbindung, "SELECT * FROM erfolge,quests WHERE erfolge.id='".$qqid."' AND quests.id=erfolge.qid") or exit('error');
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
            for ($j = 0; $j < count($t_var); ++$j) {
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

