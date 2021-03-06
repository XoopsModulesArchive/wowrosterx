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
 * $Id: realmstatus.php 394 2006-12-28 10:24:34Z zanix $
 *
 ******************************/

// WOW Server Status
// Version 3.2
// Copyright 2005 Nick Schaffner
// http://53x11.com

// EDITED BY: http://wowroster.net for use in wowroster
// XML parsing by Swipe
// Most other changes by Zanix

// Disable generation of headers from settings.php
$no_roster_headers = true;

require_once  'settings.php';
require_once  ROSTER_LIB . 'xmlparse.php';

//==========[ GET FROM CONF.PHP ]====================================================

if (empty($roster_conf['realmstatus'])) {
    $realmstatus = utf8_decode($roster_conf['server_name']);
} else {
    $realmstatus = utf8_decode($roster_conf['realmstatus']);
}

$realmstatus = trim($realmstatus);

if ($roster_conf['rs_mode']) {
    $generate_image = true;
} elseif (!$roster_conf['rs_mode']) {
    $generate_image = false;
} else {
    $generate_image = true;
}

//==========[ OTHER SETTINGS ]=========================================================

// Minutes between status update refresh 0-60
    $timer = 10;

// Path to image folder
    $image_path = ROSTER_BASE . 'img' . DIR_SEP . 'realmstatus' . DIR_SEP;

// Path to font folder
    $font_path = ROSTER_BASE . 'fonts' . DIR_SEP;

// URL for status page
    $url = $roster_conf['realmstatus_url'];

#--[ MYSQL CONNECT AND STORE ]=========================================================

// Read info from Database
$querystr = 'SELECT * FROM `' . ROSTER_REALMSTATUSTABLE . "` WHERE `server_name` = '" . $wowdb->escape($realmstatus) . "'";
$sql = $wowdb->query($querystr);
if ($sql) {
    $realmData = $wowdb->fetch_array($sql);
} else {
    return;
}

//==========[ STATUS GENERATION CODE ]=================================================

// Check timestamp, update when ready
    $current_time = date('i') * 1;

if ($current_time >= ($realmData['timestamp'] + $timer) || $current_time < $realmData['timestamp']) {
    // Get and format HTML data

    // Check for xml/html

    if ('.xml' == mb_substr($url, -4)) {
        $xml = 1;
    } else {
        if (function_exists('curl_init')) {
            $fp = curl_init($url);

            curl_setopt($fp, CURLOPT_HEADER, 0);

            curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1);

            $html = curl_exec($fp);

            if (curl_errno($fp)) {
                $ch_err = 1;
            } else {
                $ch_err = 0;
            }

            curl_close($fp);
        } else {
            $html = @file_get_contents($url);
        }
    }

    //==========[ HTML PARSER ]============================================================

    if (isset($html) && $html) {
        if (!preg_match('/\<tr(.(?!\<tr))*(' . str_replace("'", '&#039;', $realmstatus) . ')(.(?!\<\/tr))*/si', $html, $matches)) {
            $err = 1;
        } elseif (3 != preg_match_all('/^.*color: #([0-9a-fA-F]+);\">([^<]*)/m', $matches[0], $row)) {
            $err = 1;
        }

        //print_r($row);

        //die();

        // Figure out Serverstatus

        $realmData['serverstatus'] = mb_strpos($matches[0], 'uparrow');

        if (!$realmData['serverstatus']) {
            $realmData['serverstatus'] = 'Down';

            $realmData['serverpop'] = 'Offline';
        } else {
            $realmData['serverstatus'] = 'Up';
        }

        // Figure out Servertype

        $realmData['servertype'] = html_entity_decode(str_replace('&nbsp;', ' ', $row[2][1]));

        $realmData['servertypecolor'] = $row[1][1];

        // Figure out Server Pop.

        $realmData['serverpop'] = html_entity_decode(str_replace('&nbsp;', ' ', $row[2][2]));

        $realmData['serverpopcolor'] = $row[1][2];
    } elseif ($xml) {
        //==========[ XML PARSER ]=============================================================

        $xml_parse = new xmlParser();

        $err = 1;

        if ($xml_parse->parse($url)) {
            if (count($xml_parse->output)) {
                if (is_array($xml_parse->output[0]['child'])) {
                    foreach ($xml_parse->output[0]['child'] as $xml_array) {
                        foreach ($xml_array as $xml_server) {
                            if ($xml_server['N'] == $realmstatus) {
                                $err = 0;

                                switch ($xml_server['S']) {
                                    case 0:
                                        $realmData['serverstatus'] = 'Down';
                                        break;
                                    case 1:
                                        $realmData['serverstatus'] = 'Up';
                                        break;
                                    case 2:
                                        $realmData['serverstatus'] = 'Maintenance';
                                        break;
                                    default:
                                        $realmData['serverstatus'] = 'Unknown';
                                }

                                switch ($xml_server['T']) {
                                    case 0:
                                        $realmData['servertype'] = 'RP-PvP';
                                        $realmData['servertypecolor'] = '535600';
                                        break;
                                    case 1:
                                        $realmData['servertype'] = 'Normal';
                                        $realmData['servertypecolor'] = '234303';
                                        break;
                                    case 2:
                                        $realmData['servertype'] = 'PvP';
                                        $realmData['servertypecolor'] = '660D02';
                                        break;
                                    case 3:
                                        $realmData['servertype'] = 'RP';
                                        $realmData['servertypecolor'] = '535600';
                                        break;
                                    default:
                                        $realmData['servertype'] = 'Unknown';
                                        $realmData['servertypecolor'] = '860D02';
                                }

                                switch ($xml_server['L']) {
                                    case 1:
                                        $realmData['serverpop'] = 'Low';
                                        $realmData['serverpopcolor'] = '234303';
                                        break;
                                    case 2:
                                        $realmData['serverpop'] = 'Medium';
                                        $realmData['serverpopcolor'] = '535600';
                                        break;
                                    case 3:
                                        $realmData['serverpop'] = 'High';
                                        $realmData['serverpopcolor'] = '660D02';
                                        break;
                                    case 4:
                                        $realmData['serverpop'] = 'Max';
                                        $realmData['serverpopcolor'] = '860D02';
                                        break;
                                    default:
                                        $realmData['serverpop'] = 'Error';
                                        $realmData['serverpopcolor'] = '860D02';
                                }
                            }
                        }
                    }
                }
            }
        }
    } else {
        $err = 1;
    }

    //==========[ WRITE INFO TO DATABASE ]=================================================

    if (!$err) { // Don't write to DB if there has been an error
        $wowdb->reset_values();

        $wowdb->add_value('server_name', $realmstatus);

        $wowdb->add_value('servertype', $realmData['servertype']);

        $wowdb->add_value('servertypecolor', $realmData['servertypecolor']);

        $wowdb->add_value('serverstatus', $realmData['serverstatus']);

        $wowdb->add_value('serverpop', $realmData['serverpop']);

        $wowdb->add_value('serverpopcolor', $realmData['serverpopcolor']);

        $wowdb->add_value('timestamp', $current_time);

        if ($realmstatus == $realmData['server_name']) {
            $querystr = 'UPDATE `' . ROSTER_REALMSTATUSTABLE . '` SET ' . $wowdb->assignstr . " WHERE `server_name` = '" . $wowdb->escape($realmstatus) . "';";
        } else {
            $querystr = 'TRUNCATE `' . ROSTER_REALMSTATUSTABLE . '`;';

            $wowdb->query($querystr);

            $querystr = 'INSERT INTO `' . ROSTER_REALMSTATUSTABLE . '` SET ' . $wowdb->assignstr . ';';
        }

        // Give only debug info with text-status enabled

        // Otherwise the debug-statement will destroy the png-generation

        if ($roster_conf['sqldebug'] && !$generate_image) {
            print "<!-- $querystr -->\n";
        }

        $wowdb->query($querystr);
    }
}

//==========[ "NOW, WHAT TO DO NEXT?" ]================================================

// Error control
if ('Down' == $realmData['serverstatus'] || 'Maitenence' == $realmData['serverstatus']) {
    $realmData['serverstatus'] = 'Down';

    $realmData['serverpop'] = 'Offline';

    $realmData['serverpopcolor'] = '660D02';
}

// Check to see if data from the DB is non-existant
if (empty($realmData['serverstatus']) || empty($realmData['serverpop'])) {
    $err = 1;
} else {
    $err = 0;
}

// Set to Unknown values upon error
if ($err) {
    $realmData['serverstatus'] = 'Unknown';

    $realmData['serverpop'] = 'No Status';

    $realmData['serverpopcolor'] = '660D02';
}

// Generate image or text?
if ($generate_image) {
    img_output($realmData, $err, $image_path, $font_path);
} else {
    echo text_output($realmData);
}

return;
//==========[ TEXT OUTPUT MODE ]=======================================================
function text_output($realmData)
{
    global $roster_conf;

    $outtext = '
<!-- Begin Realmstatus -->
<div style="width:88px;font-family:arial;font-weight:bold;">
	<div style="width:88px;height:41px;background-image:url(' . $roster_conf['img_url'] . 'realmstatus/' . mb_strtolower($realmData['serverstatus']) . '.png);"></div>';

    if ('full' == $roster_conf['rs_display']) {
        $outtext .= '
	<div style="vertical-align:middle;text-align:center;width:88px;height:54px;background-image:url(' . $roster_conf['img_url'] . 'realmstatus/' . mb_strtolower($realmData['serverstatus']) . '2.png);">
		<div style="padding-top:7px;color:black;font-size:10px;">' . $realmData['server_name'] . '</div>
		<div style="color:#' . $realmData['serverpopcolor'] . ';font-size:12px;">' . $realmData['serverpop'] . '</div>
		<div style="color:#' . $realmData['servertypecolor'] . ';font-size:9px;">' . $realmData['servertype'] . '</div>
	</div>';
    }

    $outtext .= '
</div>
<!-- End Realmstatus -->
';

    return $outtext;
}

//==========[ IMAGE GENERATOR ]========================================================

function img_output($realmData, $err, $image_path, $font_path)
{
    global $roster_conf;

    $serverfont = $font_path . 'VERANDA.TTF';

    $serverfontsize = 7;

    $typefont = $font_path . 'silkscreenb.ttf';

    $typefontsize = 6;

    $serverpopfont = $font_path . 'GREY.TTF';

    $serverpopfontsize = 11;

    // Get and combine base images, set colors

    $top = @imagecreatefrompng($image_path . mb_strtolower($realmData['serverstatus']) . '.png');

    if (!$top) {
        return;
    }

    if ('full' == $roster_conf['rs_display']) {
        // Get with of the top image

        $topwidth = imagesx($top);

        // Make the bottom part

        $bottom = imagecreatefrompng($image_path . mb_strtolower(($err ? 'down' : $realmData['serverstatus'])) . '2.png');

        // Create a new image

        $full = imagecreatetruecolor($topwidth, (imagesy($top) + imagesy($bottom)));

        // Turn off apha so we can get a clean boder to set to transparent

        imagealphablending($full, false);

        // Set the trans color

        $col = getColor('22ff22', $full);

        imagefilledrectangle($full, 0, 0, $topwidth, (imagesy($top) + imagesy($bottom)), $col);

        imagecopy($full, $top, 0, 0, 0, 0, $topwidth, imagesy($top));

        imagecopy($full, $bottom, 0, imagesy($top), 0, 0, imagesx($bottom), imagesy($bottom));

        imagealphablending($full, true);

        $popcolor = getColor($realmData['serverpopcolor'], $full);

        $typecolor = getColor($realmData['servertypecolor'], $full);

        $textcolor = getColor('000000', $full);

        $shadow = getColor('95824e', $full);

        //imagecopy($full,$top,0,0,0,0,$topwidth,imagesy($top));

        //imagecopy($full,$bottom,0,imagesy($top),0,0,imagesx($bottom),imagesy($bottom));

        $top = $full;

        // Ouput centered $server name

        $maxw = 62;

        $output = '';

        $box = imagettfbbox($serverfontsize, 0, $serverfont, $realmData['server_name']);

        $w = abs($box[0]) + abs($box[2]);

        if ($w > $maxw) {
            $i = $w;

            $t = mb_strlen($realmData['server_name']);

            while ($i > $maxw) {
                $t--;

                $box = imagettfbbox($serverfontsize, 0, $serverfont, mb_substr($realmData['server_name'], 0, $t));

                $i = abs($box[0]) + abs($box[2]);
            }

            $t = mb_strrpos(mb_substr($realmData['server_name'], 0, $t), ' ');

            $output[0] = mb_substr($realmData['server_name'], 0, $t);

            $output[1] = ltrim(mb_substr($realmData['server_name'], $t));

            $vadj = -6;
        } else {
            $output[0] = $realmData['server_name'];
        }

        $i = 0;

        foreach ($output as $value) {
            $box = imagettfbbox($serverfontsize, 0, $serverfont, $value);

            $w = abs($box[0]) + abs($box[2]);

            writeText($top, $serverfontsize, round(($topwidth - $w) / 2), 54 + ($i * 8) + $vadj, $textcolor, $serverfont, $value, $shadow);

            $i++;
        }

        unset($output);

        $vadj = 0;

        // Ouput centered $realmData['serverpop']

        if ($realmData['serverpop']) {
            $box = imagettfbbox($serverpopfontsize, 0, $serverpopfont, $realmData['serverpop']);

            $w = abs($box[0]) + abs($box[2]);

            if ($w > $maxw) {
                $i = $w;

                $t = mb_strlen($realmData['serverpop']);

                while ($i > $maxw) {
                    $t--;

                    $box = imagettfbbox($serverpopfontsize, 0, $serverpopfont, mb_substr($realmData['serverpop'], 0, $t));

                    $i = abs($box[0]) + abs($box[2]);
                }

                $t = mb_strrpos(mb_substr($realmData['serverpop'], 0, $t), ' ');

                $output[0] = mb_substr($realmData['serverpop'], 0, $t);

                $output[1] = ltrim(mb_substr($realmData['serverpop'], $t));

                $vadj = -4;
            } else {
                $output[0] = $realmData['serverpop'];
            }

            $i = 0;

            foreach ($output as $value) {
                $box = imagettfbbox($serverpopfontsize, 0, $serverpopfont, $value);

                $w = abs($box[0]) + abs($box[2]);

                writeText($top, $serverpopfontsize, round(($topwidth - $w) / 2), 73 + ($i * 10) + $vadj, $popcolor, $serverpopfont, $value, $shadow);

                $i++;
            }
        }

        // Ouput centered $realmData['servertype']

        if ($realmData['servertype'] && !$err) {
            $box = imagettfbbox($typefontsize, 0, $typefont, $realmData['servertype']);

            $w = abs($box[0]) + abs($box[2]);

            writeText($top, $typefontsize, round(($topwidth - $w) / 2), 88, $typecolor, $typefont, $realmData['servertype'], $shadow);
        }
    }

    header('Content-type: image/png');

    imagealphablending($top, false);

    imagesavealpha($top, true);

    imagepng($top);

    imagedestroy($top);
}

// Function to set color of text
function getColor($color, $image)
{
    $red = 100;

    $green = 100;

    $blue = 100;

    $ret = '';

    if (eregi('[#]?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})', $color, $ret)) {
        $red = hexdec($ret[1]);

        $green = hexdec($ret[2]);

        $blue = hexdec($ret[3]);
    }

    return imagecolorallocate($image, $red, $green, $blue);
}

// Write Text
function writeText($im, $fontsize, $xpos, $ypos, $color, $font, $text, $shadow_color)
{
    // Create the pseudo-shadow

    if (!empty($shadow_color)) {
        shadowText($im, $fontsize, $xpos, $ypos, $font, $text, $shadow_color);
    }

    // Write the text

    @imagettftext($im, $fontsize, 0, $xpos, $ypos, $color, $font, $text);
}

// Shadow Text
function shadowText($im, $fontsize, $xpos, $ypos, $font, $text, $color)
{
    $_x = [ 0, 1, 1 ];

    $_y = [ 1, 0, 1 ];

    for ($n = 0; $n <= 2; $n++) {
        @imagettftext($im, $fontsize, 0, $xpos + $_x[$n], $ypos + $_y[$n], $color, $font, $text);
    }
}
