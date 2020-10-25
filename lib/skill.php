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
 * $Id: skill.php 341 2006-12-10 06:20:26Z zanix $
 *
 ******************************/

if (!defined('ROSTER_INSTALLED')) {
    exit('Detected invalid access to this file!');
}

class skill
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function get($field)
    {
        return $this->data[$field];
    }

    public function outHeader()
    {
        return '<div class="skilltype">' . $this->data['skill_type'] . ' </div>';
    }

    public function out()
    {
        global $roster_conf;

        [$level, $max] = explode(':', $this->data['skill_level']);

        if (1 == $max) {
            $bgImage = $roster_conf['img_url'] . 'bargrey.gif';
        } else {
            $bgImage = $roster_conf['img_url'] . 'barempty.gif';
        }

        $returnstring = '
<div class="skill">
  <div class="skillbox">
    <img class="bg" alt="" src="' . $bgImage . '">';

        if ($max > 1) {
            $width = (int)(($level / $max) * 354);

            $returnstring .= '<img src="' . $roster_conf['img_url'] . 'barbit.gif" alt="" class="bit" width="' . $width . '">';
        }

        $returnstring .= '
    <span class="name">' . $this->data['skill_name'] . '</span>';

        if ($max > 1) {
            $returnstring .= '<span class="level">' . $level . '/' . $max . '</span>';
        }

        $returnstring .= '
  </div>
</div>
';

#      echo '<img class="bgGrey" src="'.$roster_conf['img_url'].'bargrey.gif">';
#    } else {
#      echo '<img class="bgGrey" src="'.$roster_conf['img_url'].'bargrey.gif">';
#    }
#    echo '<div class="skill">'.$this->data['skill_name'].": ".$this->data['skill_level']."</div>\n";

        return $returnstring;
    }
}

function skill_get_many_by_type($member_id, $type)
{
    global $wowdb;

    $type = $wowdb->escape($type);

    return skill_get_many($member_id, "`skill_type` = '$type'");
}

function skill_get_many_by_order($member_id, $order)
{
    global $wowdb;

    $order = $wowdb->escape($order);

    return skill_get_many($member_id, "`skill_order` = '$order'");
}

function skill_get_many($member_id, $search)
{
    global $wowdb;

    if (isset($char)) {
        $char = $wowdb->escape($char);
    }

    if (isset($server)) {
        $server = $wowdb->escape($server);
    }

    $query = 'SELECT * FROM `' . ROSTER_SKILLSTABLE . "` WHERE `member_id` = '$member_id' AND $search";

    $result = $wowdb->query($query);

    $skills = [];

    while ($data = $wowdb->fetch_assoc($result)) {
        $skill = new skill($data);

        $skills[] = $skill;
    }

    return $skills;
}
