<?php
global $xoopsDB, $xoopsUser, $xoopsModule, $xoopsModuleConfig;
$content = '';
$module_name = 'wowrosterx';
if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $module_name && $xoopsModule->getVar('isactive'))) {
    $content .= 'print <img src="motd.php" alt="' . $guildMOTD . '"><br><br>';

    $content .= '<hr>';
} else {
    require_once('settings.php');

    $content .= 'print <img src="motd.php" alt="' . $guildMOTD . '"><br><br>';

    $content .= '<hr>';
}
