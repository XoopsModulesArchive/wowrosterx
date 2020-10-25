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
 * $Id: quest.php 341 2006-12-10 06:20:26Z zanix $
 *
 ******************************/

if (!defined('ROSTER_INSTALLED')) {
    exit('Detected invalid access to this file!');
}

class quest
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
        echo '<div class="questtype">' . $this->data['quest_zone'] . ' </div>';
    }

    public function out2()
    {
        echo '<b><font face="Georgia" size="+1" color="#0000FF"></font></b>';

        echo '[' . $this->data['quest_level'] . '] ' . $this->data['quest_name'];
    }

    public function out()
    {
        global $roster_conf;

        $max = 60;

        $level = $this->data['quest_level'];

        if (1 == $max) {
            $bgImage = $roster_conf['img_url'] . 'bargrey.gif';
        } else {
            $bgImage = $roster_conf['img_url'] . 'barempty.gif';
        }

        echo '
	<div class="quest">
		<div class="questbox">
			<img class="bg" alt="" src="' . $bgImage . '">';

        if ($max > 1) {
            $width = (int)(($level / $max) * 354);

            echo '<img src="' . $roster_conf['img_url'] . 'barbit.gif" alt="" class="bit" width="' . $width . '">';
        }

        echo '
		<span class="name">' . $this->data['quest_name'] . '</span>';

        if ($max > 1) {
            echo '<span class="level"> [' . $level . ']</span>';
        }

        echo '</div></div>';
    }
}

function quest_get_many($member_id, $search)
{
    global $wowdb;

    $query = 'SELECT * FROM `' . ROSTER_QUESTSTABLE . "` WHERE `member_id` = '$member_id' ORDER BY 'quest_index' ASC";

    $result = $wowdb->query($query);

    $quests = [];

    while ($data = $wowdb->fetch_assoc($result)) {
        $quest = new quest($data);

        $quests[] = $quest;
    }

    return $quests;
}
