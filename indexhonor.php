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
 * $Id: indexhonor.php 374 2006-12-23 21:50:48Z zanix $
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

$header_title = $wordings[$roster_conf['roster_lang']]['menuhonor'];
require_once(ROSTER_BASE . 'roster_header.tpl');

// Additional querries needed for this page
// Make sure the last item in this array DOES NOT have a (,) at the end
$additional_sql = [
    '`players`.`RankIcon`, ',
    '`players`.`Rankexp`, ',
    '`players`.`sessionHK`, ',
    '`players`.`sessionCP`, ',
    '`players`.`yesterdayHK`, ',
    '`players`.`yesterdayContribution`, ',
    '`players`.`lifetimeHK`, ',
    '`players`.`lifetimeRankName`, ',
    '`players`.`honorpoints`, ',
    '`players`.`arenapoints` ',
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
    'sessionHK' => [
        'lang_field' => 'Today HK',
        'order' => [ '`players`.`sessionHK` DESC' ],
        'order_d' => [ '`players`.`sessionHK` ASC' ],
    ],
];

$FIELD[] = [
    'sessionCP' => [
        'lang_field' => 'Today CP',
        'order' => [ '`players`.`sessionCP` DESC' ],
        'order_d' => [ '`players`.`sessionCP` ASC' ],
    ],
];

$FIELD[] = [
    'yesterdayHK' => [
        'lang_field' => 'Yest HK',
        'order' => [ '`players`.`yesterdayHK` DESC' ],
        'order_d' => [ '`players`.`yesterdayHK` ASC' ],
    ],
];

$FIELD[] = [
    'yesterdayContribution' => [
        'lang_field' => 'Yest CP',
        'order' => [ '`players`.`yesterdayContribution` DESC' ],
        'order_d' => [ '`players`.`yesterdayContribution` ASC' ],
    ],
];

$FIELD[] = [
    'lifetimeHK' => [
        'lang_field' => 'Life HK',
        'order' => [ '`players`.`lifetimeHK` DESC' ],
        'order_d' => [ '`players`.`lifetimeHK` ASC' ],
    ],
];

$FIELD[] = [
    'lifetimeRankName' => [
        'lang_field' => 'Highest Rank',
        'order' => [ '`players`.`lifetimeRankName` DESC' ],
        'order_d' => [ '`players`.`lifetimeRankName` ASC' ],
    ],
];

$FIELD[] = [
    'honorpoints' => [
        'lang_field' => 'Honor Points',
        'order' => [ '`players`.`honorpoints` DESC' ],
        'order_d' => [ '`players`.`honorpoints` ASC' ],
    ],
];

$FIELD[] = [
    'arenapoints' => [
        'lang_field' => 'Arena Points',
        'order' => [ '`players`.`arenapoints` DESC' ],
        'order_d' => [ '`players`.`arenapoints` ASC' ],
    ],
];

require_once(ROSTER_BASE . 'memberslist.php');

require_once(ROSTER_BASE . 'roster_footer.tpl');

require_once(XOOPS_ROOT_PATH . '/footer.php');
