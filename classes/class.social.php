<?php
include_once dirname(__DIR__) . '/connect.php';

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
        $q = mysqli_query($verbindung, "select id from channel where public=1 or id in (select cid from channelabo where status=1 and uid='".session_id()."') or   founder = ".intval(\$_SESSION['Id']).'            order by id') or exit(mysqli_error($verbindung));
        while ($r = mysqli_fetch_array($q)) {
            $dummy = new Channel($r['id']);
            $l[] = $dummy;
        }

        return $l;
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

