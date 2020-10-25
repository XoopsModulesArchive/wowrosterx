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
 * $Id: char.php 204 2006-09-17 09:31:44Z zanix $
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

$name = ($_GET['name'] ?? '');
$header_title = 'Character Stats for: ' . $name;
require_once(ROSTER_BASE . 'roster_header.tpl');

require_once(ROSTER_BASE . 'memberdetails.php');

require_once(ROSTER_BASE . 'roster_footer.tpl');

require_once(XOOPS_ROOT_PATH . '/footer.php');
