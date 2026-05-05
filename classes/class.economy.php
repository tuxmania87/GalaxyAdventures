<?php
include_once dirname(__DIR__) . '/connect.php';

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

class Warenkonto extends Frachtraum
{
    // --- ATTRIBUTES ---
    // --- OPERATIONS ---
}

/* end of class Warenkonto */

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

class Konto
{
    public $id;
    public $frachtraum;

    public function __construct($id)
    {
        // $tt = mysqli_query($verbindung, "SELECT * FROM konto WHERE besitzer='$id'");
        // while ($t = mysqli_fetch_array($tt)) {
        $this->id = $id;
        $this->frachtraum = new Frachtraum($id, 'konto');
        // }
    }

    public function save()
    {
        $this->frachtraum->save();
    }
}

