CREATE TABLE wrx_roster_account (
    `account_id` SMALLINT(6) NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(30) NOT NULL DEFAULT '',
    `hash`       VARCHAR(32) NOT NULL DEFAULT '',
    PRIMARY KEY (`account_id`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_buffs (
    `member_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `name`      VARCHAR(96)      NOT NULL DEFAULT '',
    `rank`      VARCHAR(32)      NOT NULL DEFAULT '',
    `count`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `icon`      VARCHAR(64)      NOT NULL DEFAULT '',
    `tooltip`   MEDIUMTEXT       NOT NULL
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_config (
    `id`           INT(11) NOT NULL,
    `config_name`  VARCHAR(255) DEFAULT NULL,
    `config_value` TINYTEXT,
    `form_type`    MEDIUMTEXT,
    `config_type`  VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_guild (
    `guild_id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `guild_name`           VARCHAR(64)      NOT NULL DEFAULT '',
    `server`               VARCHAR(32)      NOT NULL DEFAULT '',
    `faction`              VARCHAR(8)                DEFAULT NULL,
    `guild_motd`           VARCHAR(255)     NOT NULL DEFAULT '',
    `guild_num_members`    INT(11)          NOT NULL DEFAULT '0',
    `guild_num_accounts`   INT(11)          NOT NULL DEFAULT '0',
    `update_time`          DATETIME                  DEFAULT NULL,
    `guild_dateupdatedutc` VARCHAR(18)               DEFAULT NULL,
    `GPversion`            VARCHAR(6)                DEFAULT NULL,
    `guild_info_text`      MEDIUMTEXT,
    PRIMARY KEY (`guild_id`),
    KEY `guild` (`guild_name`, `server`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_items (
    `member_id`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `item_name`     VARCHAR(96)      NOT NULL,
    `item_parent`   VARCHAR(64)      NOT NULL DEFAULT '',
    `item_slot`     VARCHAR(32)      NOT NULL DEFAULT '',
    `item_color`    VARCHAR(16)      NOT NULL DEFAULT '',
    `item_id`       VARCHAR(32)               DEFAULT NULL,
    `item_texture`  VARCHAR(64)      NOT NULL DEFAULT '',
    `item_quantity` INT(11)                   DEFAULT NULL,
    `item_tooltip`  MEDIUMTEXT       NOT NULL,
    `level`         INT(11)                   DEFAULT NULL,
    `item_level`    INT(11)                   DEFAULT NULL,
    PRIMARY KEY (`member_id`, `item_parent`, `item_slot`),
    KEY `parent` (`item_parent`),
    KEY `slot` (`item_slot`),
    KEY `name` (`item_name`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_mailbox (
    `member_id`         INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `mailbox_slot`      INT(11)          NOT NULL DEFAULT '0',
    `mailbox_coin`      INT(11)          NOT NULL DEFAULT '0',
    `mailbox_coin_icon` VARCHAR(64)      NOT NULL DEFAULT '',
    `mailbox_days`      FLOAT            NOT NULL DEFAULT '0',
    `mailbox_sender`    VARCHAR(30)      NOT NULL DEFAULT '',
    `mailbox_subject`   MEDIUMTEXT       NOT NULL,
    `item_icon`         VARCHAR(64)      NOT NULL DEFAULT '',
    `item_name`         VARCHAR(96)      NOT NULL,
    `item_quantity`     INT(11)                   DEFAULT NULL,
    `item_tooltip`      MEDIUMTEXT       NOT NULL,
    `item_color`        VARCHAR(16)      NOT NULL DEFAULT '',
    PRIMARY KEY (`member_id`, `mailbox_slot`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_members (
    `member_id`    INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(64)      NOT NULL DEFAULT '',
    `guild_id`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `class`        VARCHAR(32)      NOT NULL DEFAULT '',
    `level`        INT(11)          NOT NULL DEFAULT '0',
    `note`         VARCHAR(255)     NOT NULL DEFAULT '',
    `guild_rank`   INT(11)                   DEFAULT '0',
    `guild_title`  VARCHAR(64)               DEFAULT NULL,
    `officer_note` VARCHAR(255)     NOT NULL DEFAULT '',
    `zone`         VARCHAR(64)      NOT NULL DEFAULT '',
    `status`       VARCHAR(16)      NOT NULL DEFAULT '',
    `online`       INT(1)                    DEFAULT '0',
    `last_online`  DATETIME                  DEFAULT NULL,
    `update_time`  DATETIME                  DEFAULT NULL,
    `account_id`   SMALLINT(6)      NOT NULL DEFAULT '0',
    `inv`          TINYINT(4)       NOT NULL DEFAULT '3',
    `talents`      TINYINT(4)       NOT NULL DEFAULT '3',
    `quests`       TINYINT(4)       NOT NULL DEFAULT '3',
    `bank`         TINYINT(4)       NOT NULL DEFAULT '3',
    `spellbook`    TINYINT(4)       NOT NULL DEFAULT '3',
    `mail`         TINYINT(4)       NOT NULL DEFAULT '3',
    `recipes`      TINYINT(4)       NOT NULL DEFAULT '3',
    `bg`           TINYINT(4)       NOT NULL DEFAULT '3',
    `pvp`          TINYINT(4)       NOT NULL DEFAULT '3',
    `duels`        TINYINT(4)       NOT NULL DEFAULT '3',
    `money`        TINYINT(4)       NOT NULL DEFAULT '3',
    `item_bonuses` TINYINT(4)       NOT NULL DEFAULT '3',
    PRIMARY KEY (`member_id`),
    KEY `member` (`guild_id`, `name`),
    KEY `name` (`name`),
    KEY `class` (`class`),
    KEY `level` (`level`),
    KEY `guild_rank` (`guild_rank`),
    KEY `last_online` (`last_online`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_memberlog (
    `log_id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`    INT(11) UNSIGNED NOT NULL,
    `name`         VARCHAR(64)      NOT NULL DEFAULT '',
    `guild_id`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `class`        VARCHAR(32)      NOT NULL DEFAULT '',
    `level`        INT(11)          NOT NULL DEFAULT '0',
    `note`         VARCHAR(255)     NOT NULL DEFAULT '',
    `guild_rank`   INT(11)                   DEFAULT '0',
    `guild_title`  VARCHAR(64)               DEFAULT NULL,
    `officer_note` VARCHAR(255)     NOT NULL DEFAULT '',
    `update_time`  DATETIME                  DEFAULT NULL,
    `type`         TINYINT(1)       NOT NULL DEFAULT '0',
    PRIMARY KEY (`log_id`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_pets (
    `member_id`          INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `name`               VARCHAR(32)      NOT NULL DEFAULT '',
    `slot`               INT(11)          NOT NULL DEFAULT '0',
    `level`              INT(11)          NOT NULL DEFAULT '0',
    `health`             INT(11)                   DEFAULT NULL,
    `mana`               INT(11)                   DEFAULT NULL,
    `xp`                 VARCHAR(32)               DEFAULT NULL,
    `usedtp`             INT(11)                   DEFAULT NULL,
    `totaltp`            INT(11)                   DEFAULT NULL,
    `type`               VARCHAR(32)      NOT NULL DEFAULT '',
    `loyalty`            VARCHAR(32)      NOT NULL DEFAULT '',
    `icon`               VARCHAR(64)      NOT NULL DEFAULT '',
    `stat_int`           VARCHAR(32)               DEFAULT NULL,
    `stat_agl`           VARCHAR(32)               DEFAULT NULL,
    `stat_sta`           VARCHAR(32)               DEFAULT NULL,
    `stat_str`           VARCHAR(32)               DEFAULT NULL,
    `stat_spr`           VARCHAR(32)               DEFAULT NULL,
    `res_frost`          INT(11)                   DEFAULT NULL,
    `res_arcane`         INT(11)                   DEFAULT NULL,
    `res_fire`           INT(11)                   DEFAULT NULL,
    `res_shadow`         INT(11)                   DEFAULT NULL,
    `res_nature`         INT(11)                   DEFAULT NULL,
    `armor`              VARCHAR(32)               DEFAULT NULL,
    `defense`            INT(11)                   DEFAULT NULL,
    `melee_power`        INT(11)                   DEFAULT NULL,
    `melee_rating`       INT(11)                   DEFAULT NULL,
    `melee_range`        VARCHAR(16)               DEFAULT NULL,
    `melee_rangetooltip` TINYTEXT,
    `melee_powertooltip` TINYTEXT,
    PRIMARY KEY (`member_id`, `name`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_players (
    `member_id`             INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `name`                  VARCHAR(64)      NOT NULL DEFAULT '',
    `guild_id`              INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `dateupdatedutc`        VARCHAR(18)               DEFAULT NULL,
    `CPversion`             VARCHAR(6)                DEFAULT NULL,
    `race`                  VARCHAR(32)      NOT NULL DEFAULT '',
    `sex`                   VARCHAR(10)      NOT NULL DEFAULT '',
    `hearth`                VARCHAR(32)      NOT NULL DEFAULT '',
    `level`                 INT(11)          NOT NULL DEFAULT '0',
    `server`                VARCHAR(32)      NOT NULL DEFAULT '',
    `talent_points`         INT(11)          NOT NULL DEFAULT '0',
    `money_c`               INT(11)          NOT NULL DEFAULT '0',
    `money_s`               INT(11)          NOT NULL DEFAULT '0',
    `money_g`               INT(11)          NOT NULL DEFAULT '0',
    `exp`                   VARCHAR(32)      NOT NULL DEFAULT '',
    `class`                 VARCHAR(32)      NOT NULL DEFAULT '',
    `health`                INT(11)          NOT NULL DEFAULT '0',
    `maildateutc`           VARCHAR(18)               DEFAULT NULL,
    `melee_power`           INT(11)                   DEFAULT NULL,
    `melee_rating`          INT(11)                   DEFAULT NULL,
    `melee_range`           VARCHAR(32)               DEFAULT NULL,
    `melee_range_tooltip`   TINYTEXT,
    `melee_power_tooltip`   TINYTEXT,
    `ranged_power`          INT(11)                   DEFAULT NULL,
    `ranged_rating`         INT(11)                   DEFAULT NULL,
    `ranged_range`          VARCHAR(32)               DEFAULT NULL,
    `ranged_range_tooltip`  TINYTEXT,
    `ranged_power_tooltip`  TINYTEXT,
    `mana`                  INT(11)          NOT NULL DEFAULT '0',
    `stat_int`              INT(11)          NOT NULL DEFAULT '0',
    `stat_int_c`            INT(11)          NOT NULL DEFAULT '0',
    `stat_int_b`            INT(11)          NOT NULL DEFAULT '0',
    `stat_int_d`            INT(11)          NOT NULL DEFAULT '0',
    `stat_agl`              INT(11)          NOT NULL DEFAULT '0',
    `stat_agl_c`            INT(11)          NOT NULL DEFAULT '0',
    `stat_agl_b`            INT(11)          NOT NULL DEFAULT '0',
    `stat_agl_d`            INT(11)          NOT NULL DEFAULT '0',
    `stat_sta`              INT(11)          NOT NULL DEFAULT '0',
    `stat_sta_c`            INT(11)          NOT NULL DEFAULT '0',
    `stat_sta_b`            INT(11)          NOT NULL DEFAULT '0',
    `stat_sta_d`            INT(11)          NOT NULL DEFAULT '0',
    `stat_str`              INT(11)          NOT NULL DEFAULT '0',
    `stat_str_c`            INT(11)          NOT NULL DEFAULT '0',
    `stat_str_b`            INT(11)          NOT NULL DEFAULT '0',
    `stat_str_d`            INT(11)          NOT NULL DEFAULT '0',
    `stat_spr`              INT(11)          NOT NULL DEFAULT '0',
    `stat_spr_c`            INT(11)          NOT NULL DEFAULT '0',
    `stat_spr_b`            INT(11)          NOT NULL DEFAULT '0',
    `stat_spr_d`            INT(11)          NOT NULL DEFAULT '0',
    `stat_def`              INT(11)          NOT NULL DEFAULT '0',
    `stat_def_c`            INT(11)          NOT NULL DEFAULT '0',
    `stat_def_b`            INT(11)          NOT NULL DEFAULT '0',
    `stat_def_d`            INT(11)          NOT NULL DEFAULT '0',
    `stat_armor`            INT(11)          NOT NULL DEFAULT '0',
    `stat_armor_c`          INT(11)          NOT NULL DEFAULT '0',
    `stat_armor_b`          INT(11)          NOT NULL DEFAULT '0',
    `stat_armor_d`          INT(11)          NOT NULL DEFAULT '0',
    `res_frost`             INT(11)          NOT NULL DEFAULT '0',
    `res_frost_c`           INT(11)          NOT NULL DEFAULT '0',
    `res_frost_b`           INT(11)          NOT NULL DEFAULT '0',
    `res_frost_d`           INT(11)          NOT NULL DEFAULT '0',
    `res_arcane`            INT(11)          NOT NULL DEFAULT '0',
    `res_arcane_c`          INT(11)          NOT NULL DEFAULT '0',
    `res_arcane_b`          INT(11)          NOT NULL DEFAULT '0',
    `res_arcane_d`          INT(11)          NOT NULL DEFAULT '0',
    `res_fire`              INT(11)          NOT NULL DEFAULT '0',
    `res_fire_c`            INT(11)          NOT NULL DEFAULT '0',
    `res_fire_b`            INT(11)          NOT NULL DEFAULT '0',
    `res_fire_d`            INT(11)          NOT NULL DEFAULT '0',
    `res_shadow`            INT(11)          NOT NULL DEFAULT '0',
    `res_shadow_c`          INT(11)          NOT NULL DEFAULT '0',
    `res_shadow_b`          INT(11)          NOT NULL DEFAULT '0',
    `res_shadow_d`          INT(11)          NOT NULL DEFAULT '0',
    `res_nature`            INT(11)          NOT NULL DEFAULT '0',
    `res_nature_c`          INT(11)          NOT NULL DEFAULT '0',
    `res_nature_b`          INT(11)          NOT NULL DEFAULT '0',
    `res_nature_d`          INT(11)          NOT NULL DEFAULT '0',
    `pvp_ratio`             FLOAT            NOT NULL DEFAULT '0',
    `sessionHK`             INT(11)          NOT NULL DEFAULT '0',
    `sessionCP`             INT(11)          NOT NULL DEFAULT '0',
    `yesterdayHK`           INT(11)          NOT NULL DEFAULT '0',
    `yesterdayContribution` INT(11)          NOT NULL DEFAULT '0',
    `lifetimeHK`            INT(11)          NOT NULL DEFAULT '0',
    `lifetimeRankName`      VARCHAR(64)      NOT NULL DEFAULT '0',
    `honorpoints`           INT(11)          NOT NULL DEFAULT '0',
    `arenapoints`           INT(11)          NOT NULL DEFAULT '0',
    `Rankexp`               FLOAT            NOT NULL DEFAULT '0',
    `dodge`                 FLOAT            NOT NULL DEFAULT '0',
    `parry`                 FLOAT            NOT NULL DEFAULT '0',
    `block`                 FLOAT            NOT NULL DEFAULT '0',
    `mitigation`            FLOAT            NOT NULL DEFAULT '0',
    `crit`                  FLOAT            NOT NULL DEFAULT '0',
    `lifetimeHighestRank`   INT(11)          NOT NULL DEFAULT '0',
    `RankInfo`              INT(11)          NOT NULL DEFAULT '0',
    `RankName`              VARCHAR(64)      NOT NULL DEFAULT '',
    `RankIcon`              VARCHAR(64)      NOT NULL DEFAULT '',
    `clientLocale`          VARCHAR(4)       NOT NULL DEFAULT '',
    `timeplayed`            INT(11)          NOT NULL DEFAULT '0',
    `timelevelplayed`       INT(11)          NOT NULL DEFAULT '0',
    PRIMARY KEY (`member_id`),
    KEY `name` (`name`, `server`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_pvp2 (
    `member_id` INT(11) UNSIGNED    NOT NULL DEFAULT '0',
    `index`     INT(11) UNSIGNED    NOT NULL DEFAULT '0',
    `date`      DATETIME                     DEFAULT NULL,
    `name`      VARCHAR(32)         NOT NULL DEFAULT '',
    `guild`     VARCHAR(32)         NOT NULL DEFAULT '',
    `realm`     VARCHAR(96)         NOT NULL DEFAULT '',
    `race`      VARCHAR(32)         NOT NULL DEFAULT '',
    `class`     VARCHAR(32)         NOT NULL DEFAULT '',
    `zone`      VARCHAR(32)         NOT NULL DEFAULT '',
    `subzone`   VARCHAR(32)         NOT NULL DEFAULT '',
    `enemy`     TINYINT(4)          NOT NULL DEFAULT '0',
    `win`       TINYINT(4)          NOT NULL DEFAULT '0',
    `rank`      VARCHAR(32)         NOT NULL DEFAULT '',
    `bg`        TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
    `leveldiff` TINYINT(4)          NOT NULL DEFAULT '0',
    `honor`     SMALLINT(6)         NOT NULL DEFAULT '0',
    `column_id` MEDIUMINT(9)        NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`column_id`),
    KEY `date` (`date`, `guild`, `class`),
    KEY `member_id` (`member_id`, `index`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_quests (
    `member_id`   INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `quest_name`  VARCHAR(64)      NOT NULL DEFAULT '',
    `quest_index` INT(11)          NOT NULL DEFAULT '0',
    `quest_level` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `quest_tag`   VARCHAR(32)      NOT NULL DEFAULT '',
    `is_complete` INT(1)           NOT NULL DEFAULT '0',
    `zone`        VARCHAR(32)      NOT NULL DEFAULT '',
    PRIMARY KEY (`member_id`, `quest_name`),
    KEY `quest_index` (`quest_index`, `quest_level`, `quest_tag`),
    FULLTEXT KEY `quest_name` (`quest_name`),
    FULLTEXT KEY `zone` (`zone`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_realmstatus (
    `server_name`     VARCHAR(20) NOT NULL DEFAULT '',
    `servertype`      VARCHAR(20) NOT NULL DEFAULT '',
    `servertypecolor` VARCHAR(8)  NOT NULL DEFAULT '',
    `serverstatus`    VARCHAR(20) NOT NULL DEFAULT '',
    `serverpop`       VARCHAR(20) NOT NULL DEFAULT '',
    `serverpopcolor`  VARCHAR(8)  NOT NULL DEFAULT '',
    `timestamp`       TINYINT(2)  NOT NULL DEFAULT '0',
    UNIQUE KEY `server_name` (`server_name`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_recipes (
    `member_id`      INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `recipe_name`    VARCHAR(64)      NOT NULL DEFAULT '',
    `recipe_type`    VARCHAR(100)     NOT NULL DEFAULT '',
    `skill_name`     VARCHAR(64)      NOT NULL DEFAULT '',
    `difficulty`     INT(11)          NOT NULL DEFAULT '0',
    `item_color`     VARCHAR(16)      NOT NULL,
    `reagents`       MEDIUMTEXT       NOT NULL,
    `recipe_texture` VARCHAR(64)      NOT NULL DEFAULT '',
    `recipe_tooltip` MEDIUMTEXT       NOT NULL,
    `categories`     VARCHAR(64)      NOT NULL DEFAULT '',
    `level`          INT(11)                   DEFAULT NULL,
    `item_level`     INT(11)                   DEFAULT NULL,
    PRIMARY KEY (`member_id`, `skill_name`, `recipe_name`, `categories`),
    KEY `skill_nameI` (`skill_name`),
    KEY `recipe_nameI` (`recipe_name`),
    KEY `categoriesI` (`categories`),
    KEY `levelI` (`level`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_reputation (
    `member_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `faction`   VARCHAR(32)      NOT NULL DEFAULT '',
    `name`      VARCHAR(32)      NOT NULL DEFAULT '',
    `curr_rep`  INT(8)           NULL,
    `max_rep`   INT(8)           NULL,
    `AtWar`     INT(11)          NOT NULL DEFAULT '0',
    `Standing`  VARCHAR(32)               DEFAULT '',
    PRIMARY KEY (`member_id`, `name`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_skills (
    `member_id`   INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `skill_type`  VARCHAR(32)      NOT NULL DEFAULT '',
    `skill_name`  VARCHAR(32)      NOT NULL DEFAULT '',
    `skill_order` INT(11)          NOT NULL DEFAULT '0',
    `skill_level` VARCHAR(16)      NOT NULL DEFAULT '',
    PRIMARY KEY (`member_id`, `skill_name`),
    KEY `skill_type` (`skill_type`),
    KEY `skill_name` (`skill_name`),
    KEY `skill_order` (`skill_order`)
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_spellbook (
    `member_id`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `spell_name`    VARCHAR(64)      NOT NULL DEFAULT '',
    `spell_type`    VARCHAR(100)     NOT NULL DEFAULT '',
    `spell_texture` VARCHAR(64)      NOT NULL DEFAULT '',
    `spell_rank`    VARCHAR(64)      NOT NULL DEFAULT '',
    `spell_tooltip` MEDIUMTEXT       NOT NULL
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_spellbooktree (
    `member_id`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `spell_type`    VARCHAR(64)      NOT NULL DEFAULT '',
    `spell_texture` VARCHAR(64)      NOT NULL DEFAULT ''
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_talents (
    `member_id` INT(11)     NOT NULL DEFAULT '0',
    `name`      VARCHAR(64) NOT NULL DEFAULT '',
    `tree`      VARCHAR(64) NOT NULL DEFAULT '',
    `row`       TINYINT(4)  NOT NULL DEFAULT '0',
    `column`    TINYINT(4)  NOT NULL DEFAULT '0',
    `rank`      TINYINT(4)  NOT NULL DEFAULT '0',
    `maxrank`   TINYINT(4)  NOT NULL DEFAULT '0',
    `tooltip`   MEDIUMTEXT  NOT NULL,
    `texture`   VARCHAR(64) NOT NULL DEFAULT ''
)
    ENGINE = ISAM;

CREATE TABLE wrx_roster_talenttree (
    `member_id`   INT(11)     NOT NULL DEFAULT '0',
    `tree`        VARCHAR(64) NOT NULL DEFAULT '',
    `background`  VARCHAR(64) NOT NULL DEFAULT '',
    `order`       TINYINT(4)  NOT NULL DEFAULT '0',
    `pointsspent` TINYINT(4)  NOT NULL DEFAULT '0'
)
    ENGINE = ISAM;

INSERT INTO wrx_roster_config
VALUES (1, 'config_list', 'main_conf|guild_conf|menu_conf|display_conf|index_conf|char_conf|realmstatus_conf|data_links|guildbank_conf|update_access', 'display', 'master');
INSERT INTO wrx_roster_config
VALUES (2, 'roster_upd_pw', '60d605debf95b04881279358dbfdb7e1', 'password:30|30', 'master');
INSERT INTO wrx_roster_config
VALUES (3, 'roster_dbver', '4', 'display', 'master');
INSERT INTO wrx_roster_config
VALUES (4, 'version', '1.7.2', 'display', 'master');

INSERT INTO wrx_roster_config
VALUES (1000, 'sqldebug', '0', 'radio{on^1|off^0', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1001, 'debug_mode', '0', 'radio{on^1|off^0', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1002, 'sql_window', '0', 'radio{on^1|off^0', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1010, 'minCPver', '2.0.0', 'text{10|10', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1020, 'minGPver', '2.0.0', 'text{10|10', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1030, 'minPvPLogver', '0.6.1', 'text{10|10', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1040, 'roster_lang', 'enUS', 'function{rosterLangValue', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1060, 'website_address', '', 'text{128|30', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1070, 'roster_dir', '', 'text{128|30', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1080, 'server_name_comp', '0', 'radio{on^1|off^0', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1085, 'interface_url', 'img/', 'text{128|30', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1090, 'img_suffix', 'png', 'select{jpg^jpg|png^png|gif^gif', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1095, 'alt_img_suffix', 'gif', 'select{jpg^jpg|png^png|gif^gif', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1100, 'img_url', 'img/', 'text{128|30', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1110, 'timezone', 'EST', 'text{10|10', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1120, 'localtimeoffset', '-5', 'select{-12^-12|-11^-11|-10^-10|-9^-9|-8^-8|-7^-7|-6^-6|-5^-5|-4^-4|-3.5^-3.5|-3^-3|-2^-2|-1^-1|0^0|+1^1|+2^2|+3^3|+3.5^3.5|+4^4|+4.5^4.5|+5^5|+5.5^5.5|+6^6|+6.5^6.5|+7^7|+8^8|+9^9|+9.5^9.5|+10^10|+11^11|+12^12|+13^13', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1130, 'pvp_log_allow', '1', 'radio{yes^1|no^0', 'main_conf');
INSERT INTO wrx_roster_config
VALUES (1140, 'use_update_triggers', '1', 'radio{on^1|off^0', 'main_conf');

INSERT INTO wrx_roster_config
VALUES (2000, 'guild_name', 'guildName', 'text{50|30', 'guild_conf');
INSERT INTO wrx_roster_config
VALUES (2010, 'server_name', 'realmName', 'text{50|30', 'guild_conf');
INSERT INTO wrx_roster_config
VALUES (2020, 'guild_desc', 'A Great WoW Guild', 'text{255|30', 'guild_conf');
INSERT INTO wrx_roster_config
VALUES (2030, 'server_type', 'PvE', 'select{PvE^PvE|PvP^PvP|RP^RP|RPPvP^RPPvP', 'guild_conf');
INSERT INTO wrx_roster_config
VALUES (2040, 'alt_type', 'alt', 'text{30|30', 'guild_conf');
INSERT INTO wrx_roster_config
VALUES (2050, 'alt_location', 'note', 'select{Player Note^note|Officer Note^officer_note|Guild Rank Number^guild_rank|Guild Title^guild_title', 'guild_conf');

INSERT INTO wrx_roster_config
VALUES (3000, 'index_pvplist', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3010, 'index_hslist', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3015, 'hspvp_list_disp', 'show', 'radio{show^show|hide^hide', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3020, 'index_member_tooltip', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3030, 'index_update_inst', '0', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3040, 'index_sort', '', 'select{Default Sort^|Name^name|Class^class|Level^level|Guild Title^guild_title|PvP Rank^RankName|Note^note|Hearthstone Location^hearth|Zone Location^zone', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3050, 'index_motd', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3060, 'index_level_bar', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3070, 'index_iconsize', '16', 'select{8px^8|9px^9|10px^10|11px^11|12px^12|13px^13|14px^14|15px^15|16px^16|17px^17|18px^18|19px^19|20px^20', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3080, 'index_tradeskill_icon', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3090, 'index_tradeskill_loc', 'professions', 'select{Name^name|Class^class|Level^level|Guild Title^guild_title|PvP Rank^RankName|Note^note|Professions^professions|Hearthed^hearth|Last Zone^zone|Last On-line^lastonline|Last Updated^last_update', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3100, 'index_class_color', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3110, 'index_classicon', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3120, 'index_honoricon', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3130, 'index_prof', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3140, 'index_currenthonor', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3150, 'index_note', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3160, 'index_title', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3170, 'index_hearthed', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3180, 'index_zone', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3190, 'index_lastonline', '1', 'radio{on^1|off^0', 'index_conf');
INSERT INTO wrx_roster_config
VALUES (3200, 'index_lastupdate', '1', 'radio{on^1|off^0', 'index_conf');

INSERT INTO wrx_roster_config
VALUES (4000, 'menu_left_pane', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4010, 'menu_right_pane', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4020, 'menu_memberlog', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4040, 'menu_guild_info', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4050, 'menu_stats_page', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4055, 'menu_pvp_page', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4060, 'menu_honor_page', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4070, 'menu_guildbank', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4080, 'menu_keys_page', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4090, 'menu_tradeskills_page', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4100, 'menu_update_page', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4110, 'menu_quests_page', '1', 'radio{on^1|off^0', 'menu_conf');
INSERT INTO wrx_roster_config
VALUES (4120, 'menu_search_page', '1', 'radio{on^1|off^0', 'menu_conf');

INSERT INTO wrx_roster_config
VALUES (5000, 'stylesheet', 'css/styles.css', 'text{128|30', 'display_conf');
INSERT INTO wrx_roster_config
VALUES (5005, 'roster_js', 'css/js/mainjs.js', 'text{128|30', 'display_conf');
INSERT INTO wrx_roster_config
VALUES (5008, 'tabcontent', 'css/js/tabcontent.js', 'text{128|30', 'display_conf');
INSERT INTO wrx_roster_config
VALUES (5010, 'overlib', 'css/js/overlib.js', 'text{128|30', 'display_conf');
INSERT INTO wrx_roster_config
VALUES (5015, 'overlib_hide', 'css/js/overlib_hideform.js', 'text{128|30', 'display_conf');
INSERT INTO wrx_roster_config
VALUES (5020, 'logo', 'img/wowroster_logo.jpg', 'text{128|30', 'display_conf');
INSERT INTO wrx_roster_config
VALUES (5025, 'roster_bg', 'img/wowroster_bg.jpg', 'text{128|30', 'display_conf');
INSERT INTO wrx_roster_config
VALUES (5030, 'motd_display_mode', '1', 'radio{Image^1|Text^0', 'display_conf');
INSERT INTO wrx_roster_config
VALUES (5035, 'compress_note', '1', 'radio{Icon^1|Text^0', 'display_conf');
INSERT INTO wrx_roster_config
VALUES (5040, 'signaturebackground', 'img/default.png', 'text{128|30', 'display_conf');
INSERT INTO wrx_roster_config
VALUES (5050, 'processtime', '1', 'radio{on^1|off^0', 'display_conf');

INSERT INTO wrx_roster_config
VALUES (6000, 'questlink_1', '1', 'radio{on^1|off^0', 'data_links');
INSERT INTO wrx_roster_config
VALUES (6010, 'questlink_2', '1', 'radio{on^1|off^0', 'data_links');
INSERT INTO wrx_roster_config
VALUES (6020, 'questlink_3', '1', 'radio{on^1|off^0', 'data_links');
INSERT INTO wrx_roster_config
VALUES (6100, 'profiler', 'http://www.rpgoutfitter.com/Addons/CharacterProfiler.cfm', 'text{128|30', 'data_links');
INSERT INTO wrx_roster_config
VALUES (6110, 'pvplogger', 'http://www.wowroster.net/Downloads/details/id=51.html', 'text{128|30', 'data_links');
INSERT INTO wrx_roster_config
VALUES (6120, 'uploadapp', 'http://www.wowroster.net/Downloads/c=2.html', 'text{128|30', 'data_links');

INSERT INTO wrx_roster_config
VALUES (7000, 'char_bodyalign', 'center', 'select{left^left|center^center|right^right', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7010, 'char_header_logo', '0', 'radio{on^1|off^0', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7015, 'show_talents', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7020, 'show_spellbook', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7030, 'show_mail', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7040, 'show_inventory', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7050, 'show_money', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7060, 'show_bank', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7070, 'show_recipes', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7080, 'show_quests', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7090, 'show_bg', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7100, 'show_pvp', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7110, 'show_duels', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7120, 'show_item_bonuses', '2', 'radio{on^1|off^0|user^2', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7130, 'show_signature', '0', 'radio{yes^1|no^0', 'char_conf');
INSERT INTO wrx_roster_config
VALUES (7140, 'show_avatar', '0', 'radio{yes^1|no^0', 'char_conf');

INSERT INTO wrx_roster_config
VALUES (8000, 'realmstatus_url', 'http://www.worldofwarcraft.com/realmstatus/status.xml',
        'select{US Servers^http://www.worldofwarcraft.com/realmstatus/status.xml|European English^http://www.wow-europe.com/en/serverstatus/index.html|European German^http://www.wow-europe.com/de/serverstatus/index.html|European French^http://www.wow-europe.com/fr/serverstatus/index.html',
        'realmstatus_conf');
INSERT INTO wrx_roster_config
VALUES (8010, 'rs_display', 'full', 'select{full^full|half^half', 'realmstatus_conf');
INSERT INTO wrx_roster_config
VALUES (8020, 'rs_mode', '1', 'radio{Image^1|DIV Container^0', 'realmstatus_conf');
INSERT INTO wrx_roster_config
VALUES (8030, 'realmstatus', '', 'text{50|30', 'realmstatus_conf');

INSERT INTO wrx_roster_config
VALUES (9000, 'guildbank_ver', '', 'select{Table^|Inventory^2', 'guildbank_conf');
INSERT INTO wrx_roster_config
VALUES (9010, 'bank_money', '0', 'radio{yes^1|no^0', 'guildbank_conf');
INSERT INTO wrx_roster_config
VALUES (9020, 'banker_rankname', 'BankMule', 'text{50|30', 'guildbank_conf');
INSERT INTO wrx_roster_config
VALUES (9030, 'banker_fieldname', 'note', 'select{Player Note^note|Officer Note^officer_note|Guild Rank Number^guild_rank|Guild Title^guild_title|Player Name^name', 'guildbank_conf');

INSERT INTO wrx_roster_config
VALUES (10000, 'authenticated_user', '1', 'radio{enable^1|disable^0', 'update_access');
