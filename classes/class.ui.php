<?php
include_once dirname(__DIR__) . '/connect.php';

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

