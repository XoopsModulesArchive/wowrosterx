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
 * $Id: reputation.php 362 2006-12-19 09:22:54Z pleegwat $
 *
 ******************************/

if (!defined('ROSTER_INSTALLED')) {
    exit('Detected invalid access to this file!');
}

class reputation
{
    public $data;

    public $lastfaction;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function get($field)
    {
        return $this->data[$field];
    }

    public function outHeader($fac)
    {
        if ($this->data['faction'] != $fac) {
            print '<div class="skilltype">' . $this->data['faction'] . '</div>';
        }

        $this->lastfaction = $this->data['faction'];

        return $this->data['faction'];
    }

    public function out()
    {
        global $wordings, $roster_conf, $char;

        $level = $this->data['curr_rep'];

        $max = $this->data['max_rep'];

        if (1 == $max) {
            $bgImage = $roster_conf['img_url'] . 'bargrey.gif';
        } else {
            $bgImage = $roster_conf['img_url'] . 'barempty.gif';
        }

        switch ($this->data['Standing']) {
        case ($wordings[$char->data['clientLocale']]['hated']):
            $RepBarImg = $roster_conf['img_url'] . 'barbit_r.gif';
            $width = (int)((($level + 26000) / 23000) * 354);
            break;
        case ($wordings[$char->data['clientLocale']]['hostile']):
            $RepBarImg = $roster_conf['img_url'] . 'barbit_r.gif';
            $width = (int)((($level + 6000) / 3000) * 354);
            break;
        case ($wordings[$char->data['clientLocale']]['neutral']):
            $RepBarImg = $roster_conf['img_url'] . 'barbit_y.gif';
            break;
        case ($wordings[$char->data['clientLocale']]['unfriendly']):
            $RepBarImg = $roster_conf['img_url'] . 'barbit_o.gif';
            $width = (int)(($level / -3000) * 354);
            break;
        case ($wordings[$char->data['clientLocale']]['honored']):
            $RepBarImg = $roster_conf['img_url'] . 'barbit_g.gif';
            break;
        case ($wordings[$char->data['clientLocale']]['friendly']):
            $RepBarImg = $roster_conf['img_url'] . 'barbit_g.gif';
            break;
        case ($wordings[$char->data['clientLocale']]['exalted']):
            $RepBarImg = $roster_conf['img_url'] . 'barbit_g.gif';
            break;
        case ($wordings[$char->data['clientLocale']]['revered']):
            $RepBarImg = $roster_conf['img_url'] . 'barbit_g.gif';
            break;
        }

        // Giving each rep a unique id so the onmouseover can work correctly

        $id = mb_strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->data['faction'] . $this->data['name']));

        $hover_code = "<div style=\"cursor:default;\" onmouseover=\"swapShow('rep_value_$id','rep_standing_$id');\" onmouseout=\"swapShow('rep_value_$id','rep_standing_$id');\">";

        $value = $hover_code . "<div class=\"value\" style=\"display:none;\" id=\"rep_value_$id\">$level/$max</div>" .
                        "<div class=\"value\" style=\"display:inline;\" id=\"rep_standing_$id\">" . $this->data['Standing'] . '</div></div>';

        // Start output

        $output = '
          <div class="rep">
            <div class="repbox">
              <img class="repbg" alt="" src="' . $bgImage . '">';

        if ($max < 1) {
            $output .= '<img src="' . $RepBarImg . '" alt="" class="repbit" width="' . $width . '">' . "\n";
        }

        if ($max > 1) {
            $width = (int)(($level / $max) * 354);

            $output .= '<img src="' . $RepBarImg . '" alt="" class="repbit" width="' . $width . '">' . "\n";
        }

        $output .= '              <span class="faction">' . $this->data['name'] . '</span>';

        $output .= "\n              $value\n";

        if (1 == $this->data['AtWar']) {
            $output .= '              <span class="war">' . $wordings[$char->data['clientLocale']]['atwar'] . '</span>';
        }  

        //$output .= '              <span class="nowar">'.$wordings[$char->data['clientLocale']]['notatwar'].'</span>';

        $output .= "\n            </div>\n          </div>";

        return $output;
    }
}

function get_reputation($member_id)
{
    global $wowdb;

    if (isset($char)) {
        $char = $wowdb->escape($char);
    }

    if (isset($server)) {
        $server = $wowdb->escape($server);
    }

    $query = 'SELECT * FROM `' . ROSTER_REPUTATIONTABLE . "` WHERE `member_id` = '$member_id' ORDER BY `faction` ASC";

    $result = $wowdb->query($query);

    $reputations = [];

    while ($data = $wowdb->fetch_assoc($result)) {
        $reputation = new reputation($data);

        $reputations[] = $reputation;
    }

    return $reputations;
}
