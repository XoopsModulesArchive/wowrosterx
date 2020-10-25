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
 * $Id: settings.php 394 2006-12-28 10:24:34Z zanix $
 ******************************/
/**
 * Xoops modifications have been made to this file
 *
 * WoW Roster X - WoW Roster for Xoops
 *
 * @author Mike DeShane (mdeshane@pkcomp.net)
 * @copyright 2006 Mike DeShane, US
 */
global $xoopsOption,$xoopsUser,$xoopsTpl,$xoopsDB;

require XOOPS_ROOT_PATH . '/modules/wowrosterx/lib/defines.inc';

define('WRX_INST_LINK', (XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin&op=install&module=wowrosterx'));

define('WRX_UPD_LINK', (XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin&op=update&module=wowrosterx'));

if (eregi(basename(__FILE__), $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly!");
}

/**
 * Set PHP error reporting
 */
error_reporting(E_ALL ^ E_NOTICE);

// Be paranoid with passed vars
// Destroy GET/POST/Cookie variables from the global scope
if (0 != (int)ini_get('register_globals')) {
    foreach ($_REQUEST as $key => $val) {
        if (isset($$key)) {
            unset($$key);
        }
    }
}

/**
 * Begin Roster Timing
 */
$starttime = explode(' ', microtime());
define('ROSTER_STARTTIME', $starttime[1] + $starttime[0]);

/**
 * OS specific Directory Seperator
 */
define('DIR_SEP', DIRECTORY_SEPARATOR);

/**
 * Get the url
 */
$url = explode('/', 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']);
array_pop($url);

/**
 * URL to roster's root directory
 */
define('ROSTER_URL', XOOPS_URL);

/**
 * Base, absolute roster directory
 */
define('ROSTER_BASE', WRX_PATH . DIR_SEP);

/**
 * Base, absolute roster library directory
 */
define('ROSTER_LIB', ROSTER_BASE . 'lib' . DIR_SEP);

/**
 * Base, absolute roster admin directory
 */
define('ROSTER_ADMIN', ROSTER_BASE . 'admin' . DIR_SEP);

/**
 * Base, absolute roster addons directory
 */
define('ROSTER_ADDONS', ROSTER_BASE . 'addons' . DIR_SEP);

/**
 * Full path to roster config file
 */
define('ROSTER_CONF_FILE', ROSTER_BASE . 'conf.php');

/**
 * If conf.php is not found, then die to installer link
 * Modified for Xoops Module installer
 */
if (!file_exists(ROSTER_CONF_FILE)) {
    exit("<center>Error RCF: Roster is not installed<br>\n<a href=" . WRX_INST_LINK . '>INSTALL</a></center>');
}  
    require_once(ROSTER_CONF_FILE);

/**
 * If ROSTER_INSTALLED is not defined, then die to installer link
 * Modified for Xoops Module Installer
 */
if (!defined('ROSTER_INSTALLED')) {
    exit("<center>Error RI: Roster is not installed<br>\n<a href=" . WRX_INST_LINK . '>INSTALL</a></center>');
}

/**
 * Include roster db file
 */
require_once(ROSTER_LIB . 'wowdb.php');

/**
 * Establish our connection and select our database
 */
$roster_dblink = $wowdb->connect($db_host, $db_user, $db_passwd, $db_name);
if (!$roster_dblink) {
    die(basename(__FILE__) . ': line[' . (__LINE__) . ']<br>' . 'Could not connect to database "' . $db_name . '"<br>MySQL said:<br>' . $wowdb->error());
}

/**
 * NULL DB Connect Info for Safety
 */
$db_user = null;
$db_passwd = null;

/**
 * Include constants file
 */
require_once(ROSTER_LIB . 'constants.php');

/**
 * Include common functions
 **/
require_once(ROSTER_LIB . 'commonfunctions.lib.php');

/**
 * Slash global data if magic_quotes_gpc is off.
 */
if (!get_magic_quotes_gpc()) {
    $_GET = escape_array($_GET);

    $_POST = escape_array($_POST);

    $_COOKIE = escape_array($_COOKIE);

    $_REQUEST = escape_array($_REQUEST);
}

/**
 * Get the current config values
 * Modified for Xoops Module Installer
 */
$sql = 'SELECT `config_name`, `config_value` FROM `' . ROSTER_CONFIGTABLE . '` ORDER BY `id` ASC;';
$results = $wowdb->query($sql);

if (!$results || 0 == $wowdb->num_rows($results)) {
    die("Error RCT: Cannot get roster configuration from database<br>\nMySQL Said: " . $wowdb->error() . "<br><br>\nYou might not have roster installed<br>\n<a href=" . WRX_INST_LINK . '>INSTALL</a>');
}

/**
 * Fill the config array with values
 */
while ($row = $wowdb->fetch_assoc($results)) {
    $roster_conf[$row['config_name']] = stripslashes($row['config_value']);
}
$wowdb->free_result($results);

/**
 * Set SQL debug value
 */
$wowdb->setSQLDebug($roster_conf['sqldebug']);

/**
 * Include locale files
 */
$localeFilePath = ROSTER_BASE . 'localization' . DIR_SEP;
if ($handle = opendir($localeFilePath)) {
    while (false !== ($file = readdir($handle))) {
        if ('.' != $file && '..' != $file) {
            $localeFiles[] = $file;
        }
    }
}

/**
 * Die if the locale directory cannot be read
 */
if (!is_array($localeFiles)) {
    die('Cannot read the directory [' . $localeFilePath . ']');
}

/**
 * Include every locale file
 * And fill the $roster_conf['multilanguages'] array
 */
foreach ($localeFiles as $file) {
    if (file_exists($localeFilePath . $file) && !is_dir($localeFilePath . $file)) {
        require_once($localeFilePath . $file);

        $roster_conf['multilanguages'][] = mb_substr($file, 0, 4);
    }
}

/**
 * If the version doesnt match the one in constants, redirect to upgrader
 * Modified for Xoops Module Updater
 */
if (empty($roster_conf['version']) || $roster_conf['version'] < ROSTER_VERSION) {
    die_quietly('Error RCV: Looks like you\'ve loaded a new version of Roster<br>
<br>
Your Version: <span class="red">' . $roster_conf['version'] . '</span><br>
New Version: <span class="green">' . ROSTER_VERSION . '</span><br>
<br>
<a href="' . WRX_UPD_LINK . '" style="border:1px outset white;padding:2px 6px 2px 6px;">UPDATE</a>', 'Update Roster');
}

/**
 * More Xoops Path modifications
 * WRX - Mike DeShane - PKComp.net
 */
$rs_dir = (dirname($_SERVER['PHP_SELF']));

/**
 * If the roster_dir config value is empty update it with the module path
 */
if (empty($roster_conf['roster_dir'])) {
    $sql = 'UPDATE `' . ROSTER_CONFIGTABLE . "` SET `config_value` = '" . $rs_dir . "' WHERE `id` = 1070";

    $update = $wowdb->query($sql);

    return $update;
}

/**
 * If the website_address config value is empty update it with the XOOPS main URL
 */
if (empty($roster_conf['website_address'])) {
    $sql = 'UPDATE `' . ROSTER_CONFIGTABLE . "` SET `config_value` = '" . XOOPS_URL . "' WHERE `id` = 1060";

    $update = $wowdb->query($sql);

    return $update;
}

/**
 * If the install directory or files exist, die()
 */
//if( file_exists(ROSTER_BASE.'install.php') ||  file_exists(ROSTER_BASE.'install') || file_exists(ROSTER_BASE.'upgrade.php') )
//{
//	if( !file_exists(ROSTER_BASE.'version_match.php') )
//	{
//		message_die('Please remove the files <span class="green">install.php</span>, <span class="green">upgrade.php</span> and the folder <span class="green">/install/</span> in this directory','Remove Install Files','sred');
//	}
//}

/**
 * Not needed for blocks
 * Include roster Login class
 */
//require_once ROSTER_LIB.'login.php';

/**
 * Get guild data from dataabse
 */
$guild_info = $wowdb->get_guild_info($roster_conf['server_name'], $roster_conf['guild_name']);

/**
 * Detect and set headers
 */
if (!isset($no_roster_headers) && !headers_sent()) {
    $now = gmdate('D, d M Y H:i:s', time()) . ' GMT';

    @header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

    @header('Last-Modified: ' . $now);

    @header('Cache-Control: no-store, no-cache, must-revalidate');

    @header('Cache-Control: post-check=0, pre-check=0', false);

    @header('Pragma: no-cache');

    @header('Content-type: text/html; ' . $wordings[$roster_conf['roster_lang']]['charset']);
}
