<?php

include 'head.php';
include 'navlogged.php';
include 'klassen.php';

$verbindung = get_verbindung();

$ich = new Account($_SESSION['Id']);

/*
Quest Typen

- 4: Buildings
- 6: Items


*/

// test implematnion vom showquest.php erfolgstrigger
// questpruefeung : Buildings ( Typ 4)
$abfrage = mysqli_query($verbindung, "SELECT erfolge.anzahl,quests.zusatz,quests.max,erfolge.id FROM erfolge,quests WHERE uid='".$_SESSION['Id']."' AND quests.id=erfolge.qid AND quests.typ=4 AND erledigt=0");
while ($row = mysqli_fetch_assoc($abfrage)) {
    $menge = $row['max'];
    $saveid = $row['id'];
    $gebaude = $row['zusatz'];
    $anzahl = $row['anzahl'];
}
echo 'DEBUG '.$saveid;
if ($saveid > 0) {
    $bcount = 0;
    $sumfeld = [];
    $abfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE besitzer='".$_SESSION['Id']."'");
    while ($row = mysqli_fetch_array($abfrage)) {
        $gefunden = false;
        $planet = new Planeten($row['id']);
        echo 'CHECKING '.$planet->id.' for '.$gebaude.' ';
        for ($i = 1; $i <= 50; ++$i) {
            echo 'LOOP '.$i.' WAS '.$planet->feld[$i]->name.' BAU ID '.$planet->feld[$i]->bau->id.' BAUZEIT '.$planet->feld[$i]->rest_bauzeit.'<br><br>';
            if ($planet->feld[$i]->bau->id == $gebaude && $planet->feld[$i]->rest_bauzeit == 0) {
                $gefunden = true;
                ++$bcount;
                echo 'found';
            }
        }
        $sumfeld[] = $gefunden ? '1' : '0';
    }
    $count = 0;
    for ($i = 0; $i < sizeof($sumfeld); ++$i) {
        ++$count;
    }
    // mysql_query("UPDATE erfolge SET anzahl='$count' WHERE id='$saveid'");

    // if($count>=$menge && $bcount>=$anzahl+$menge) mysql_query("UPDATE erfolge SET anzahl='$menge',erledigt=1 WHERE erledigt=0 AND id='$saveid'");
    if ($bcount >= $menge) {
        mysqli_query($verbindung, "UPDATE erfolge SET anzahl='$menge',erledigt=1 WHERE erledigt=0 AND id='$saveid'");
    }
}

// questpruefeung : Items ( Typ 6)
$abfrage = mysqli_query($verbindung, "SELECT erfolge.anzahl,quests.zusatz,quests.max,erfolge.id FROM erfolge,quests WHERE uid='".$_SESSION['Id']."' AND quests.id=erfolge.qid AND quests.typ='4' AND erledigt='0'");
while ($row = mysqli_fetch_assoc($abfrage)) {
    $menge = $row['max'];
    $saveid = $row['id'];
    $gebaude = $row['zusatz'];
    $anzahl = $row['anzahl'];
}
if ($saveid > 0) {
    $bcount = 0;
    $sumfeld = [];
    $abfrage = mysqli_query($verbindung, "SELECT * FROM planeten WHERE besitzer='".$_SESSION['Id']."'");
    while ($row = mysqli_fetch_array($abfrage)) {
        $gefunden = false;
        $planet = new Planeten($row['id']);
        for ($i = 1; $i <= 50; ++$i) {
            if ($planet->feld[$i]->was == $gebaude && $planet->feld[$i]->bauzeit == 0) {
                $gefunden = true;
                ++$bcount;
            }
        }
        $sumfeld[] = $gefunden ? '1' : '0';
    }
    $count = 0;
    for ($i = 0; $i < sizeof($sumfeld); ++$i) {
        ++$count;
    }
    // mysql_query("UPDATE erfolge SET anzahl='$count' WHERE id='$saveid'");

    if ($count >= $menge && $bcount >= $anzahl + $menge) {
        mysqli_query($verbindung, "UPDATE erfolge SET anzahl='$menge',erledigt=1 WHERE erledigt=0 AND id='$saveid'");
    }
}

// ende trigger

$sid = $_GET['sid'];
if (!ctype_digit($sid)) {
    exit;
}
$uid = $_GET['uid'];
if (isset($_GET['uid']) && !ctype_digit($sid)) {
    exit;
}

if ($uid > 0) {
    $ushp = new Schiffe($uid);
}

if ($sid > 0) {
    $schiff = new Schiffe($sid);
    if ($sid > 0 && ($ushp->position->x != $schiff->position->x || $ushp->position->y != $schiff->position->y || $ushp->position->orbit != $schiff->position->orbit || $ushp->position->system->id != $schiff->position->system->id)) {
        exit('interner Questfehler. bitte Admin benachrichtigen!<br />');
    }

    $sarray = [];
    $abfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE system='".$schiff->position->system->id."' AND x='".$schiff->position->x."' AND y='".$schiff->position->y."'");
    while ($row = mysqli_fetch_array($abfrage)) {
        $sarray[] = $row['id'];
    }
    if (sizeof($sarray) <= 1) {
        exit('Fehler: Kein Schiff gefunden');
    }
}
if ($sid == 0) {
    $sarray = ['0'];
}
if (isset($_GET['aid']) && ctype_digit($_GET['aid'])) {
    $schiff_bekommen = true;
    $neuq = new Quest($_GET['aid']);
    if (in_array($neuq->abgeber, $sarray) || $neuq->abgeber == 0) {
        // qabgeben
        // allgemein Belohnungsfrage

        // ENDE belohnung
        // spezialfall typ=6
        if ($neuq->typ == 6) {
            // pr�fen auf globale quest
            if ($neuq->abgeber == 0) {
                // entfernen aller Qitems
                $abfrage = mysqli_query($verbindung, "SELECT id FROM schiffe WHERE besitzer='$ich->id'");
                while ($row = mysqli_fetch_array($abfrage)) {
                    $shp = new Schiffe($row[0]);
                    for ($i = 0; $i < sizeof($shp->qitems); ++$i) {
                        if ($neuq->item == $shp->qitems[$i]->id) {
                            $shp->qitems[$i]->id = 0;
                        }
                    }
                    $shp->savequest();
                }
                $result = mysqli_query($verbindung, "UPDATE erfolge SET erledigt=2 WHERE erledigt=1 AND id='".$_GET['aid']."' AND uid='".$_SESSION['Id']."'");
                if ($result) {
                    echo 'Quest erfolgreich abgegeben.';
                }
            } else {
                $shp2 = new Schiffe($neuq->abgeber);
                // auswahl einengen alle schiffe die im sektor sind
                $abfrage = mysqli_query($verbindung, "SELECT * FROM schiffe WHERE x='".$shp2->position->x."' AND y='".$shp2->position->y."' AND orbit='".$shp2->position->orbit."' AND system='".$shp2->position->system->id."' AND besitzer='".$_SESSION['Id']."'");
                while ($row = mysqli_fetch_array($abfrage)) {
                    // auswahl einengen alle schiffe die max anzahl an qitems dabei haben in $preship werfen (id)
                    $qcounter = 0;
                    $stellen = [];
                    $shp3 = new Schiffe($row['id']);
                    for ($i = 0; $i < sizeof($shp3->qitems); ++$i) {
                        if ($shp3->qitems[$i]->id == $neuq->item) {
                            ++$qcounter;
                            $shp3->qitems[$i]->id = 0;
                        }
                    }
                    if ($qcounter >= $neuq->max) {	// abgeben
                        // for($k=0;$k<sizeof($stellen);$k++) unset($shp3->qitems[$stellen[$k]]);
                        /*
                        foreach($shp->qitems as $k => $v)
                        {
                        if($v==$neuq->qid)
                        unset($shp->qitems[$k]);
                        }
                        */
                        $shp3->savequest();
                        $result = mysqli_query($verbindung, "UPDATE erfolge SET erledigt=2 WHERE erledigt=1 AND id='".$_GET['aid']."' AND uid='".$_SESSION['Id']."'");
                        if ($result) {
                            echo 'Quest erfolgreich abgegeben.';
                        }
                    } else {
                        $qit = new Item($neuq->zusatz);
                        echo 'Hinweis: Schiff '.$shp3->name.' ('.$shp3->id.') ist zwar vor Ort hat aber keine '.$neuq->max.' '.$qit->name.' an Board!<br />';
                        $schiff_bekommen = false;
                    }
                }
            }
        } else {
            if ($neuq->typ == 2) {
                $tstoff = $neuq->zusatz;
                if ($uid > 0) {
                    $ushp->frachtraum->$tstoff -= $neuq->max;
                    $ushp->frachtraum->save();
                }
            }
            if ($neuq->qid == 18) {
                mysqli_query($verbindung, "UPDATE forschung SET oberth=1 WHERE besitzer='".$_SESSION['Id']."'");
            }

            $result = mysqli_query($verbindung, "UPDATE erfolge SET erledigt=2 WHERE erledigt=1 AND id='".$_GET['aid']."' AND uid='".$_SESSION['Id']."'");
            if ($result) {
                echo 'Quest erfolgreich abgegeben.';
            }
        }
        if ($neuq->qid == 30) {
            mysqli_query($verbindung, "UPDATE forschung SET miranda=1 WHERE besitzer='".$_SESSION['Id']."'");
        }

        /* QUESTBELOHNUNG */
        if ((sizeof($neuq->bschiffe) > 0 || sizeof($brohstoffe) > 0) && $schiff_bekommen) {
            $heimat = mysqli_query($verbindung, "SELECT x,y,system FROM planeten WHERE heimat=1 AND besitzer='".$_SESSION['Id']."'");
            $heimat = mysqli_fetch_array($heimat);
            if ($neuq->bschiffe[0] != '') {
                for ($i = 0; $i < sizeof($neuq->bschiffe); ++$i) {
                    $lastid = checkforlastid('schiffe') + 1;
                    echo 'MILCH: ',$neuq->bschiffe[$i],'<br />';
                    mysqli_query($verbindung, "INSERT INTO schiffe (name,x,y,system,besitzer,energie,maxenergie,energieoutput,maxwarpkern,schilde,maxschilde,hull,maxhull,bild,klasse,typ,lager,deuterium,skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,skillbau) SELECT 'noname','".$heimat[0]."','".$heimat[1]."','".$heimat[2]."','".$_SESSION['Id']."',maxenergie,maxenergie,energieoutput,maxwarpkern,maxschilde,maxschilde,maxhull,maxhull,bild,klasse,typ,lager,'40',skilldeut,skillerz,skilltranswarp,laser,maxtorpedo,maxgondeln,maxphaser,skilltarnung,lrs,skillbau FROM bauplan WHERE klasse='".$neuq->bschiffe[$i]."'") or exit(mysqli_error($verbindung));
                    mysqli_query($verbindung, "INSERT INTO schiffsmodule (sid,a1,a2,d1,d2,c1,c2) SELECT '$lastid',a1,a2,d1,d2,c1,c2 FROM bauplan WHERE klasse='".$neuq->bschiffe[$i]."'") or exit(mysqli_error($verbindung));
                }
            }
            if ($neuq->brohstoffe[0] != '' && $neuq->abgeber > 0) {
                for ($i = 0; $i < sizeof($neuq->brohstoffe); ++$i) {
                    $roh = explode('-', $neuq->brohstoffe[$i]);
                    // schiffsberechnung
                    $ranzahl = $roh[1];
                    if ($ushp->frachtraum->gesamt() + $ranzahl > $ushp->frachtraum->max) {
                        $ranzahl = $ushp->frachtraum->max - $ushp->frachtraum->gesamt();
                    }
                    if ($ranzahl < 0) {
                        $ranzahl = 0;
                    }
                    $ushp->frachtraum->$roh[0] += $ranzahl;
                    $ushp->frachtraum->save();
                }
            }
            // Warenboerse
            if ($neuq->brohstoffe[0] != '' && $neuq->abgeber == 0) {
                $konto = new Konto($_SESSION['Id']);
                for ($i = 0; $i < sizeof($neuq->brohstoffe); ++$i) {
                    $roh = explode('-', $neuq->brohstoffe[$i]);
                    // schiffsberechnung
                    $ranzahl = $roh[1];
                    if ($ranzahl < 0) {
                        $ranzahl = 0;
                    }
                    $konto->$roh[0] += $ranzahl;
                    $konto->save();
                }
            }
        }
    }
}

if (isset($_GET['qid']) && ctype_digit($_GET['qid'])) {
    $bcount = 0;
    // falls 4 bestimmen der aktuellen baude
    $abfrage = mysqli_query($verbindung, "SELECT typ,zusatz FROM quests WHERE id='".$_GET['qid']."'");
    $abfrage = mysqli_fetch_array($abfrage);
    $qtyp = $abfrage[0];
    $zusatz1 = $abfrage[1];
    $abfrage = mysqli_query($verbindung, "SELECT id FROM planeten WHERE besitzer='".$_SESSION['Id']."'");
    while ($row = mysqli_fetch_array($abfrage)) {
        $pla = new Planeten($row['id']);
        for ($i = 1; $i <= 50; ++$i) {
            if ($pla->feld[$i]->was == $zusatz1 && $pla->feld[$i]->bauzeit == 0) {
                ++$bcount;
            }
        }
    }

    // quest annehmen
    $abf = mysqli_query($verbindung, "SELECT * FROM erfolge WHERE qid='".$_GET['qid']."' AND uid='".$_SESSION['Id']."'");
    while ($ro = mysqli_fetch_array($abf)) {
        exit('Quest bereits vorhanden!');
    }
    if ($qtyp != 4 && $qtyp != 8) {
        mysqli_query($verbindung, 'INSERT INTO erfolge (uid,qid,anzahl) VALUES ('.$_SESSION['Id'].','.$_GET['qid'].',0)') or exit($verbindung->error);
    }
    if ($qtyp == 4) {
        mysqli_query($verbindung, "INSERT INTO erfolge (anzahl,uid,qid) VALUES ('$bcount','".$_SESSION['Id']."','".$_GET['qid']."')") or exit(mysqli_error($verbindung));
    }
    if ($qtyp == 8) {
        mysqli_query($verbindung, "INSERT INTO erfolge (erledigt,uid,qid) VALUES ('1','".$_SESSION['Id']."','".$_GET['qid']."')") or exit(mysqli_error($verbindung));
    }
    if ($qtyp == 8) {
        echo '<b>Quest erledigt!</b><br /><br />';
    } else {
        echo 'Quest angenommen!<br /><br />';
    }
}

$abfrage = mysqli_query($verbindung, "SELECT * FROM quests WHERE (geber='$sid' OR abgeber='$sid') AND (level='".$ich->level."' OR level=0) ORDER BY id DESC");
while ($row = mysqli_fetch_array($abfrage)) {
    // Anzeige der QUests beschraenken
    $ch = false;
    $abfrage2 = mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='".$_SESSION['Id']."' AND erledigt=2 AND qid='".$row['vorquest']."'");
    while ($row2 = mysqli_fetch_array($abfrage2)) {
        $ch = true;
    }

    if ($ch || $row['vorquest'] == 0) {
        // ende der beschr
        $tempid = $row['id'];
        $abfrage2 = mysqli_query($verbindung, "SELECT * FROM erfolge WHERE uid='".$_SESSION['Id']."' AND qid='".$tempid."'");
        $fertig = -1;
        while ($row2 = mysqli_fetch_array($abfrage2)) {
            $fertig = $row2['erledigt'];
            $aid = $row2['id'];
        }

        // questtyp 2 bestimmen	/*
        if ($row['typ'] == 2 && $uid > 0 && $fertig == 0) {
            $tvar = $row['zusatz'];
            echo 'Bedingung 1: ',$ushp->frachtraum->$tvar,'   Bedingung 2: ',$row['max'],'<br />';
            if ($ushp->frachtraum->$tvar >= $row['max']) {
                $fertig = 1;
                mysqli_query($verbindung, "UPDATE erfolge SET erledigt=1 WHERE qid='".$row['id']."' AND uid='".$_SESSION['Id']."' AND erledigt=0");
                $row['erledigt'] = 1;
            }
        }

        // ende bestimmung questtyp 2

        // echo $fertig>=0 ." - ". $schiff->id==$row["abgeber"] ." - ". $uid>0;
        if (($fertig == -1 && $schiff->id == $row['geber'] && $uid > 0) || ($row['geber'] == 0 && $fertig == -1) || ($row['abgeber'] == 0 && $fertig >= 0) || ($fertig >= 0 && $schiff->id == $row['abgeber'] && $uid > 0)) {
            $lvl = new Account('5');
            echo '<div class="rahmen">Questgeber: ';
            if ($sid > 0) {
                echo $schiff->besitzer->nickname;
            } else {
                echo $lvl->nickname;
            } echo '<hr /><br />';
            echo '<h3>',nl2br($row['titel']),'</h3>';
            if ($fertig >= 1) {
                echo '',nl2br($row['abgabetext']),'<br /><hr />';
            } else {
                echo '',nl2br($row['text']),'<br /><br /><hr />';
            }
            if ($fertig == -1) {
                echo '<a href="quest.php?uid=',$_GET['uid'],'&sid=',$sid,'&qid=',$row['id'],'"><span style="color:yellow;font-weight:bold;">annehmen</span></a></div><br />';
            }
            if ($fertig == 1) {
                echo '<a href="quest.php?uid=',$_GET['uid'],'&sid=',$sid,'&aid=',$aid,'"><span style="color:yellow;font-weight:bold;">Quest beenden</span></a></div><br />';
            }
            if ($fertig == 0) {
                echo '<span style="color:red;font-weight:bold;">noch nicht abgeschlossen</span></div><br />';
            }
            if ($fertig == 2) {
                echo '<span style="color:green;font-weight:bold;">bereits abgeschlossen</span></div><br />';
            }
        }
    }
}
echo '<br />';

if ($uid > 0) {
    echo '<br /><a href="schiffe.php?sid=',$uid,'"><span class="uberschrift">zur&uuml;ck zum Schiff</span></a>';
}
include 'foot.php';
