<?php
include_once dirname(__DIR__) . '/connect.php';

// ============================================================
//  Quest Engine v2
//  Trigger-basiertes Quest-System
//
//  Trigger-Typen:
//    kill_ships        — Schiffe vernichten
//    collect_resource  — Rohstoff im Frachtraum haben
//    build_building    — Gebäude gebaut haben
//    scan_systems      — Sternensysteme gescannt haben
//    fly_to            — Position erreichen
//    deliver_item      — Quest-Item abliefern
// ============================================================


// ------------------------------------------------------------
//  Interface: Jeder Trigger implementiert dies
// ------------------------------------------------------------
interface QuestTriggerInterface
{
    /**
     * Berechnet den aktuellen Fortschritt für diesen Trigger-Typ.
     *
     * @param  int   $uid     Spieler-Account-ID
     * @param  array $params  Parameter aus quest_steps.parameter (JSON-decoded)
     * @return int            Aktueller Fortschritt (0 bis max)
     */
    public function getProgress(int $uid, array $params): int;

    /**
     * Gibt das Maximum zurück das für diesen Trigger zählt.
     * Normalerweise direkt aus $params, aber manche Trigger
     * berechnen es dynamisch.
     */
    public function getMax(array $params): int;
}


// ------------------------------------------------------------
//  Trigger: Schiffe vernichten
//  Parameter: {"anzahl": 5, "rasse": "klingon"}  (rasse optional)
// ------------------------------------------------------------
class KillShipsTrigger implements QuestTriggerInterface
{
    public function getProgress(int $uid, array $params): int
    {
        // Fortschritt kommt aus player_quest_steps.fortschritt —
        // wird von aussen erhöht (z.B. in combat.php via QuestEngine::onKill)
        // Hier gibt es keinen "scan"-Modus, daher 0 als Fallback
        return 0;
    }

    public function getMax(array $params): int
    {
        return (int)($params['anzahl'] ?? 1);
    }
}


// ------------------------------------------------------------
//  Trigger: Rohstoff sammeln
//  Parameter: {"rohstoff": "erz", "menge": 500}
// ------------------------------------------------------------
class CollectResourceTrigger implements QuestTriggerInterface
{
    public function getProgress(int $uid, array $params): int
    {
        $verbindung = get_verbindung();
        $rohstoff   = $params['rohstoff'] ?? '';
        $menge      = (int)($params['menge'] ?? 0);

        if ($rohstoff === '' || $menge <= 0) {
            return 0;
        }

        // Rohstoffe in allen Schiffen des Spielers summieren
        // Frachtraum-Felder aus der schiffe-Tabelle
        $allowed = ['erz', 'deuterium', 'titan', 'silizium', 'antimaterie',
                    'nahrung', 'wasser', 'stahl', 'kristall'];
        if (!in_array($rohstoff, $allowed, true)) {
            return 0;
        }

        $col    = mysqli_real_escape_string($verbindung, $rohstoff);
        $q = mysqli_query($verbindung,
            "SELECT SUM(f.`$col`) AS gesamt
               FROM schiffe s
               JOIN frachtraum f ON f.sid = s.id
              WHERE s.besitzer = " . intval($uid)
        );
        $row = mysqli_fetch_assoc($q);
        $aktuell = (int)($row['gesamt'] ?? 0);

        return min($aktuell, $menge); // max = menge, nie überschreiten
    }

    public function getMax(array $params): int
    {
        return (int)($params['menge'] ?? 1);
    }
}


// ------------------------------------------------------------
//  Trigger: Gebäude gebaut haben
//  Parameter: {"gebaeude_id": 3, "anzahl": 2}
// ------------------------------------------------------------
class BuildBuildingTrigger implements QuestTriggerInterface
{
    public function getProgress(int $uid, array $params): int
    {
        $verbindung  = get_verbindung();
        $gebaeude_id = (int)($params['gebaeude_id'] ?? 0);
        $anzahl      = (int)($params['anzahl'] ?? 1);

        if ($gebaeude_id <= 0) {
            return 0;
        }

        // Alle Planeten des Spielers durchgehen und in planet2 zählen
        $count = 0;
        $pq = mysqli_query($verbindung,
            "SELECT id FROM planeten WHERE besitzer = " . intval($uid)
        );
        while ($prow = mysqli_fetch_assoc($pq)) {
            $pid = (int)$prow['id'];
            // Felder feld1..feld70 prüfen
            $fq = mysqli_query($verbindung,
                "SELECT * FROM planet2 WHERE id = $pid"
            );
            $frow = mysqli_fetch_assoc($fq);
            if (!$frow) {
                continue;
            }
            for ($i = 1; $i <= 70; ++$i) {
                $key = 'feld' . $i;
                if (!isset($frow[$key])) {
                    continue;
                }
                $parts = explode('/', $frow[$key]);
                // Format: gebaeude_id/untergrund_id/bauzeit/hull/aktiv
                $bid     = (int)($parts[0] ?? 0);
                $bauzeit = (int)($parts[2] ?? 1); // 0 = fertig
                if ($bid === $gebaeude_id && $bauzeit === 0) {
                    ++$count;
                }
            }
        }

        return min($count, $anzahl);
    }

    public function getMax(array $params): int
    {
        return (int)($params['anzahl'] ?? 1);
    }
}


// ------------------------------------------------------------
//  Trigger: Sternensysteme scannen
//  Parameter: {"anzahl": 3}
//  Fortschritt wird von aussen erhöht (scan-Event)
// ------------------------------------------------------------
class ScanSystemsTrigger implements QuestTriggerInterface
{
    public function getProgress(int $uid, array $params): int
    {
        // Event-getrieben — Fortschritt liegt in player_quest_steps
        return 0;
    }

    public function getMax(array $params): int
    {
        return (int)($params['anzahl'] ?? 1);
    }
}


// ------------------------------------------------------------
//  Trigger: Position erreichen
//  Parameter: {"x": 10, "y": 5, "system_id": 0}
//  Fortschritt: 0 oder 1 (noch nicht / erreicht)
// ------------------------------------------------------------
class FlyToTrigger implements QuestTriggerInterface
{
    public function getProgress(int $uid, array $params): int
    {
        // Event-getrieben — wird gesetzt wenn ein Schiff des Spielers
        // die Zielkoordinaten betritt
        return 0;
    }

    public function getMax(array $params): int
    {
        return 1;
    }
}


// ------------------------------------------------------------
//  Trigger: Quest-Item abliefern
//  Parameter: {"item_id": 7, "menge": 1}
//  Fortschritt wird von aussen gesetzt
// ------------------------------------------------------------
class DeliverItemTrigger implements QuestTriggerInterface
{
    public function getProgress(int $uid, array $params): int
    {
        return 0;
    }

    public function getMax(array $params): int
    {
        return (int)($params['menge'] ?? 1);
    }
}


// ============================================================
//  QuestEngine — Hauptklasse
// ============================================================
class QuestEngine
{
    // Registry: trigger_typ => Klassenname
    private static array $triggerRegistry = [
        'kill_ships'       => KillShipsTrigger::class,
        'collect_resource' => CollectResourceTrigger::class,
        'build_building'   => BuildBuildingTrigger::class,
        'scan_systems'     => ScanSystemsTrigger::class,
        'fly_to'           => FlyToTrigger::class,
        'deliver_item'     => DeliverItemTrigger::class,
    ];

    // --------------------------------------------------------
    //  Trigger-Instanz holen
    // --------------------------------------------------------
    private static function getTrigger(string $typ): ?QuestTriggerInterface
    {
        $class = self::$triggerRegistry[$typ] ?? null;
        if ($class === null) {
            return null;
        }
        return new $class();
    }

    // --------------------------------------------------------
    //  Quest annehmen
    //  Legt player_quests + player_quest_steps an
    // --------------------------------------------------------
    public static function accept(int $uid, int $quest_id): bool
    {
        $verbindung = get_verbindung();

        // Quest-Template laden
        $q = mysqli_query($verbindung,
            "SELECT * FROM quest_templates WHERE id = " . intval($quest_id) . " AND aktiv = 1"
        );
        $template = mysqli_fetch_assoc($q);
        if (!$template) {
            return false;
        }

        // Vorquest prüfen
        if ((int)$template['vorquest_id'] > 0) {
            $vq = mysqli_query($verbindung,
                "SELECT id FROM player_quests
                  WHERE uid = " . intval($uid) . "
                    AND quest_id = " . intval($template['vorquest_id']) . "
                    AND status = 'done'
                  LIMIT 1"
            );
            if (!mysqli_fetch_assoc($vq)) {
                return false; // Vorquest nicht abgeschlossen
            }
        }

        // Bereits aktiv oder abgeschlossen (und nicht repeatable)?
        $existQ = mysqli_query($verbindung,
            "SELECT id, status FROM player_quests
              WHERE uid = " . intval($uid) . "
                AND quest_id = " . intval($quest_id) . "
              ORDER BY id DESC LIMIT 1"
        );
        $exist = mysqli_fetch_assoc($existQ);
        if ($exist) {
            if ($exist['status'] === 'active') {
                return false; // schon aktiv
            }
            if ($exist['status'] === 'done' && !(bool)$template['repeatable']) {
                return false; // schon erledigt, nicht wiederholbar
            }
        }

        // player_quests Eintrag anlegen
        mysqli_query($verbindung,
            "INSERT INTO player_quests (uid, quest_id, status)
             VALUES (" . intval($uid) . ", " . intval($quest_id) . ", 'active')"
        );
        $pq_id = (int)mysqli_insert_id($verbindung);
        if ($pq_id <= 0) {
            return false;
        }

        // Schritte laden und player_quest_steps anlegen
        $sq = mysqli_query($verbindung,
            "SELECT * FROM quest_steps WHERE quest_id = " . intval($quest_id) . "
              ORDER BY reihenfolge ASC"
        );
        while ($step = mysqli_fetch_assoc($sq)) {
            $params  = json_decode($step['parameter'], true) ?? [];
            $trigger = self::getTrigger($step['trigger_typ']);
            $max     = $trigger ? $trigger->getMax($params) : 1;

            mysqli_query($verbindung,
                "INSERT INTO player_quest_steps (player_quest_id, step_id, fortschritt, max)
                 VALUES ($pq_id, " . intval($step['id']) . ", 0, " . intval($max) . ")"
            );
        }

        return true;
    }

    // --------------------------------------------------------
    //  Fortschritt aller aktiven Quests eines Spielers prüfen
    //  Sollte nach relevanten Aktionen aufgerufen werden
    // --------------------------------------------------------
    public static function checkAll(int $uid): void
    {
        $verbindung = get_verbindung();

        // Alle aktiven Quests des Spielers
        $pqQ = mysqli_query($verbindung,
            "SELECT pq.id AS pq_id, pq.quest_id
               FROM player_quests pq
              WHERE pq.uid = " . intval($uid) . "
                AND pq.status = 'active'"
        );

        while ($pq = mysqli_fetch_assoc($pqQ)) {
            $pq_id = (int)$pq['pq_id'];
            self::checkQuest($uid, $pq_id);
        }
    }

    // --------------------------------------------------------
    //  Einzelne Quest prüfen
    // --------------------------------------------------------
    public static function checkQuest(int $uid, int $pq_id): void
    {
        $verbindung = get_verbindung();

        // Schritte dieser Quest laden
        $stepsQ = mysqli_query($verbindung,
            "SELECT pqs.id AS pqs_id, pqs.step_id, pqs.fortschritt, pqs.max, pqs.erledigt,
                    qs.trigger_typ, qs.parameter, qs.reihenfolge
               FROM player_quest_steps pqs
               JOIN quest_steps qs ON qs.id = pqs.step_id
              WHERE pqs.player_quest_id = " . intval($pq_id) . "
              ORDER BY qs.reihenfolge ASC"
        );

        $allDone = true;

        while ($step = mysqli_fetch_assoc($stepsQ)) {
            if ((int)$step['erledigt'] === 1) {
                continue; // Schritt schon fertig
            }

            $params  = json_decode($step['parameter'], true) ?? [];
            $trigger = self::getTrigger($step['trigger_typ']);

            if ($trigger !== null) {
                $progress = $trigger->getProgress($uid, $params);
                $max      = (int)$step['max'];

                mysqli_query($verbindung,
                    "UPDATE player_quest_steps
                        SET fortschritt = " . min($progress, $max) . "
                      WHERE id = " . intval($step['pqs_id'])
                );

                if ($progress >= $max) {
                    mysqli_query($verbindung,
                        "UPDATE player_quest_steps SET erledigt = 1
                          WHERE id = " . intval($step['pqs_id'])
                    );
                } else {
                    $allDone = false;
                }
            } else {
                $allDone = false;
            }
        }

        // Wenn alle Schritte erledigt: Quest auf "bereit zur Abgabe" setzen
        // (status bleibt 'active' — Spieler muss noch abgeben)
        // Quest abschließen passiert in complete()
    }

    // --------------------------------------------------------
    //  Prüfen ob alle Schritte einer Quest erledigt sind
    // --------------------------------------------------------
    public static function isReadyToComplete(int $pq_id): bool
    {
        $verbindung = get_verbindung();
        $q = mysqli_query($verbindung,
            "SELECT COUNT(*) AS total,
                    SUM(erledigt) AS done
               FROM player_quest_steps
              WHERE player_quest_id = " . intval($pq_id)
        );
        $row = mysqli_fetch_assoc($q);
        return (int)$row['total'] > 0 && (int)$row['done'] === (int)$row['total'];
    }

    // --------------------------------------------------------
    //  Quest abschließen und Belohnung vergeben
    // --------------------------------------------------------
    public static function complete(int $uid, int $pq_id): bool
    {
        $verbindung = get_verbindung();

        // Sicherheit: Quest gehört dem Spieler und ist aktiv
        $pqQ = mysqli_query($verbindung,
            "SELECT pq.id, pq.quest_id, qt.belohnung
               FROM player_quests pq
               JOIN quest_templates qt ON qt.id = pq.quest_id
              WHERE pq.id = " . intval($pq_id) . "
                AND pq.uid = " . intval($uid) . "
                AND pq.status = 'active'
              LIMIT 1"
        );
        $pq = mysqli_fetch_assoc($pqQ);
        if (!$pq) {
            return false;
        }

        if (!self::isReadyToComplete($pq_id)) {
            return false;
        }

        // Belohnung vergeben
        $belohnung = json_decode($pq['belohnung'], true) ?? [];
        self::grantReward($uid, $belohnung);

        // Quest als abgeschlossen markieren
        mysqli_query($verbindung,
            "UPDATE player_quests
                SET status = 'done', abgeschlossen_am = NOW()
              WHERE id = " . intval($pq_id)
        );

        return true;
    }

    // --------------------------------------------------------
    //  Belohnung vergeben
    // --------------------------------------------------------
    private static function grantReward(int $uid, array $belohnung): void
    {
        $verbindung = get_verbindung();

        // Rohstoffe: auf Heimatplanet oder Konto gutschreiben
        $rohstoffe = $belohnung['rohstoffe'] ?? [];
        if (!empty($rohstoffe)) {
            // Konto des Spielers
            $konto = new Konto($uid);
            foreach ($rohstoffe as $rohstoff => $menge) {
                $menge = (int)$menge;
                if ($menge > 0 && property_exists($konto, $rohstoff)) {
                    $konto->$rohstoff += $menge;
                }
            }
            $konto->save();
        }

        // Schiffe: am Heimatplaneten spawnen
        $schiffe = $belohnung['schiffe'] ?? [];
        if (!empty($schiffe)) {
            $heimatQ = mysqli_query($verbindung,
                "SELECT x, y, `system` FROM planeten
                  WHERE heimat = 1 AND besitzer = " . intval($uid) . "
                  LIMIT 1"
            );
            $heimat = mysqli_fetch_assoc($heimatQ);
            if ($heimat) {
                foreach ($schiffe as $klasse) {
                    $klasse = mysqli_real_escape_string($verbindung, $klasse);
                    $lastid = checkforlastid('schiffe') + 1;
                    mysqli_query($verbindung,
                        "INSERT INTO schiffe
                            (name, x, y, `system`, besitzer,
                             energie, maxenergie, energieoutput, maxwarpkern,
                             schilde, maxschilde, hull, maxhull, bild, klasse, typ,
                             lager, deuterium)
                         SELECT 'Geschenk', '{$heimat['x']}', '{$heimat['y']}', '{$heimat['system']}',
                                " . intval($uid) . ",
                                maxenergie, maxenergie, energieoutput, maxwarpkern,
                                maxschilde, maxschilde, maxhull, maxhull, bild, klasse, typ,
                                lager, 40
                           FROM bauplan WHERE klasse = '$klasse'"
                    );
                    mysqli_query($verbindung,
                        "INSERT INTO schiffsmodule (sid, a1, a2, d1, d2, c1, c2)
                         SELECT '$lastid', a1, a2, d1, d2, c1, c2
                           FROM bauplan WHERE klasse = '$klasse'"
                    );
                }
            }
        }
    }

    // --------------------------------------------------------
    //  Event: Schiff wurde zerstört (vom Spieler $uid)
    //  Aufruf z.B. in combat.php nach dem Kampf
    // --------------------------------------------------------
    public static function onKill(int $uid, string $rasse_des_opfers): void
    {
        $verbindung = get_verbindung();

        // Alle aktiven kill_ships-Schritte des Spielers finden
        $q = mysqli_query($verbindung,
            "SELECT pqs.id, pqs.fortschritt, pqs.max, qs.parameter
               FROM player_quest_steps pqs
               JOIN quest_steps qs ON qs.id = pqs.step_id
               JOIN player_quests pq ON pq.id = pqs.player_quest_id
              WHERE pq.uid = " . intval($uid) . "
                AND pq.status = 'active'
                AND qs.trigger_typ = 'kill_ships'
                AND pqs.erledigt = 0"
        );

        while ($row = mysqli_fetch_assoc($q)) {
            $params = json_decode($row['parameter'], true) ?? [];
            $filter = $params['rasse'] ?? '';

            // Rassen-Filter: leer = alle, sonst nur passende
            if ($filter !== '' && $filter !== $rasse_des_opfers) {
                continue;
            }

            $neu = min((int)$row['fortschritt'] + 1, (int)$row['max']);
            $done = $neu >= (int)$row['max'] ? 1 : 0;

            mysqli_query($verbindung,
                "UPDATE player_quest_steps
                    SET fortschritt = $neu, erledigt = $done
                  WHERE id = " . intval($row['id'])
            );
        }
    }

    // --------------------------------------------------------
    //  Event: System wurde gescannt
    // --------------------------------------------------------
    public static function onScan(int $uid): void
    {
        $verbindung = get_verbindung();

        $q = mysqli_query($verbindung,
            "SELECT pqs.id, pqs.fortschritt, pqs.max
               FROM player_quest_steps pqs
               JOIN quest_steps qs ON qs.id = pqs.step_id
               JOIN player_quests pq ON pq.id = pqs.player_quest_id
              WHERE pq.uid = " . intval($uid) . "
                AND pq.status = 'active'
                AND qs.trigger_typ = 'scan_systems'
                AND pqs.erledigt = 0"
        );

        while ($row = mysqli_fetch_assoc($q)) {
            $neu  = min((int)$row['fortschritt'] + 1, (int)$row['max']);
            $done = $neu >= (int)$row['max'] ? 1 : 0;
            mysqli_query($verbindung,
                "UPDATE player_quest_steps
                    SET fortschritt = $neu, erledigt = $done
                  WHERE id = " . intval($row['id'])
            );
        }
    }

    // --------------------------------------------------------
    //  Event: Koordinaten betreten
    // --------------------------------------------------------
    public static function onFlyTo(int $uid, int $x, int $y, int $system_id): void
    {
        $verbindung = get_verbindung();

        $q = mysqli_query($verbindung,
            "SELECT pqs.id, qs.parameter
               FROM player_quest_steps pqs
               JOIN quest_steps qs ON qs.id = pqs.step_id
               JOIN player_quests pq ON pq.id = pqs.player_quest_id
              WHERE pq.uid = " . intval($uid) . "
                AND pq.status = 'active'
                AND qs.trigger_typ = 'fly_to'
                AND pqs.erledigt = 0"
        );

        while ($row = mysqli_fetch_assoc($q)) {
            $params = json_decode($row['parameter'], true) ?? [];
            if ((int)($params['x'] ?? -1) === $x
                && (int)($params['y'] ?? -1) === $y
                && (int)($params['system_id'] ?? -1) === $system_id
            ) {
                mysqli_query($verbindung,
                    "UPDATE player_quest_steps
                        SET fortschritt = 1, erledigt = 1
                      WHERE id = " . intval($row['id'])
                );
            }
        }
    }

    // --------------------------------------------------------
    //  Aktive Quests eines Spielers mit Fortschritt laden
    //  (für die Spieler-UI)
    // --------------------------------------------------------
    public static function getActiveQuests(int $uid): array
    {
        $verbindung = get_verbindung();
        $result     = [];

        $pqQ = mysqli_query($verbindung,
            "SELECT pq.id AS pq_id, pq.quest_id, pq.angenommen_am,
                    qt.titel, qt.beschreibung, qt.abgabetext
               FROM player_quests pq
               JOIN quest_templates qt ON qt.id = pq.quest_id
              WHERE pq.uid = " . intval($uid) . "
                AND pq.status = 'active'
              ORDER BY pq.angenommen_am ASC"
        );

        while ($pq = mysqli_fetch_assoc($pqQ)) {
            $pq_id  = (int)$pq['pq_id'];
            $steps  = [];

            $stepsQ = mysqli_query($verbindung,
                "SELECT pqs.fortschritt, pqs.max, pqs.erledigt,
                        qs.beschreibung, qs.trigger_typ, qs.reihenfolge
                   FROM player_quest_steps pqs
                   JOIN quest_steps qs ON qs.id = pqs.step_id
                  WHERE pqs.player_quest_id = $pq_id
                  ORDER BY qs.reihenfolge ASC"
            );
            while ($s = mysqli_fetch_assoc($stepsQ)) {
                $steps[] = $s;
            }

            $result[] = [
                'pq_id'        => $pq_id,
                'quest_id'     => (int)$pq['quest_id'],
                'titel'        => $pq['titel'],
                'beschreibung' => $pq['beschreibung'],
                'abgabetext'   => $pq['abgabetext'],
                'angenommen_am'=> $pq['angenommen_am'],
                'steps'        => $steps,
                'bereit'       => self::isReadyToComplete($pq_id),
            ];
        }

        return $result;
    }

    // --------------------------------------------------------
    //  Verfügbare Quests für einen Spieler (noch nicht aktiv/done)
    // --------------------------------------------------------
    public static function getAvailableQuests(int $uid, int $level): array
    {
        $verbindung = get_verbindung();
        $result     = [];

        $q = mysqli_query($verbindung,
            "SELECT qt.*
               FROM quest_templates qt
              WHERE qt.aktiv = 1
                AND qt.min_level <= " . intval($level) . "
                AND qt.id NOT IN (
                    SELECT quest_id FROM player_quests
                     WHERE uid = " . intval($uid) . "
                       AND status IN ('active','done')
                )
              ORDER BY qt.id ASC"
        );

        while ($row = mysqli_fetch_assoc($q)) {
            // Vorquest prüfen
            $vorquest_ok = true;
            if ((int)$row['vorquest_id'] > 0) {
                $vq = mysqli_query($verbindung,
                    "SELECT id FROM player_quests
                      WHERE uid = " . intval($uid) . "
                        AND quest_id = " . intval($row['vorquest_id']) . "
                        AND status = 'done'
                      LIMIT 1"
                );
                $vorquest_ok = (bool)mysqli_fetch_assoc($vq);
            }
            if ($vorquest_ok) {
                // Schritte anhängen
                $steps = [];
                $sq = mysqli_query($verbindung,
                    "SELECT beschreibung, trigger_typ FROM quest_steps
                      WHERE quest_id = " . intval($row['id']) . "
                      ORDER BY reihenfolge ASC"
                );
                while ($s = mysqli_fetch_assoc($sq)) {
                    $steps[] = $s;
                }
                $row['steps'] = $steps;
                $result[] = $row;
            }
        }

        return $result;
    }

    // --------------------------------------------------------
    //  Abgeschlossene Quests eines Spielers
    // --------------------------------------------------------
    public static function getDoneQuests(int $uid): array
    {
        $verbindung = get_verbindung();
        $result     = [];

        $q = mysqli_query($verbindung,
            "SELECT pq.abgeschlossen_am, qt.titel
               FROM player_quests pq
               JOIN quest_templates qt ON qt.id = pq.quest_id
              WHERE pq.uid = " . intval($uid) . "
                AND pq.status = 'done'
              ORDER BY pq.abgeschlossen_am DESC"
        );
        while ($row = mysqli_fetch_assoc($q)) {
            $result[] = $row;
        }
        return $result;
    }
}
