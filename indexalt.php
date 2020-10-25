<?php
/******************************
 * WoWRoster.net  Roster
 * Copyright 2002-2006
 * Licensed under the Creative Commons
 * "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * Short summary
 *  http://creativecommons.org/licenses/by-nc-sa/2.5/
 *
 * Full license information
 *  http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
 * -----------------------------
 *
 * $Id: indexalt.php 72 2006-08-15 22:14:50Z zanix $
 *
 ******************************/

/**
 * Xoops modifications have been made to this file
 *
 * WoW Roster X - WoW Roster for Xoops
 *
 * @author Mike DeShane (mdeshane@pkcomp.net)
 * @copyright 2006 Mike DeShane, US
 */
require_once('../../mainfile.php');

global $xoopsOption,$xoopsUser,$xoopsTpl,$xoopsDB;

require_once(XOOPS_ROOT_PATH . '/header.php');

require_once  'settings.php';

$header_title = $wordings[$roster_conf['roster_lang']]['alternate'];
require_once(ROSTER_BASE . 'roster_header.tpl');

// Additional querries needed for this page
// Make sure the last item in this array DOES NOT have a (,) at the end
$additional_sql = [
    '`players`.`health`, ',
    "IF( `players`.`health` IS NULL OR `players`.`health` = '', 1, 0 ) AS 'hisnull', ",
    '`players`.`mana`, ',
    "IF( `players`.`mana` IS NULL OR `players`.`mana` = '', 1, 0 ) AS 'misnull', ",
    '`players`.`money_g`, ',
    '`players`.`money_s`, ',
    '`players`.`money_c`, ',
    '`players`.`stat_armor`, ',
    '`players`.`stat_armor_c`, ',
    '`players`.`stat_armor_b`, ',
    '`players`.`stat_armor_d`, ',
    "IF( `players`.`stat_armor_c` IS NULL OR `players`.`stat_armor_c` = '', 1, 0 ) AS 'aisnull', ",
    '`players`.`mitigation`, ',
    '`players`.`dodge`, ',
    "IF( `players`.`dodge` IS NULL OR `players`.`dodge` = '', 1, 0 ) AS 'disnull', ",
    '`players`.`parry`, ',
    "IF( `players`.`parry` IS NULL OR `players`.`parry` = '', 1, 0 ) AS 'pisnull', ",
    '`players`.`block`, ',
    "IF( `players`.`block` IS NULL OR `players`.`block` = '', 1, 0 ) AS 'bisnull', ",
    '`players`.`crit`, ',
    "IF( `players`.`crit` IS NULL OR `players`.`crit` = '', 1, 0 ) AS 'cisnull' ",
];

$FIELD[] = [
    'name' => [
        'lang_field' => 'name',
        'order' => [ '`members`.`name` ASC' ],
        'order_d' => [ '`members`.`name` DESC' ],
        'required' => true,
        'default' => true,
        'value' => 'name_value',
    ],
];

$FIELD[] = [
    'class' => [
        'lang_field' => 'class',
        'divider' => true,
        'divider_value' => 'class_divider',
        'order' => [ '`members`.`class` ASC' ],
        'order_d' => [ '`members`.`class` DESC' ],
        'default' => true,
        'value' => 'class_value',
    ],
];

$FIELD[] = [
    'level' => [
        'lang_field' => 'level',
        'divider' => true,
        'divider_prefix' => 'Level ',
        'order_d' => [ '`members`.`level` ASC' ],
        'default' => true,
        'value' => 'level_value',
    ],
];

$FIELD[] = [
    'health' => [
        'lang_field' => 'health',
        'order' => [ 'hisnull', '`players`.`health` DESC' ],
        'order_d' => [ 'hisnull', '`players`.`health` ASC' ],
    ],
];

$FIELD[] = [
    'mana' => [
        'lang_field' => 'mana',
        'order' => [ 'misnull', '`players`.`mana` DESC' ],
        'order_d' => [ 'misnull', '`players`.`mana` ASC' ],
    ],
];

if ($roster_conf['show_money']) {
    $FIELD[] = [
        'money_g' => [
            'lang_field' => 'gold',
            'order' => [ '`players`.`money_g` DESC', '`players`.`money_s` DESC', '`players`.`money_c` DESC' ],
            'order_d' => [ '`players`.`money_g` ASC', '`players`.`money_s` ASC', '`players`.`money_c` ASC' ],
            'value' => 'money_value',
        ],
    ];
}

$FIELD[] = [
    'stat_armor_c' => [
        'lang_field' => 'armor',
        'order' => [ 'aisnull', '`players`.`stat_armor_c` DESC' ],
        'order_d' => [ 'aisnull', '`players`.`stat_armor_c` ASC' ],
        'value' => 'armor_value',
    ],
];

$FIELD[] = [
    'dodge' => [
        'lang_field' => 'dodge',
        'order' => [ 'disnull', '`players`.`dodge` DESC' ],
        'order_d' => [ 'disnull', '`players`.`dodge` ASC' ],
    ],
];

$FIELD[] = [
    'parry' => [
        'lang_field' => 'parry',
        'order' => [ 'pisnull', '`players`.`parry` DESC' ],
        'order_d' => [ 'pisnull', '`players`.`parry` ASC' ],
    ],
];

$FIELD[] = [
    'block' => [
        'lang_field' => 'block',
        'order' => [ 'bisnull', '`players`.`block` DESC' ],
        'order_d' => [ 'bisnull', '`players`.`block` ASC' ],
    ],
];

$FIELD[] = [
    'crit' => [
        'lang_field' => 'crit',
        'order' => [ 'cisnull', '`players`.`crit` DESC' ],
        'order_d' => [ 'cisnull', '`players`.`crit` ASC' ],
    ],
];

require_once(ROSTER_BASE . 'memberslist.php');

require_once(ROSTER_BASE . 'roster_footer.tpl');

require_once(XOOPS_ROOT_PATH . '/footer.php');
