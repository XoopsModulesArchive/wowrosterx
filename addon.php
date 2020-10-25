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
 * $Id: addon.php 394 2006-12-28 10:24:34Z zanix $
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

require_once __DIR__ . '/settings.php';

//---[ Check for Guild Info ]------------
if (empty($guild_info)) {
    die_quietly($wordings[$roster_conf['roster_lang']]['nodata']);
}

// Get the addon's location
$addonDir = ROSTER_ADDONS . $_REQUEST['roster_addon_name'] . DIR_SEP;

// Get the addon's index file
$addonFile = $addonDir . 'index.php';

// Get the addon's css style
$cssFile = $addonDir . 'default.css';

// Get the addon's locale file
$localizationFile = $addonDir . 'localization.php';

// Get the addon's config file
$configFile = $addonDir . 'conf.php';

// Initialize css holder
$css = '';

// Make the header/menu/footer show by default
$roster_show_header = true;
$roster_show_menu = true;
$roster_show_footer = true;

// Check to see if the index file exists
if (file_exists($addonFile)) {
    $script_filename = 'addon.php?roster_addon_name=' . $_REQUEST['roster_addon_name'];

    // Set the css for the template set in conf.php

    if (file_exists($cssFile)) {
        $css = '/addons/' . $_REQUEST['roster_addon_name'] . '/default.css';
    }

    // Include localization variables

    if (file_exists($localizationFile)) {
        require_once $localizationFile;
    }

    // Include addon's conf.php settings

    if (file_exists($configFile)) {
        require_once $configFile;
    }

    // The addon will now assign its output to $content

    ob_start();

    require_once $addonFile;

    $content = ob_get_contents();

    ob_end_clean();
} else {
    $content = '<b>The addon "' . $_REQUEST['roster_addon_name'] . '" does not exist!</b>';
}

// Everything after this line will have to be changed to integrate into smarty! ;)

// Pass all the css to $more_css which is a placeholder in roster_header for more css style defines
if ('' != $css) {
    $more_css = '  <link rel="stylesheet" type="text/css" href="' . $roster_conf['roster_dir'] . $css . '">' . "\n";
}

if ($roster_show_header) {
    require_once(ROSTER_BASE . 'roster_header.tpl');
}
if ($roster_show_menu) {
    require_once(ROSTER_LIB . 'menu.php');
}

echo $content;

if ($roster_show_footer) {
    require_once(ROSTER_BASE . 'roster_footer.tpl');
}

require_once(XOOPS_ROOT_PATH . '/footer.php');
