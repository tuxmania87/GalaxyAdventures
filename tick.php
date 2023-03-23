<?php

$hkontrolle = date("H");
ignore_user_abort(true);

include("klassen.php");
include_once("connect.php");


$verindung = get_verbindung();

//tick anmelden
$anfangminuten = date("i");
$anfangsek = date("s");
$anfang = $anfangminuten * 60 + $anfangsek;


$datum = date("Y-m-d H:i:s");
$ip = $_SERVER['REMOTE_ADDR'];
mysqli_query($verindung,"INSERT INTO `ticklog` (datum,ip,status) VALUES ('$datum','$ip','1')");


//globale Accountänderung
mysqli_query($verindung,"UPDATE account SET mitglied=mitglied+1");
mysqli_query($verindung,"UPDATE account SET inaktiv=inaktiv+1");

//Output auf schiffen / energie
$outarray = array();
$abfrage = mysqli_query($verindung,"SELECT * FROM schiffe s, bauplan b WHERE s.klasse = b.klasse and besitzer!=2 AND energie<maxenergie AND (warpkern>0 AND warpkernstatus=1)") or die(mysqli_error($verindung));
while ($energie = mysqli_fetch_array($abfrage))
    $outarray[] = $energie["id"];

for ($g = 0; $g < sizeof($outarray); $g++) {
    $eschiff = new Schiffe($outarray[$g]);
    echo $eschiff->id." <br />";
    $eamount = $eschiff->energieoutput;
    //aus dem warpkern nehmen
    while ($eschiff->energie < $eschiff->maxenergie && $eschiff->warpkern > 0 && $eamount > 0 && $eschiff->warpkernstatus == 1) {
        $eschiff->energie++;
        $eschiff->warpkern--;
        $eamount--;
    }

    mysqli_query($verindung,"UPDATE schiffe SET warpkern='" . $eschiff->warpkern . "',energie='" . $eschiff->energie . "' WHERE id='" . $eschiff->id . "'");
}

//forschung allgemein TODO
mysqli_query($verindung,"update mapforschung set status=status-1 where status>1");

//schiffe ausbauen
mysqli_query($verindung,"UPDATE schiffe SET frachtraum=frachtraum-1 WHERE typ='' AND frachtraum>0");
mysqli_query($verindung,"UPDATE schiffe SET typ='s',frachtraum='0/0/0/0/0/0/0/0/50' WHERE typ='' AND frachtraum=0");

//planetenberechnung
//pre tick berechnung
//allgemeine Graphberechung

$nodelist = array();
$liste = Bauplan_Gebaude::getCompleteListe();


//reverse map  Res -> Factory
$map = array();
for ($i = 0; $i < sizeof($liste); $i++) {
    for ($j = 0; $j < sizeof($liste[$i]->produziert->fracht); $j++) {
        if ($liste[$i]->produziert->fracht[$j]->anzahl > 0) {
            $map[$liste[$i]->produziert->fracht[$j]->id][] = $liste[$i]->id;
        }
    }
}



//while(sizeof($already_leaf < $prod_count)) {
//for ($ssd = 0; $ssd < 2; $ssd++) {
for ($i = 0; $i < sizeof($liste); $i++) {


    //is_productive?
    $produktiv = false;
    for ($j = 1; $j < sizeof($liste[$i]->produziert->fracht); $j++) {
        if ($liste[$i]->produziert->fracht[$j]->anzahl > 0) {
            $produktiv = true;
        }
    }

    if ($produktiv) {
        //echo $liste[$i]->name." -- ".$liste[$i]->id." <br />";
        $n = new node();
        $n->data = $liste[$i]->id;
        $n->next = new Menge();

        for ($j = 1; $j < sizeof($liste[$i]->braucht->fracht); $j++) {
            if ($liste[$i]->braucht->fracht[$j]->anzahl > 0) {

                for ($h = 0; $h < sizeof($map[$liste[$i]->braucht->fracht[$j]->id]); $h++) {
                    $n->next->add($map[$liste[$i]->braucht->fracht[$j]->id][$h]);
                }
            }
        }
        $nodelist[] = $n;
    }
}

$leaf_nodes = array();
$round_nodes = array(0);
$runden = array();

while (sizeof($round_nodes) > 0) {

    $round_nodes = array();

    for ($i = 0; $i < sizeof($nodelist); $i++) {
        if ($nodelist[$i]->next->is_Empty() && !in_array($nodelist[$i]->data, $leaf_nodes)) {
            $round_nodes[] = $nodelist[$i]->data;
            //$leaf_nodes[] = $nodelist[$i]->data;
        }
    }

    $runden[] = $round_nodes;

    //prepare for next round
    for ($i = 0; $i < sizeof($nodelist); $i++) {
        if (!$nodelist[$i]->next->is_Empty()) {
            for ($j = 0; $j < sizeof($round_nodes); $j++) {
                $nodelist[$i]->next->del($round_nodes[$j]);
            }
        }
    }
    $leaf_nodes = array_merge($leaf_nodes, $round_nodes);
}




$q = mysqli_query($verindung,"select id from planeten where besitzer != 2");
while ($r = mysqli_fetch_array($q)) {

    $planet = new Planeten($r["id"]);


//first get energy
    $t_fracht = array();
    $glob_energy = 0;
//get energy producters
    for ($i = 1; $i < sizeof($planet->feld); $i++) {
        if ($planet->feld[$i]->bau->produziert->fracht[0]->anzahl > 0 &&
                $planet->feld[$i]->aktiv == 1 && $planet->feld[$i]->rest_bauzeit == 0) {
            $create = true;
            $t_array = array();
            //echo " <br />" . $planet->feld[$i]->bau->name;
            for ($j = 0; $j < sizeof($planet->frachtraum->fracht); $j++) {
                if ($planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl > 0) {
                    //echo $planet->feld[$i]->bau->braucht->fracht[$j + 1]->name;
                    if ($planet->frachtraum->fracht[$j]->anzahl < $planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl) {
                        $create = false;
                        break;
                    } else {
                        $t_array[$planet->feld[$i]->bau->braucht->fracht[$j + 1]->id] -= $planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl;
                    }
                }
            }
            if (!$create) {
                $t_array = array();
            } else {
                foreach ($t_array as $key => $value) {
                    echo $planet->frachtraum->fracht[$key]->anzahl . " " . $key . " " . $value . " <br />";
                    $planet->frachtraum->fracht[$key - 1]->anzahl+=$value;
                    $t_fracht[$key] += $value;
                }
                $glob_energy += $planet->feld[$i]->bau->produziert->fracht[0]->anzahl;
            }
        }
    }

//DEBUG echo "E: ".$glob_energy;

    $cp_array = array_fill(0, sizeof($planet->frachtraum->fracht) + 1, 0);

    foreach ($t_fracht as $key => $value) {
        $cp_array[$key] = $value;
    }
    $cp_array[0] = $glob_energy;

    $dummy_fracht = implode("/", $cp_array);

    $addfracht = new Frachtraum($dummy_fracht, "dummy");


    if ($planet->id == 154) {
        echo "DUMP_BEFORE:<br />";
        $planet->frachtraum->dump();
    }

    //TODO: round_nodes contains building ID that have to be handeled by
    // Iterating over planet field list in linear time times O(n)= n * log n
    for ($k = 0; $k < sizeof($runden); $k++) {
        for ($i = 1; $i < sizeof($planet->feld); $i++) {
            if (in_array($planet->feld[$i]->bau->id, $runden[$k]) && $planet->feld[$i]->aktiv == 1 && $planet->feld[$i]->rest_bauzeit == 0) {
                echo $planet->id . " -- " . $planet->feld[$i]->name . "\n";
                //do we have all necessairy stuff?
                $stuff_control = true;
                for ($j = 0; $j < sizeof($planet->frachtraum->fracht); $j++) {
                    if ($planet->frachtraum->fracht[$j]->anzahl < $planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl) {
                        $stuff_control = false;
                    }
                }
                if ($planet->energie + $glob_energy < $planet->feld[$i]->bau->braucht->fracht[0]->anzahl)
                    $stuff_control = false;


                if ($stuff_control) {
                    //yep we can build it
                    for ($j = 0; $j < sizeof($planet->frachtraum->fracht); $j++) {
                        echo "we handle " . $planet->feld[$i]->bau->name . " in k=" . $k . " i=" . $i . " j=" . $j . "\n";
                        echo $planet->feld[$i]->bau->produziert->fracht[$j + 1]->name . " -- " . $planet->feld[$i]->bau->produziert->fracht[$j + 1]->anzahl . "\n";
                        $planet->frachtraum->fracht[$j]->anzahl += $planet->feld[$i]->bau->produziert->fracht[$j + 1]->anzahl;
                        $planet->frachtraum->fracht[$j]->anzahl -= $planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl;
                        $addfracht->fracht[$j + 1]->anzahl += $planet->feld[$i]->bau->produziert->fracht[$j + 1]->anzahl;
                        $addfracht->fracht[$j + 1]->anzahl -= $planet->feld[$i]->bau->braucht->fracht[$j + 1]->anzahl;
                    }

                    print $glob_energy."\n";
                    var_dump($planet->feld[$i]->bau->braucht->fracht[0]);

                    $glob_energy -= $planet->feld[$i]->bau->braucht->fracht[0]->anzahl;
                    $addfracht->fracht[0]->anzahl -= $planet->feld[$i]->bau->braucht->fracht[0]->anzahl;
                }
            }
        } // end of inner rounds
    } // end of rounds
    $planet->frachtraum->save();
    mysqli_query($verindung,"update planeten set energie=energie+" . $glob_energy . " where id = " . $planet->id);
    if ($planet->id == 154)
        echo "update planeten set energie=energie+" . $glob_energy . " where id = " . $planet->id . "\n";

//upgrade von gebäuden    
//nach diesem Block wird das Panetenobjekt abgebaut
    for($i=1;$i< sizeof($planet->feld);$i++) {
        if($planet->feld[$i]->rest_bauzeit > 0) {
            $planet->feld[$i]->rest_bauzeit--;
            if($planet->feld[$i]->rest_bauzeit == 0) {
                mysqli_query($verindung,"update planeten set lager=lager+".$planet->feld[$i]->bau->lager.",maxschilde=maxschilde+".$planet->feld[$i]->bau->schilde.",laser=laser+".$planet->feld[$i]->bau->laser.",maxenergie=maxenergie+".$planet->feld[$i]->bau->epslager." where id=".$planet->id) or die(mysqli_error($verindung));
                
            }
        }
        $planet->feld[$i]->save();
    }
}

//ende Planetenbrechnung

mysqli_query($verindung,"UPDATE planeten SET energie=maxenergie WHERE energie>maxenergie");
mysqli_query($verindung,"update planeten set schilde=maxschilde where schilde>maxschilde");

//schilde und tarnung
mysqli_query($verindung,"UPDATE schiffe SET schildstatus=0 WHERE schildstatus=1 AND energie=0");
mysqli_query($verindung,"UPDATE planeten SET schildstatus=0 WHERE schildstatus=1 AND energie=0");
mysqli_query($verindung,"UPDATE schiffe SET tarnung=0 WHERE tarnung=1 AND energie=0");
mysqli_query($verindung,"UPDATE schiffe SET energie=energie-1 WHERE energie>0 AND schildstatus=1");
mysqli_query($verindung,"UPDATE planeten SET energie=energie-1 WHERE energie>0 AND schildstatus=1");
mysqli_query($verindung,"UPDATE schiffe SET energie=energie-1 WHERE energie>0 AND tarnung=1");

#mysqli_query($verindung,"UPDATE schiffe SET schilde=maxschilde,hull=maxhull WHERE (SELECT COUNT(*) FROM quests WHERE geber=schiffe.id OR abgeber=schiffe.id)>0");

//Energieverlust der Schiffe

$q = mysqli_query($verindung,"update schiffe s, weltraum w, weltraumfelder x 
set s.energie=round(s.energie*(1-x.energieverlust/10)*100)/100
where
s.typ ='s' and
s.besitzer != 2 and
s.x = w.x and 
s.y = w.y and
s.system = w.system and
w.typ = x.id and
x.energieverlust >0 
");

//Ende EVerlust


/* warenkzinsen TODO 
  //0 bis 8
  for ($ct = 0; $ct <= 8; $ct++) {
  $zinslimes = $ct > 3 ? 150 : 300;
  mysql_query("UPDATE konto SET " . $inhalt[$ct] . "=ceil(" . $inhalt[$ct] . "*0.9) WHERE besitzer>9 AND " . $inhalt[$ct] . ">$zinslimes");
  } */


//mailerinnerungen
$rememberquery = mysqli_query($verindung,"SELECT * FROM account WHERE inaktiv=35 AND id>9");
while ($rem = mysqli_fetch_array($rememberquery)) {
    $mail1 = $rem["email"];
    $name1 = $rem["name"];
    $message = "Hallo $name1,\n\nDu bekommst diese Mail weil du seit 35 Ticks (7 Tagen) dich nicht mehr bei Galaxy Adventures gemeldet hast. Dies soll nur eine kleine Erinnerung sein, dass dein Account noch existiert ;). Solltest du kein Interesse mehr an Galaxy-Adventures 2 haben und weitere 35 Ticks verstreichen, so wird dein Account geloescht und alle deine Daten aus der Datenbank entfernt.\nIch wuensche dir viel Spass\n\ncremetorte";
    //mail($mail1, "Erinnerung - 7 Tage", $message, "From: GA-Team <gasupport@keinerspieltmitmir.de>");
}

//loeschung
//mailerinnerungen
$rememberquery = mysqli_query($verindung,"SELECT * FROM account WHERE inaktiv >= 70 AND id>9");
while ($rem = mysqli_fetch_array($rememberquery)) {
    $mail1 = $rem["email"];
    $name1 = $rem["name"];
    $id1 = $rem["id"];
    $message = "Hallo $name1,\n\nDu bekommst diese Mail weil du seit 70 Ticks (14 Tagen) dich nicht mehr bei Galaxy Adventures gemeldet hast. \nDein Account wurde geloescht. Ich hoffe du hattest Spass in Galaxy-Adventures.\nIch wuensche dir viel Spass\n\ncremetorte";
    //mail($mail1, "Loeschung - 14 Tage", $message, "From: GA-Team <gasupport@keinerspieltmitmir.de>");
    //mysql_query("INSERT INTO mail (empfaenger,absender,neu) VALUES ('1','2','1')");
    //mysql_query("DELETE FROM account WHERE id='$id1'");
}

//Gondeln reseten
mysqli_query($verindung,"UPDATE schiffe SET gondeln=0,phaser=0");

//blauer nebel schaden
$abfrage = mysqli_query($verindung,"SELECT * FROM weltraum WHERE typ='b'");
while ($foo = mysqli_fetch_array($abfrage)) {
    $x = $foo["x"];
    $y = $foo["y"];
    $abfrage2 = mysqli_query($verindung,"SELECT * FROM schiffe WHERE x='$x' AND y='$y'");
    while ($bar = mysqli_fetch_array($abfrage2)) {
        $schiff = new Schiffe($bar["id"]);
        $schiff->hull--;
        if ($schiff->hull <= 0)
            $schiff->zerstoerung();
    }
}

//Nebel Abfragen
/*
  $abfrage=mysql_query("SELECT id FROM schiffe WHERE orbit=0 AND besitzer!=2");
  while($row=mysql_fetch_array($abfrage))
  {
  $shp=new Schiffe($row[0]);
  $feld=new Feld($shp->position->x,$shp->position->y,$shp->position->system->id);
  if($feld->was=='Weltraum')
  switch ($feld->typ) {
  case "g": mysql_query("UPDATE schiffe SET schilde=ROUND(schilde*1.1) WHERE id='".$shp->id-"'"); break;
  case "b":
  case "radio":
  case "metrion":
  }
  }
 */

#mysqli_query($verindung,"UPDATE schiffe SET schilde=maxschilde WHERE schilde>maxschilde");

//include("wpunkte.php");
//Tick abmelden
$endeminuten = date("i");
$endesek = date("s");
$ende = $endeminuten * 60 + $endesek;
$zeit = $ende - $anfang;
mysqli_query($verindung,"UPDATE `ticklog` SET status=0,dauer='$zeit' WHERE datum='$datum'");
?>
