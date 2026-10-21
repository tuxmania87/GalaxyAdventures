-- Quest Engine v2 Migration
-- Trigger-basiertes Quest-System
-- Parallel zum alten System — alte Tabellen (quests, erfolge) bleiben unberührt
--
-- Ausführen mit: mysql -u USER -p DBNAME < quest_v2_migration.sql

-- -----------------------------------------------------------------------
-- quest_templates
-- Definition einer Quest: Titel, Text, Voraussetzungen, Belohnung
-- -----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `quest_templates` (
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `titel`         VARCHAR(250) NOT NULL DEFAULT '',
    `beschreibung`  TEXT         NOT NULL,
    `abgabetext`    TEXT         NOT NULL,       -- Text wenn Quest abgeschlossen
    `vorquest_id`   INT          NOT NULL DEFAULT 0,  -- 0 = keine Voraussetzung
    `min_level`     INT          NOT NULL DEFAULT 0,
    `belohnung`     JSON         NOT NULL,        -- {"schiffe":["miranda"],"rohstoffe":{"erz":500},"credits":0}
    `repeatable`    TINYINT(1)   NOT NULL DEFAULT 0,  -- 1 = wiederholbar
    `aktiv`         TINYINT(1)   NOT NULL DEFAULT 1,  -- 0 = deaktiviert (nicht sichtbar)
    `erstellt_von`  INT          NOT NULL DEFAULT 0,  -- Account-ID des Admins
    `erstellt_am`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------
-- quest_steps
-- Einzelne Schritte (Trigger) einer Quest — werden der Reihe nach erledigt
-- -----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `quest_steps` (
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `quest_id`      INT          NOT NULL,
    `reihenfolge`   INT          NOT NULL DEFAULT 0,  -- Sortierung (0, 1, 2, ...)
    `trigger_typ`   VARCHAR(50)  NOT NULL,             -- z.B. 'kill_ships', 'build_building'
    `beschreibung`  VARCHAR(500) NOT NULL DEFAULT '',  -- Anzeige für den Spieler
    `parameter`     JSON         NOT NULL,             -- Trigger-spezifische Parameter
    -- Beispiel kill_ships:     {"anzahl": 5, "rasse": "klingon"}
    -- Beispiel build_building: {"gebaeude_id": 3, "anzahl": 2}
    -- Beispiel collect_resource: {"rohstoff": "erz", "menge": 1000}
    -- Beispiel scan_systems:   {"anzahl": 3}
    -- Beispiel fly_to:         {"x": 10, "y": 5, "system_id": 0}
    -- Beispiel deliver_item:   {"item_id": 7, "menge": 1}
    PRIMARY KEY (`id`),
    KEY `idx_quest_id` (`quest_id`),
    CONSTRAINT `fk_qs_quest` FOREIGN KEY (`quest_id`) REFERENCES `quest_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------
-- player_quests
-- Aktive/abgeschlossene Quests eines Spielers
-- -----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `player_quests` (
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `uid`           INT          NOT NULL,
    `quest_id`      INT          NOT NULL,
    `status`        ENUM('active','done','failed') NOT NULL DEFAULT 'active',
    `angenommen_am` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `abgeschlossen_am` DATETIME  NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_uid` (`uid`),
    KEY `idx_quest_id` (`quest_id`),
    CONSTRAINT `fk_pq_quest` FOREIGN KEY (`quest_id`) REFERENCES `quest_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------
-- player_quest_steps
-- Fortschritt je Schritt je Spieler
-- -----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `player_quest_steps` (
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `player_quest_id` INT        NOT NULL,
    `step_id`       INT          NOT NULL,
    `fortschritt`   INT          NOT NULL DEFAULT 0,
    `max`           INT          NOT NULL DEFAULT 1,   -- wird beim Annehmen aus quest_steps.parameter befüllt
    `erledigt`      TINYINT(1)   NOT NULL DEFAULT 0,
    `meta`          JSON         NULL DEFAULT NULL,    -- trigger-spezifische Zusatzdaten (z.B. bereits gezählte System-IDs)
    PRIMARY KEY (`id`),
    KEY `idx_pq_id` (`player_quest_id`),
    CONSTRAINT `fk_pqs_pq` FOREIGN KEY (`player_quest_id`) REFERENCES `player_quests` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pqs_step` FOREIGN KEY (`step_id`) REFERENCES `quest_steps` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- meta-Spalte nachrüsten falls Tabelle schon existiert (idempotent)
ALTER TABLE `player_quest_steps`
    ADD COLUMN IF NOT EXISTS `meta` JSON NULL DEFAULT NULL AFTER `erledigt`;

-- -----------------------------------------------------------------------
-- Beispiel-Daten: Eine einfache Quest-Kette (3 Quests)
-- -----------------------------------------------------------------------

-- Quest 1: Erkundungsauftrag (sofort verfügbar, Level 0)
INSERT INTO `quest_templates`
    (`titel`, `beschreibung`, `abgabetext`, `vorquest_id`, `min_level`, `belohnung`, `aktiv`, `erstellt_von`)
VALUES (
    'Erkundungsauftrag',
    'Melde dich bei Starfleet Command — scanne 3 Sternensysteme in der Umgebung und berichte zurück.',
    'Ausgezeichnet, Käpten. Ihre Daten werden in unsere Sternenkarten eingetragen. Hier ist Ihre Belohnung.',
    0, 0,
    '{"rohstoffe": {"erz": 200, "deuterium": 100}, "schiffe": [], "credits": 0}',
    1, 1
);

-- Quest 2: Rohstofflieferung (nach Quest 1, Level 1)
INSERT INTO `quest_templates`
    (`titel`, `beschreibung`, `abgabetext`, `vorquest_id`, `min_level`, `belohnung`, `aktiv`, `erstellt_von`)
VALUES (
    'Rohstofflieferung',
    'Die Kolonie braucht dringend Erz. Bringt 500 Einheiten Erz zu diesem Sektor.',
    'Die Lieferung ist angekommen. Die Kolonie dankt Ihnen. Nehmen Sie diese Frachter als Zeichen unserer Dankbarkeit.',
    1, 1,
    '{"rohstoffe": {}, "schiffe": ["hauler"], "credits": 0}',
    1, 1
);

-- Quest 3: Verteidigung (nach Quest 2, Level 2)
INSERT INTO `quest_templates`
    (`titel`, `beschreibung`, `abgabetext`, `vorquest_id`, `min_level`, `belohnung`, `aktiv`, `erstellt_von`)
VALUES (
    'Piraten abwehren',
    'Klingonische Piraten bedrohen unsere Handelsrouten. Vernichtet 5 feindliche Schiffe.',
    'Die Handelsrouten sind wieder sicher. Starfleet dankt Ihnen für Ihren Einsatz.',
    2, 2,
    '{"rohstoffe": {"erz": 1000, "deuterium": 500}, "schiffe": [], "credits": 0}',
    1, 1
);

-- Schritte für Quest 1: Erkundungsauftrag
INSERT INTO `quest_steps` (`quest_id`, `reihenfolge`, `trigger_typ`, `beschreibung`, `parameter`)
VALUES
(1, 0, 'scan_systems', '3 Sternensysteme scannen', '{"anzahl": 3}');

-- Schritte für Quest 2: Rohstofflieferung
INSERT INTO `quest_steps` (`quest_id`, `reihenfolge`, `trigger_typ`, `beschreibung`, `parameter`)
VALUES
(2, 0, 'collect_resource', '500 Erz im Frachtraum haben', '{"rohstoff": "erz", "menge": 500}');

-- Schritte für Quest 3: Piraten abwehren
INSERT INTO `quest_steps` (`quest_id`, `reihenfolge`, `trigger_typ`, `beschreibung`, `parameter`)
VALUES
(3, 0, 'kill_ships', '5 feindliche Schiffe vernichten', '{"anzahl": 5, "rasse": "klingon"}');
