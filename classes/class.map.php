<?php
include_once dirname(__DIR__) . '/connect.php';

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

