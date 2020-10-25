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
 * $Id: guildinfo.php 394 2006-12-28 10:24:34Z zanix $
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

require_once 'settings.php';

//---[ Check for Guild Info ]------------
if (empty($guild_info)) {
    message_die($wordings[$roster_conf['roster_lang']]['nodata']);
}

// Get guild info from guild info check above
$GuildInfo = $guild_info['guild_info_text'];
$guildMOTD = $guild_info['guild_motd'];

$header_title = $wordings[$roster_conf['roster_lang']]['Guild_Info'];

require_once ROSTER_BASE . 'roster_header.tpl';

if (1 == $roster_conf['index_motd'] && !empty($guildMOTD)) {
    if ($roster_conf['motd_display_mode']) {
        print '<img src="motd.php" alt="Guild msg of the day"><br><br>';
    } else {
        echo '<span class="GMOTD">Guild MOTD: ' . $guildMOTD . '</span><br><br>';
    }
}

require_once(ROSTER_BASE . 'lib/menu.php');

if (!empty($GuildInfo)) {
    print border('syellow', 'start', $wordings[$roster_conf['roster_lang']]['Guild_Info']) . '<div class="GuildInfoText">' . nl2br($GuildInfo) . '</div>' . border('syellow', 'end');
}

require_once ROSTER_BASE . 'roster_footer.tpl';

require_once(XOOPS_ROOT_PATH . '/footer.php');
