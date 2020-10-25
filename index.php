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
 * $Id: index.php 267 2006-10-22 06:21:03Z zanix $
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

require_once(ROSTER_BASE . 'roster_header.tpl');

// Additional querries needed for this page
// Make sure the last item in this array DOES NOT have a (,) at the end
$additional_sql = [
    '`players`.`RankIcon`, ',
    '`players`.`Rankexp`, ',
    '`players`.`hearth`, ',
    "IF( `players`.`hearth` IS NULL OR `players`.`hearth` = '', 1, 0 ) AS 'hisnull', ",
    "`players`.`dateupdatedutc` AS 'last_update', ",
    "IF( `players`.`dateupdatedutc` IS NULL OR `players`.`dateupdatedutc` = '', 1, 0 ) AS 'luisnull' ",
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

if (1 == $roster_conf['index_title']) {
    $FIELD[] = [
        'guild_title' => [
            'lang_field' => 'title',
            'divider' => true,
            'order' => [ '`members`.`guild_rank` ASC' ],
            'order_d' => [ '`members`.`guild_rank` DESC' ],
        ],
    ];
}

if (1 == $roster_conf['index_currenthonor']) {
    $FIELD[] = [
        'RankName' => [
            'lang_field' => 'currenthonor',
            'divider' => true,
            'order' => [ 'risnull', '`players`.`RankInfo` DESC' ],
            'order_d' => [ 'risnull', '`players`.`RankInfo` ASC' ],
            'value' => 'honor_value',
        ],
    ];
}

if (1 == $roster_conf['index_note'] && 0 == $roster_conf['compress_note']) {
    $FIELD[] = [
        'note' => [
            'lang_field' => 'note',
            'order' => [ 'nisnull', '`members`.`note` ASC' ],
            'order_d' => [ 'nisnull', '`members`.`note` DESC' ],
            'value' => 'note_value',
        ],
    ];
}

if (1 == $roster_conf['index_prof']) {
    $FIELD[] = [
        'professions' => [
            'lang_field' => 'professions',
        ],
    ];
}

if (1 == $roster_conf['index_hearthed']) {
    $FIELD[] = [
        'hearth' => [
            'lang_field' => 'hearthed',
            'divider' => true,
            'order' => [ 'hisnull', 'hearth ASC' ],
            'order_d' => [ 'hisnull', 'hearth DESC' ],
        ],
    ];
}

if (1 == $roster_conf['index_zone']) {
    $FIELD[] = [
        'zone' => [
            'lang_field' => 'zone',
            'divider' => true,
            'order' => [ '`members`.`zone` ASC' ],
            'order_d' => [ '`members`.`zone` DESC' ],
        ],
    ];
}

if (1 == $roster_conf['index_lastonline']) {
    $FIELD[] = [
        'last_online' => [
            'lang_field' => 'lastonline',
            'order' => [ '`members`.`last_online` DESC' ],
            'order_d' => [ '`members`.`last_online` ASC' ],
        ],
    ];
}

if (1 == $roster_conf['index_lastupdate']) {
    $FIELD[] = [
        'last_update' => [
            'lang_field' => 'lastupdate',
            'order' => [ 'luisnull', 'last_update DESC' ],
            'order_d' => [ 'luisnull', 'last_update ASC' ],
            'value' => 'last_up_value',
        ],
    ];
}

if (1 == $roster_conf['index_note'] && 1 == $roster_conf['compress_note']) {
    $FIELD[] = [
        'note' => [
            'lang_field' => 'note',
            'order' => [ 'nisnull', '`members`.`note` ASC' ],
            'order_d' => [ 'nisnull', '`members`.`note` DESC' ],
            'value' => 'note_value',
        ],
    ];
}

require_once(ROSTER_BASE . 'memberslist.php');

require_once(ROSTER_BASE . 'roster_footer.tpl');

require_once(XOOPS_ROOT_PATH . '/footer.php');
