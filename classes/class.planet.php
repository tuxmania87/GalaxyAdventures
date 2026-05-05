<?php
include_once dirname(__DIR__) . '/connect.php';

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
            $t = new Planetenfeld($r['id']);
            // $t->id = $r['id'];
            // $t->name = $r['name'];
            // $t->bild = $r['bild'];
            // $res[] = $t;
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
            for ($i = 0; $i < count($tvar); ++$i) {
                $this->bild[] = $tvar[$i];
            }

            $tvar = explode('/', $r['untergrund']);
            for ($i = 0; $i < count($tvar); ++$i) {
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
            for ($i = 0; $i < count($this->bau->untergrund); ++$i) {
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
  $abfrage = mysqli_query($verbindung, "SELECT * FROM planet2 WHERE id='$pid'");
  while ($row = mysqli_fetch_array($abfrage)) {
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
  mysqli_query($verbindung, "UPDATE planet2 SET feld" . $this->fid . " = '" . $this->was . "-" . $this->bauzeit . "-" . $this->untergrund . "-" . $this->hull . "-" . $this->aktiv . "' WHERE id='" . $this->pid . "'") or die(mysqli_error($verbindung));
  //echo "UPDATE planet2 SET feld".$this->fid." = '".$this->was."-".$this->bauzeit."-".$this->untergrund."-".$this->hull."-".$this->aktiv." WHERE pid='".$this->pid."'";
  }

  } */

