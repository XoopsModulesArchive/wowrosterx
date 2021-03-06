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
 * $Id: char.php 394 2006-12-28 10:24:34Z zanix $
 *
 ******************************/

if (!defined('ROSTER_INSTALLED')) {
    exit('Detected invalid access to this file!');
}

require_once(ROSTER_LIB . 'item.php');
require_once(ROSTER_LIB . 'bag.php');
require_once(ROSTER_LIB . 'skill.php');
require_once(ROSTER_LIB . 'reputation.php');
require_once(ROSTER_LIB . 'quest.php');
require_once(ROSTER_LIB . 'recipes.php');
require_once(ROSTER_LIB . 'pvp3.php');

$myBonus = [];
$myTooltip = [];

class char
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function printXP()
    {
        [$current, $max] = explode(':', $this->data['exp']);

        $perc = 0;

        if ($max > 0) {
            $perc = round(($current / $max) * 248, 0);
        }

        return $perc;
    }

    public function show_pvp2($type, $url, $sort, $start)
    {
        $pvps = pvp_get_many3($this->data['member_id'], $type, $sort, -1);

        $returnstring .= '<div align="center">';

        if (is_array($pvps)) {
            $returnstring .= output_pvp_summary($pvps, $type);

            if (isset($pvps[0])) {
                switch ($type) {
                    case 'BG':
                        $returnstring .= output_bglog($this->data['member_id']);
                        break;
                    case 'PvP':
                        $returnstring .= output_pvplog($this->data['member_id']);
                        break;
                    case 'Duel':
                        $returnstring .= output_duellog($this->data['member_id']);
                        break;
                    default:
                        break;
                }
            }

            $returnstring .= '<br>';

            $returnstring .= '<br>';

            $max = count($pvps);

            $sort_part = $sort ? "&amp;s=$sort" : '';

            if ($start > 0) {
                $prev = $url . '&amp;start=0' . $sort_part . '">&lt;&lt;</a> ' . $url . '&amp;start=' . ($start - 50) . $sort_part . '">&lt;</a> ';
            }

            if (($start + 50) < $max) {
                $listing = '<small>[' . $start . ' - ' . ($start + 50) . '] of ' . $max . '</small>';

                $next = ' ' . $url . '&amp;start=' . ($start + 50) . $sort_part . '">&gt;</a>' . $url . '&amp;start=' . ($max - 50) . $sort_part . '">&gt;&gt;</a>';
            } else {
                $listing = '<small>[' . $start . ' - ' . ($max) . '] of ' . $max . '</small>';
            }

            $pvps = pvp_get_many3($this->data['member_id'], $type, $sort, $start);

            if (isset($pvps[0])) {
                $returnstring .= border('sgray', 'start', $prev . 'Log ' . $listing . $next);

                $returnstring .= output_pvp2($pvps, $url . '&amp;start=' . $start, $type);

                $returnstring .= border('sgray', 'end');
            }

            $returnstring .= '<br>';

            if ($start > 0) {
                $returnstring .= $prev;
            }

            if (($start + 50) < $max) {
                $returnstring .= '[' . $start . ' - ' . ($start + 50) . '] of ' . $max;

                $returnstring .= $next;
            } else {
                $returnstring .= '[' . $start . ' - ' . ($max) . '] of ' . $max;
            }

            $returnstring .= '</div><br>';

            return $returnstring;
        }
  

        return '';
    }

    public function show_quests()
    {
        global $wordings, $roster_conf, $questlinks;

        $lang = $this->data['clientLocale'];

        $quests = quest_get_many($this->data['member_id'], '');

        if (isset($quests[0])) {
            $zone = '';

            $returnstring .= border('sgray', 'start', $wordings[$lang]['questlog'] . ' (' . count($quests) . '/25)') .
                '<table class="bodyline" cellspacing="0" cellpadding="0">';

            foreach ($quests as $quest) {
                if ($zone != $quest->data['zone']) {
                    $zone = $quest->data['zone'];

                    $returnstring .= '<tr><th colspan="10" class="membersHeaderRight">' . $zone . '</th></tr>';
                }

                $quest_level = $quest->data['quest_level'];

                $char_level = $this->data['level'];

                $font = 'grey';

                if ($quest_level + 9 < $char_level) {
                    $font = 'grey';
                } elseif ($quest_level + 2 < $char_level) {
                    $font = 'green';
                } elseif ($quest_level < $char_level + 3) {
                    $font = 'yellow';
                } else {
                    $font = 'red';
                }

                $name = $quest->data['quest_name'];

                if ('[' == $name[0]) {
                    $name = trim(mb_strstr($name, ' '));
                }

                $returnstring .= '        <tr>
          <td class="membersRow1">';

                $returnstring .= '<span class="' . $font . '">[' . $quest_level . '] ' . $name . '</span>';

                $quest_tags = '';

                if ($quest->data['quest_tag']) {
                    $quest_tags[] = $quest->data['quest_tag'];
                }

                if (1 == $quest->data['is_complete']) {
                    $quest_tags[] = 'Complete';
                } elseif (-1 == $quest->data['is_complete']) {
                    $quest_tags[] = 'Failed';
                }

                if (is_array($quest_tags)) {
                    foreach ($quest_tags as $quest_tag) {
                        $returnstring .= ' (' . $quest_tag . ')';
                    }
                }

                $returnstring .= "</td>\n";

                $returnstring .= '<td class="membersRowRight1 quest_link">';

                $q = 1;

                foreach ($questlinks as $link) {
                    if ($roster_conf['questlink_' . $q]) {
                        $returnstring .= '<a href="' . $link[$lang]['url1'] . urlencode(utf8_decode($name)) . ($link[$lang]['url2'] ? $link[$lang]['url2'] . $quest_level : '') . ($link[$lang]['url3'] ? $link[$lang]['url3'] . $quest_level : '') . '" target="_blank">' . $link[$lang]['name'] . "</a>\n";
                    }

                    $q++;
                }

                $returnstring .= '</td></tr>';
            }

            $returnstring .= '      </table>' . border('sgray', 'end');
        }

        return $returnstring;
    }

    public function show_recipes()
    {
        global $roster_conf, $url, $sort, $wordings, $wowdb;

        $lang = $this->data['clientLocale'];

        $recipes = recipe_get_many($this->data['member_id'], '', $sort);

        if (isset($recipes[0])) {
            $skill_name = '';

            $returnstring = '<span class="headline_1">' . $wordings[$lang]['recipelist'] . '</span>' . "\n";

            // Get char professions for quick links

            $query = 'SELECT `skill_name` FROM `' . ROSTER_RECIPESTABLE . "` WHERE `member_id` = '" . $this->data['member_id'] . "' GROUP BY `skill_name` ORDER BY `skill_name`";

            $result = $wowdb->query($query);

            // Set a ank for link to top of page

            $returnstring .= "<a name=\"top\">&nbsp;</a>\n";

            $returnstring .= '<div align="center">';

            $skill_name_divider = '';

            while ($data = $wowdb->fetch_assoc($result)) {
                $skill_name_header = $data['skill_name'];

                $returnstring .= $skill_name_divider . '<a href="#' . mb_strtolower(str_replace(' ', '', $skill_name_header)) . '">' . $skill_name_header . '</a>';

                $skill_name_divider = '&nbsp;-&nbsp;';
            }

            $returnstring .= "</div>\n<br>\n";

            $rc = 0;

            $first_run = 1;

            foreach ($recipes as $recipe) {
                if ($skill_name != $recipe->data['skill_name']) {
                    $skill_name = $recipe->data['skill_name'];

                    if (!$first_run) {
                        $returnstring .= '</table>' . border('sgray', 'end') . "<br>\n";
                    }

                    $first_run = 0;

                    // Set an link to the top behind the profession image

                    $skill_image = 'Interface/Icons/' . $wordings[$this->data['clientLocale']]['ts_iconArray'][$skill_name];

                    $skill_image = '<div style="display:inline;float:left;"><img width="17" height="17" src="' . $roster_conf['interface_url'] . $skill_image . '.' . $roster_conf['img_suffix'] . "\" alt=\"\"></div>\n";

                    $header = '<div style="cursor:pointer;width:600px;" onclick="showHide(\'table_' . $rc . '\',\'img_' . $rc . '\',\'' . $roster_conf['img_url'] . 'minus.gif\',\'' . $roster_conf['img_url'] . 'plus.gif\');">
	' . $skill_image . '
	<div style="display:inline;float:right;"><img id="img_' . $rc . '" src="' . $roster_conf['img_url'] . 'plus.gif" alt=""></div>
<a name="' . mb_strtolower(str_replace(' ', '', $skill_name)) . '"></a>' . $skill_name . '</div>';

                    $returnstring .= border('sgray', 'start', $header) . "\n<table width=\"100%\" style=\"display:none;\" class=\"bodyline\" cellspacing=\"0\" id=\"table_$rc\">\n";

                    $returnstring .= '  <tr>
    <th class="membersHeader">' . $url . '&amp;action=recipes&amp;s=item">' . $wordings[$lang]['item'] . '</a></th>
    <th class="membersHeader">' . $url . '&amp;action=recipes&amp;s=name">' . $wordings[$lang]['name'] . '</a></th>
    <th class="membersHeader">' . $url . '&amp;action=recipes&amp;s=difficulty">' . $wordings[$lang]['difficulty'] . '</a></th>
    <th class="membersHeader">' . $url . '&amp;action=recipes&amp;s=type">' . $wordings[$lang]['type'] . '</a></th>
    <th class="membersHeader">' . $url . '&amp;action=recipes&amp;s=level">' . $wordings[$lang]['level'] . '</a></th>
    <th class="membersHeaderRight">' . $url . '&amp;action=recipes&amp;s=reagents">' . $wordings[$lang]['reagents'] . '</a></th>
  </tr>
';
                }

                if ('4' == $recipe->data['difficulty']) {
                    $difficultycolor = 'FF9900';
                } elseif ('3' == $recipe->data['difficulty']) {
                    $difficultycolor = 'FFFF66';
                } elseif ('2' == $recipe->data['difficulty']) {
                    $difficultycolor = '339900';
                } elseif ('1' == $recipe->data['difficulty']) {
                    $difficultycolor = 'CCCCCC';
                } else {
                    $difficultycolor = 'FFFF80';
                }

                // Dont' set an CSS class for the image cell - center it

                $stripe = (($rc % 2) + 1);

                $returnstring .= '  <tr>
    <td class="membersRow' . $stripe . '">';

                $returnstring .= $recipe->out();

                $returnstring .= '</td>
    <td class="membersRow' . $stripe . '"><span style="color:#' . mb_substr($recipe->data['item_color'], 2, 6) . '">&nbsp;' . $recipe->data['recipe_name'] . '</span></td>
    <td class="membersRow' . $stripe . '"><span style="color:#' . $difficultycolor . '">&nbsp;' . $wordings[$lang]['recipe_' . $recipe->data['difficulty']] . '</span></td>
    <td class="membersRow' . $stripe . '">&nbsp;' . $recipe->data['recipe_type'] . '&nbsp;</td>
    <td class="membersRow' . $stripe . '">&nbsp;' . $recipe->data['level'] . '&nbsp;</td>
    <td class="membersRowRight' . $stripe . '">&nbsp;' . str_replace('<br>', '&nbsp;<br>&nbsp;', $recipe->data['reagents']) . '</td>
  </tr>
';

                $rc++;
            }

            $returnstring .= '</table>' . border('sgray', 'end');
        }

        return $returnstring;
    }

    public function show_mailbox()
    {
        global $wowdb, $wordings, $roster_conf, $phptimeformat, $itemlink;

        $sqlquery = 'SELECT * FROM `' . ROSTER_MAILBOXTABLE . '` ' .
            "WHERE `member_id` = '" . $this->data['member_id'] . "' " .
            'ORDER BY `mailbox_days`;';

        if ($wowdb->sqldebug) {
            $content .= "<!-- $sqlquery -->\n";
        }

        $result = $wowdb->query($sqlquery);

        if (!$result) {
            return 'No ' . $wordings[$roster_conf['roster_lang']]['mailbox'] . ' for ' . $this->data['name'];
        }

        if ($wowdb->num_rows($result) > 0) {
            //begin generation of mailbox's output

            $content .= border('sgray', 'start', $wordings[$roster_conf['roster_lang']]['mailbox']) .
                '<table cellpadding="0" cellspacing="0" class="bodyline">' . "\n";

            $content .= "<tr>\n";

            $content .= '<th class="membersHeader">' . $wordings[$roster_conf['roster_lang']]['mail_item'] . '</th>' . "\n";

            $content .= '<th class="membersHeader">' . $wordings[$roster_conf['roster_lang']]['mail_sender'] . '</th>' . "\n";

            $content .= '<th class="membersHeader">' . $wordings[$roster_conf['roster_lang']]['mail_subject'] . '</th>' . "\n";

            $content .= '<th class="membersHeaderRight">' . $wordings[$roster_conf['roster_lang']]['mail_expires'] . '</th>' . "\n";

            $content .= "</tr>\n";

            $content .= "<tr>\n";

            $cur_row = 1;

            while ($row = $wowdb->fetch_assoc($result)) {
                $maildateutc = strtotime($this->data['maildateutc']);

                $content .= '<td class="membersRow' . $cur_row . '">' . "\n";

                $cur_row = (($cur_row + 1) % 1) + 1;

                // Get money in mail

                $money_included = '';

                if ($row['mailbox_coin'] > 0 && $roster_conf['show_money']) {
                    $db_money = $row['mailbox_coin'];

                    $mail_money['c'] = mb_substr($db_money, -2, 2);

                    $db_money = mb_substr($db_money, 0, -2);

                    $money_included = $mail_money['c'] . '<img src="' . $roster_conf['img_url'] . 'bagcoinbronze.gif" alt="c">';

                    if (!empty($db_money)) {
                        $mail_money['s'] = mb_substr($db_money, -2, 2);

                        $db_money = mb_substr($db_money, 0, -2);

                        $money_included = $mail_money['s'] . '<img src="' . $roster_conf['img_url'] . 'bagcoinsilver.gif" alt="s"> ' . $money_included;
                    }

                    if (!empty($db_money)) {
                        $mail_money['g'] = $db_money;

                        $money_included = $mail_money['g'] . '<img src="' . $roster_conf['img_url'] . 'bagcoingold.gif" alt="g"> ' . $money_included;
                    }
                }

                // Fix icon texture

                if (!empty($row['item_icon'])) {
                    $item_icon = $roster_conf['interface_url'] . $row['item_icon'] . '.' . $roster_conf['img_suffix'];
                } elseif (!empty($money_included)) {
                    $item_icon = $roster_conf['interface_url'] . $row['mailbox_coin_icon'] . '.' . $roster_conf['img_suffix'];
                } else {
                    $item_icon = $roster_conf['interface_url'] . 'Interface/Icons/INV_Misc_Note_02.' . $roster_conf['img_suffix'];
                }

                // Start the tooltips

                $tooltip_h = $row['mailbox_subject'];

                // first line is sender

                $tooltip = $wordings[$this->data['clientLocale']]['mail_sender'] .
                    ': ' . $row['mailbox_sender'] . '<br>';

                $expires_line = date($phptimeformat[$this->data['clientLocale']], ((($row['mailbox_days'] * 24 + $roster_conf['localtimeoffset']) * 3600) + $maildateutc)) . ' ' . $roster_conf['timezone'];

                if ((($row['mailbox_days'] * 24 * 3600) + $maildateutc) - time() < (3 * 24 * 3600)) {
                    $color = 'ff0000;';
                } else {
                    $color = 'ffffff;';
                }

                $tooltip .= $wordings[$this->data['clientLocale']]['mail_expires'] . ": <span style=\"color:#$color\">$expires_line</span><br>";

                // Join money with main tooltip

                if (!empty($money_included)) {
                    $tooltip .= $wordings[$this->data['clientLocale']]['mail_money'] . ': ' . $money_included;
                }

                // Get item tooltip

                $item_tooltip = colorTooltip($row['item_tooltip'], $row['item_color'], $this->data['clientLocale']);

                // If the tip has no info, at least get the item name in there

                if ('<br>' != $item_tooltip) {
                    $item_tooltip = '<hr>' . $item_tooltip;
                }

                // Join item tooltip with main tooltip

                $tooltip .= $item_tooltip;

                if ('' == $tooltip) {
                    if ('' != $row['item_name']) {
                        $tooltip = $row['item_name'];
                    } else {
                        $tooltip = 'No information';
                    }
                }

                $tooltip = makeOverlib($tooltip, $tooltip_h, '', 2, $this->data['clientLocale']);

                $content .= '<div class="item" ' . $tooltip . '>';

                $content .= '<a href="' . $itemlink[$this->data['clientLocale']] . urlencode(utf8_decode($row['item_name'])) . '" target="_blank">' . "\n" .
                    '<img src="' . $item_icon . '"' . " alt=\"\"></a>\n";

                if (($row['item_quantity'] > 1)) {
                    $content .= '<span class="quant">' . $row['item_quantity'] . '</span>';
                }

                $content .= "</div>\n</td>\n";

                $content .= '<td class="membersRow' . $cur_row . '">' . $row['mailbox_sender'] . '</td>' . "\n";

                $content .= '<td class="membersRow' . $cur_row . '">' . $row['mailbox_subject'] . '</td>' . "\n";

                $content .= '<td class="membersRowRight' . $cur_row . '">' . $expires_line . '</td>' . "\n";

                $content .= "</tr>\n";
            }

            $content .= "</tr>\n</table>\n" . border('sgray', 'end');

            return $content;
        }
  

        return 'No ' . $wordings[$roster_conf['roster_lang']]['mailbox'] . ' for ' . $this->data['name'];
    }

    public function show_spellbook()
    {
        global $wowdb, $wordings, $roster_conf;

        $sqlquery = 'SELECT `spelltree`.*, `talenttree`.`order` ' .
            'FROM `' . ROSTER_SPELLTREETABLE . '` AS spelltree ' .
            'LEFT JOIN `' . ROSTER_TALENTTREETABLE . '` AS talenttree ON `spelltree`.`member_id` = `talenttree`.`member_id` AND `spelltree`.`spell_type` = `talenttree`.`tree` ' .
            'WHERE `spelltree` . `member_id` = ' . $this->data['member_id'] . ' ' .
            'ORDER BY `talenttree` . `order` ASC';

        $result = $wowdb->query($sqlquery);

        if (!$result) {
            return 'No ' . $wordings[$roster_conf['roster_lang']]['spellbook'] . ' for ' . $this->data['name'];
        }

        $num_trees = $wowdb->num_rows($result);

        if ($num_trees > 0) {
            for ($t = 0; $t < $num_trees; $t++) {
                $treedata = $wowdb->fetch_assoc($result);

                $tree[$t]['name'] = $treedata['spell_type'];

                $tree[$t]['icon'] = $treedata['spell_texture'];

                $tree[$t]['id'] = $t;

                // Get Icons

                $sqlquery = 'SELECT * FROM `' . ROSTER_SPELLTABLE . "` WHERE `member_id` = '" . $this->data['member_id'] . "' AND `spell_type` = '" . $wowdb->escape($tree[$t]['name']) . "' ORDER BY `spell_name`;";

                $icons_result = $wowdb->query($sqlquery);

                if ($wowdb->num_rows($icons_result) > 0) {
                    $i = 0;

                    $p = 0;

                    for ($r = 0; $r < $wowdb->num_rows($icons_result); $r++) {
                        $icons_data = $wowdb->fetch_assoc($icons_result);

                        if ($i >= 14) {
                            $i = 0;

                            $p++;
                        }

                        $spells[$p][$i]['name'] = $icons_data['spell_name'];

                        $spells[$p][$i]['type'] = $icons_data['spell_type'];

                        $spells[$p][$i]['icon'] = $icons_data['spell_texture'];

                        $spells[$p][$i]['rank'] = $icons_data['spell_rank'];

                        // Parse the tooltip

                        $spells[$p][$i]['tooltip'] = makeOverlib($icons_data['spell_tooltip'], '', '', 0, $this->data['clientLocale'], ',RIGHT');

                        $i++;
                    }

                    // Assign spells to tree

                    $tree[$t]['spells'] = $spells;

                    unset($spells);
                }
            }

            $spelltree = $tree;
        } else {
            return 'No ' . $wordings[$roster_conf['roster_lang']]['spellbook'] . ' for ' . $this->data['name'];
        }

        $return_string .= '
<div class="spell_panel">
	<div class="spell_panel_name">' . $wordings[$roster_conf['roster_lang']]['spellbook'] . '</div>

	<!-- Skill Type Icons Menu -->
	<div class="spell_skill_tab_bar">
';

        foreach ($spelltree as $tree) {
            $treetip = makeOverlib($tree['name'], '', '', 2, '', ',WRAP,RIGHT');

            $return_string .= '
		<div class="spell_skill_tab">
			<img class="spell_skill_tab_icon" src="' . $roster_conf['interface_url'] . $tree['icon'] . '.' . $roster_conf['img_suffix'] . '" ' . $treetip . ' alt="" onclick="showSpellTree(\'spelltree_' . $tree['id'] . '\');">
		</div>' . "\n";
        }

        $return_string .= "	</div>\n";

        foreach ($spelltree as $tree) {
            if (0 == $tree['id']) {
                $return_string .= '	<div id="spelltree_' . $tree['id'] . '">' . "\n";
            } else {
                $return_string .= '	<div id="spelltree_' . $tree['id'] . '" style="display:none;">' . "\n";
            }

            $num_pages = count($tree['spells']);

            $first_page = true;

            $page = 0;

            foreach ($tree['spells'] as $spellpage) {
                if ($first_page) {
                    if (($num_pages - 1) == $page) {
                        $return_string .= '		<div id="page_' . $page . '_' . $tree['id'] . '">' . "\n";

                        $first_page = false;
                    } else {
                        $return_string .= '		<div id="page_' . $page . '_' . $tree['id'] . '">' . "\n";

                        $return_string .= '			<div class="spell_page_forward" onclick="show(\'page_' . ($page + 1) . '_' . $tree['id'] . '\');hide(\'page_' . $page . '_' . $tree['id'] . '\');">' . $wordings[$roster_conf['roster_lang']]['next'] . ' <img src="' . $roster_conf['img_url'] . 'spellbook/pageforward.gif" class="navicon" alt=""></div>' . "\n";

                        $first_page = false;
                    }
                } elseif (($num_pages - 1) == $page) {
                    $return_string .= '		<div id="page_' . $page . '_' . $tree['id'] . '" style="display:none;">' . "\n";

                    $return_string .= '			<div class="spell_page_back" onclick="show(\'page_' . ($page - 1) . '_' . $tree['id'] . '\');hide(\'page_' . $page . '_' . $tree['id'] . '\');"><img src="' . $roster_conf['img_url'] . 'spellbook/pageback.gif" class="navicon" alt=""> ' . $wordings[$roster_conf['roster_lang']]['prev'] . '</div>' . "\n";
                } else {
                    $return_string .= '		<div id="page_' . $page . '_' . $tree['id'] . '" style="display:none;">' . "\n";

                    $return_string .= '			<div class="spell_page_back" onclick="show(\'page_' . ($page - 1) . '_' . $tree['id'] . '\');hide(\'page_' . $page . '_' . $tree['id'] . '\');"><img src="' . $roster_conf['img_url'] . 'spellbook/pageback.gif" class="navicon" alt=""> ' . $wordings[$roster_conf['roster_lang']]['prev'] . '</div>' . "\n";

                    $return_string .= '			<div class="spell_page_forward" onclick="show(\'page_' . ($page + 1) . '_' . $tree['id'] . '\');hide(\'page_' . $page . '_' . $tree['id'] . '\');">' . $wordings[$roster_conf['roster_lang']]['next'] . ' <img src="' . $roster_conf['img_url'] . 'spellbook/pageforward.gif" class="navicon" alt=""></div>' . "\n";
                }

                $return_string .= '			<div class="spell_pagenumber">Page ' . ($page + 1) . '</div>' . "\n";

                $icon_num = 0;

                foreach ($spellpage as $spellicons) {
                    if (0 == $icon_num) {
                        $return_string .= '			<div class="spell_container_1">' . "\n";
                    } elseif (7 == $icon_num) {
                        $return_string .= "			</div>\n			<div class=\"spell_container_2\">\n";
                    }

                    $return_string .= '
				<div class="spell_info_container">
					<img src="' . $roster_conf['interface_url'] . $spellicons['icon'] . '.' . $roster_conf['img_suffix'] . '" class="icon" ' . $spellicons['tooltip'] . ' onmouseout="return nd();" alt="">
					<span class="text"><span class="spellYellow">' . $spellicons['name'] . '</span>';

                    if ('' != $spellicons['rank']) {
                        $return_string .= '<br><span class="spellBrown">&nbsp;&nbsp;' . $spellicons['rank'] . '</span>';
                    }

                    $return_string .= "</span>\n				</div>\n";

                    $icon_num++;
                }

                $return_string .= "			</div>\n		</div>\n";

                $page++;
            }

            $return_string .= "	</div>\n";
        }

        $return_string .= "</div>\n";

        return $return_string;
    }

    public function get($field)
    {
        return $this->data[$field];
    }

    public function getNumPets($name, $server)
    {
        global $wowdb; 				//the object derived from class wowdb used to do queries

        $server = $wowdb->escape($server);

        /******************************************************************************************************************
        returns the number of pets the character has in the database
        ******************************************************************************************************************/

        $query = 'SELECT * FROM `' . ROSTER_PLAYERSTABLE . "` WHERE `name` = '$name' AND `server` = '$server'";

        $result = $wowdb->query($query);

        $row = $wowdb->fetch_assoc($result);

        $member_id = $row['member_id'];

        $query = 'SELECT * FROM `' . ROSTER_PETSTABLE . "` WHERE `member_id` = '$member_id' order by `level` DESC";

        $result = $wowdb->query($query);

        return $wowdb->num_rows($result);
    }

    public function printPet($name, $server)
    {
        global $wowdb, $wordings, $roster_conf;

        $lang = $this->data['clientLocale'];

        $server = $wowdb->escape($server);

        /******************************************************************************************************************
        Gets all the pets from the database associated with that character from that server.
        ******************************************************************************************************************/

        $query = 'SELECT * FROM `' . ROSTER_PLAYERSTABLE . "` WHERE `name` = '$name' AND `server` = '$server'";

        $result = $wowdb->query($query);

        $row = $wowdb->fetch_assoc($result);

        $member_id = $row['member_id'];

        $query = 'SELECT * FROM `' . ROSTER_PETSTABLE . "` WHERE `member_id` = '$member_id' ORDER BY `level` DESC";

        $result = $wowdb->query($query);

        $petNum = 1;

        while (false !== ($row = $wowdb->fetch_assoc($result))) {
            $showxpBar = true;

            if (mb_strlen($row['xp']) < 1) {
                $showxpBar = false;
            }

            [$xpearned, $totalxp] = preg_split(':', $row['xp']);

            if (0 == $totalxp) {
                $xp_percent = .00;
            } else {
                $xp_percent = $xpearned / $totalxp;
            }

            $barpixelwidth = floor(381 * $xp_percent);

            $xp_percent_word = floor($xp_percent * 100) . '%';

            $unusedtp = $row['totaltp'] - $row['usedtp'];

            if (60 == $row['level']) {
                $showxpBar = false;
            }

            $tmp = preg_split(':', $row['stat_str']);

            $str = $tmp[0];

            $tmp = preg_split(':', $row['stat_agl']);

            $agi = $tmp[0];

            $tmp = preg_split(':', $row['stat_sta']);

            $sta = $tmp[0];

            $tmp = preg_split(':', $row['stat_int']);

            $int = $tmp[0];

            $tmp = preg_split(':', $row['stat_spr']);

            $spr = $tmp[0];

            $tmp = preg_split(':', $row['armor']);

            $basearmor = $tmp[0];

            switch ($petNum) {
                case 1:
                    $left = 35;
                    $top = 285;
                    break;
                case 2:
                    $left = 85;
                    $top = 285;
                    break;
                case 3:
                    $left = 135;
                    $top = 285;
                    break;
                default:
                    $left = 185;
                    $top = 285;
                    break;
            }

            // Start Warlock Pet Icon Mod

            $imp = 'Interface/Icons/Spell_Shadow_SummonImp';

            $void = 'Interface/Icons/Spell_Shadow_SummonVoidWalker';

            $suc = 'Interface/Icons/Spell_Shadow_SummonSuccubus';

            $fel = 'Interface/Icons/Spell_Shadow_SummonFelHunter';

            $inferno = 'Interface/Icons/Spell_Shadow_SummonInfernal';

            $felguard = 'Interface/Icons/Spell_Shadow_SummonFelGuard';

            $iconStyle = 'cursor:pointer;position:absolute;left:' . $left . 'px;top:' . $top . 'px;height:40px;width:40px;';

            if ($row['type'] == $wordings[$lang]['Imp']) {
                $row['icon'] = $imp;
            }

            if ($row['type'] == $wordings[$lang]['Voidwalker']) {
                $row['icon'] = $void;
            }

            if ($row['type'] == $wordings[$lang]['Succubus']) {
                $row['icon'] = $suc;
            }

            if ($row['type'] == $wordings[$lang]['Felhunter']) {
                $row['icon'] = $fel;
            }

            if ($row['type'] == $wordings[$lang]['Felguard']) {
                $row['icon'] = $felguard;
            }

            if ($row['type'] == $wordings[$lang]['Infernal']) {
                $row['icon'] = $inferno;
            }

            // End Warlock Pet Icon Mod

            if ('' == $row['icon'] || !isset($row['icon'])) {
                $row['icon'] = 'Interface/Icons/INV_Misc_QuestionMark';
            }

            $icons .= '<img src="' . $roster_conf['interface_url'] . $row['icon'] . '.' . $roster_conf['img_suffix'] . '" onclick="showPet(\'' . $petNum . '\')" style="' . $iconStyle . '" alt="" ' . makeOverlib($row['name'], $row['type'], '', 2, '', ',WRAP') . '>';

            $petName .= '<span class="petName" style="top: 10px; left: 95px; display: none;" id="pet_name' . $petNum . '">' . stripslashes($row['name']) . '</span>';

            $petTitle .= '<span class="petName" style="top: 30px; left: 95px; display: none;" id="pet_title' . $petNum . '">' . $wordings[$lang]['level'] . ' ' . $row['level'] . ' ' . stripslashes($row['type']) . '</span>';

            $loyalty .= '<span class="petName" style="top: 50px; left: 95px; display: none;" id="pet_loyalty' . $petNum . '">' . $row['loyalty'] . '</span>';

            $petIcon .= '<img id="pet_top_icon' . $petNum . '" style="position: absolute; left: 30px; top: 8px; width: 64px; height: 64px; display: none;" src="' . $roster_conf['interface_url'] . $row['icon'] . '.' . $roster_conf['img_suffix'] . '" alt="">';

            $resistances .= '<div  class="pet_resistance" id="pet_resistances' . $petNum . '">
				<ul>
					<li class="pet_fire"><span class="white">' . $row['res_fire'] . '</span></li>
					<li class="pet_nature"><span class="white">' . $row['res_nature'] . '</span></li>
					<li class="pet_arcane"><span class="white">' . $row['res_arcane'] . '</span></li>
					<li class="pet_frost"><span class="white">' . $row['res_frost'] . '</span></li>
					<li class="pet_shadow"><span class="white">' . $row['res_shadow'] . '</span></li>
				</ul>
			</div>';

            $stats .= '
			<div class="petStatsBg" id="pet_stats_table' . $petNum . '" >
					<table style="text-align: left; position: absolute; top: 5px; left: 5px;" border="0" cellpadding="2" cellspacing="0" width="130">
						<tr>
							<td class="petStatsTableStatname">' . $wordings[$lang]['strength'] . ':</td>
							<td class="petStatsTableStatValue">' . $str . '</td>
						</tr>
						<tr>
							<td class="petStatsTableStatname">' . $wordings[$lang]['agility'] . ':</td>
							<td class="petStatsTableStatValue">' . $agi . '</td>
						</tr>
						<tr>
							<td class="petStatsTableStatname">' . $wordings[$lang]['stamina'] . ':</td>
							<td class="petStatsTableStatValue">' . $sta . '</td>
						</tr>
						<tr>
							<td class="petStatsTableStatname">' . $wordings[$lang]['intellect'] . ':</td>
							<td class="petStatsTableStatValue">' . $int . '</td>
						</tr>
						<tr>
							<td class="petStatsTableStatname">' . $wordings[$lang]['spirit'] . ':</td>
							<td class="petStatsTableStatValue">' . $spr . '</td>
						</tr>
					</table>

					<table style="text-align: left;	position: absolute;	top: 5px; left: 146px;" border="0" cellpadding="2" cellspacing="0" width="130">
						<tr>
							<td class="petStatsTableStatname">' . $wordings[$lang]['attack'] . ':</td>
							<td class="petStatsTableStatValue">' . $row['melee_rating'] . '</td>
						</tr>
						<tr>
							<td class="petStatsTableStatname">' . $wordings[$lang]['power'] . ':</td>
							<td class="petStatsTableStatValue">' . $row['melee_power'] . '</td>
						</tr>
						<tr>
							<td class="petStatsTableStatname">' . $wordings[$lang]['damage'] . ':</td>
							<td class="petStatsTableStatValue">' . str_replace(':', ' - ', $row['melee_range']) . '</td>
						</tr>
						<tr>
							<td class="petStatsTableStatname">' . $wordings[$lang]['defense'] . ':</td>
							<td class="petStatsTableStatValue">' . $row['defense'] . '</td>
						</tr>
						<tr>
							<td class="petStatsTableStatname">' . $wordings[$lang]['armor'] . ':</td>
							<td class="petStatsTableStatValue">' . $basearmor . '</td>
						</tr>
					</table>
			</div>';

            if ($showxpBar) {
                $xpBar .= '
				<div class="pet_xp" id="pet_xp_bar' . $petNum . '">
		            <div class="pet_xpbox">
		                <img class="xp_bg" width="100%" height="15" src="' . $roster_conf['img_url'] . 'barxpempty.gif" alt="">
		                <img src="' . $roster_conf['img_url'] . 'expbar-var2.gif" alt="" class="pet_bit" width="' . $barpixelwidth . '">
		                <span class="pet_xp_level">' . $xpearned . '/' . $totalxp . ' ( ' . $xp_percent_word . ' )</span>
		            </div>
		        </div>';
            }

            if ('' != $row['totaltp'] && '0' != $row['totaltp']) {
                $trainingPoints .= '
			<span class="petTrainingPts" style="position: absolute; top: 412px; left: 100px;" id="pet_training_nm' . $petNum . '">' . $wordings[$lang]['unusedtrainingpoints'] . ': </span>
			<span class="petTrainingPts" style="color: #FFFFFF; position: absolute; top: 413px; left: 305px;" id="pet_training_pts' . $petNum . '" >' . $unusedtp . ' / ' . $row['totaltp'] . '</span>';
            }

            $hpMana .= '
			<div id="pet_hpmana' . $petNum . '" class="health_mana" style="position: absolute;	left: 35px; top: 65px; display: none;">
				<div class="health" style="text-align: left;">
					' . $wordings[$lang]['health'] . ': <span class="white">' . $row['health'] . '</span>
				</div>
		        <div class="mana" style="text-align: left;">
		        	' . $wordings[$lang]['mana'] . ': <span class="white">' . $row['mana'] . '</span>
		        </div>
			</div>';

            $petNum++;
        }

        //return all the objects

        return
        $petName
        . $petTitle
        . $loyalty
        . $petIcon
        . $resistances
        . $stats
        . $xpBar
        . $trainingPoints
        . $hpMana
        . $icons
        ;
    }

    public function printStat($statname)
    {
        global $wordings;

        $lang = $this->data['clientLocale'];

        $base = $this->data[$statname];

        $current = $this->data[$statname . '_c'];

        $buff = $this->data[$statname . '_b'];

        $debuff = $this->data[$statname . '_d'];

        $id = $statname . ':' . $base . ':' . $current . ':' . $buff . ':' . $debuff;

        if (0 == $buff) {
            $color = 'white';

            $mod_symbol = '';
        } elseif ($buff < 0) {
            $color = 'purple';

            $mod_symbol = '-';
        } else {
            $color = 'green';

            $mod_symbol = '+';
        }

        switch ($statname) {
            case 'stat_str':
                $name = $wordings[$lang]['strength'];
                $tooltip = $wordings[$lang]['strength_tooltip'];
                break;
            case 'stat_int':
                $name = $wordings[$lang]['intellect'];
                $tooltip = $wordings[$lang]['intellect_tooltip'];
                break;
            case 'stat_sta':
                $name = $wordings[$lang]['stamina'];
                $tooltip = $wordings[$lang]['stamina_tooltip'];
                break;
            case 'stat_spr':
                $name = $wordings[$lang]['spirit'];
                $tooltip = $wordings[$lang]['spirit_tooltip'];
                break;
            case 'stat_agl':
                $name = $wordings[$lang]['agility'];
                $tooltip = $wordings[$lang]['agility_tooltip'];
                break;
            case 'stat_armor':
                $name = $wordings[$lang]['armor'];
                $tooltip = $wordings[$lang]['armor_tooltip'];
                if (!empty($this->data['mitigation'])) {
                    $tooltip .= '<br><span class="red">' . $wordings[$lang]['tooltip_damage_reduction'] . ': ' . $this->data['mitigation'] . '%</span>';
                }
                break;
        }

        if ('' == $mod_symbol) {
            $tooltipheader = $name . ' ' . $current;
        } else {
            $tooltipheader = "$name $current ($base <span class=\"$color\">$mod_symbol $buff</span>)";
        }

        $line = '<span style="color:#ffffff;font-size:12px;font-weight:bold;">' . $tooltipheader . '</span><br>';

        $line .= '<span style="color:#DFB801;">' . $tooltip . '</span>';

        $output = '<span ' . makeOverlib($line, '', '', 2) . '>';

        $output .= '<strong class="' . $color . '">' . $current . '</strong>';

        $output .= '</span>';

        return $output;
    }

    public function printRes($resname)
    {
        global $wordings;

        $lang = $this->data['clientLocale'];

        switch ($resname) {
        case 'res_fire':
            $name = $wordings[$lang]['res_fire'];
            $tooltip = $wordings[$lang]['res_fire_tooltip'];
            $color = 'red';
            break;
        case 'res_nature':
            $name = $wordings[$lang]['res_nature'];
            $tooltip = $wordings[$lang]['res_nature_tooltip'];
            $color = 'green';
            break;
        case 'res_arcane':
            $name = $wordings[$lang]['res_arcane'];
            $tooltip = $wordings[$lang]['res_arcane_tooltip'];
            $color = 'yellow';
            break;
        case 'res_frost':
            $name = $wordings[$lang]['res_frost'];
            $tooltip = $wordings[$lang]['res_frost_tooltip'];
            $color = 'blue';
            break;
        case 'res_shadow':
            $name = $wordings[$lang]['res_shadow'];
            $tooltip = $wordings[$lang]['res_shadow_tooltip'];
            $color = 'purple';
            break;
        }

        $tooltipheader = $name;

        $line = '<span style="color:' . $color . ';font-size:12px;font-weight:bold;">' . $tooltipheader . '</span><br>';

        $line .= '<span style="color:#DFB801;text-align:left;">' . $tooltip . '</span>';

        $output .= '<li class="' . mb_substr($resname, 4) . '" ' . makeOverlib($line, '', '', 2, '', ',WRAP') . '>';

        $output .= $this->data[$resname . '_c'];

        $output .= "</li>\n";

        return $output;
    }

    public function printEquip($slot)
    {
        global $roster_conf, $wordings;

        $item = item_get_one($this->data['member_id'], $slot);

        if (isset($item)) {
            $output = $item->out();
        } else {
            $output = '<div class="item" ' . makeOverlib($wordings[$roster_conf['roster_lang']]['empty_equip'], $slot, '', 2, '', ',WRAP') . '>' . "\n";

            if ('Ammo' == $slot) {
                $output .= '<img src="' . $roster_conf['interface_url'] . 'Interface/EmptyEquip/' . $slot . '.gif" class="iconsmall" alt="">' . "\n";
            } else {
                $output .= '<img src="' . $roster_conf['interface_url'] . 'Interface/EmptyEquip/' . $slot . '.gif" class="icon" alt="">' . "\n";
            }

            $output .= "</div>\n";
        }

        return $output;
    }

    public function printAtk($type, $stat)
    {
        global $wordings;

        $lang = $this->data['clientLocale'];

        $atk = $this->data[$type . '_' . $stat];

        $atktooltip = $this->data[$type . '_' . $stat . '_tooltip'];

        if (preg_match(':', $atk)) {
            $atk = preg_replace(':', ' - ', $atk);
        }

        $matches = '';

        preg_match('|\|c[a-f0-9]{2}([a-f0-9]{6})(.+?)\|r|', $atk, $matches);

        $atkcolor = $matches[1] ?? 'ffffff';

        $atk = preg_replace('|\|c[a-f0-9]{8}(.+?)\|r|', '$1', $atk);

        switch ($stat) {
            case 'rating':
                if ('melee' == $type) {
                    $tooltipheader = $wordings[$lang]['melee_rating'];

                    $tooltip = $wordings[$lang]['melee_rating_tooltip'];
                } else {
                    $tooltipheader = $wordings[$lang]['range_rating'];

                    $tooltip = $wordings[$lang]['range_rating_tooltip'];
                }
                break;
            case 'power':
                if ('melee' == $type) {
                    $tooltipheader = $wordings[$lang]['melee_att_power'] . ' ' . $atk;
                } else {
                    $tooltipheader = $wordings[$lang]['range_att_power'] . ' ' . $atk;
                }
                $tooltip = nl2br($atktooltip);
                break;
            case 'range':
                $tooltipheader = $wordings[$lang]['damage'] . ' ' . $atk;
                if (!empty($atktooltip)) {
                    $tooltip = str_replace("\n", '</td></tr><tr><td style="font-size:10px;">', $atktooltip);

                    $tooltip = str_replace(":\t", ':</td><td align="right" style="color:white;font-size:10px;">', $tooltip);

                    $tooltip = '<table style="width:220px;" cellspacing="0" cellpadding="0"><tr><td colspan="2">' . $tooltip . '</table>';
                } else {
                    $tooltip = 'N/A';
                }
                break;
        }

        $line = "<span style=\"color:#FFFFFF;font-size:12px;font-weight:bold;\">$tooltipheader</span><br>";

        $line .= "<span style=\"color:#DFB801;\">$tooltip</span>";

        if ('' == $atk) {
            $atk = 'N/A';
        }

        $output = '<span>';

        $output .= '<strong ' . makeOverlib($line, '', '', 2) . ' style="color:#' . $atkcolor . '">' . $atk . '</strong>';

        $output .= '</span>';

        return $output;
    }

    public function printTab($name, $div, $enabled = false)
    {
        /**********************************************************
         * onlclick event has additional Javascript that will
         * select the first pet and show the elements for it
        **********************************************************/

        if ('page2' == $div) {
            $action = 'onclick="showPet(1); doTab( \'' . $div . '\' )"';
        } else {
            $action = 'onclick="doTab( \'' . $div . '\' )"';
        }

        if ($enabled) {
            $output = '<div class="tab" ' . $action . '><span id="tabfont' . $div . '" class="white">' . $name . '</span></div>';
        } else {
            $output = '<div class="tab" ' . $action . '><span id="tabfont' . $div . '" class="yellow">' . $name . '</span></div>';
        }

        return $output;
    }

    public function printTalents($member_id)
    {
        global $roster_conf, $wowdb, $wordings;

        $lang = $this->data['clientLocale'];

        $query = 'SELECT * FROM `' . ROSTER_TALENTTREETABLE . "` WHERE `member_id` = '$member_id' ORDER BY `order`;";

        $trees = $wowdb->query($query);

        if ($wowdb->num_rows($trees) > 0) {
            $g = 0;

            $outtalent = '<div><img class="tbar" height="5" src="' . $roster_conf['img_url'] . 'tbar.png" width="300" alt="">';

            $outtalent .= '<img id="tlabbg1" height="32" src="' . $roster_conf['img_url'] . 'atab.gif" width="105" alt="">';

            $outtalent .= '<img id="tlabbg2" height="32" src="' . $roster_conf['img_url'] . 'itab.gif" width="105" alt="">';

            $outtalent .= '<img id="tlabbg3" height="32" src="' . $roster_conf['img_url'] . 'itab.gif" width="105" alt=""></div>';

            while ($tree = $wowdb->fetch_assoc($trees)) {
                $g++;

                $c = 0;

                if (1 == $g) {
                    $outtalent .= '<div class="tablabelactive" id="tlab' . $g . '" onclick="setActiveTalentWrapper(\'' . $g . '\',\'' . $roster_conf['img_url'] . '\');">' . $tree['tree'] . '</div>';
                } else {
                    $outtalent .= '<div class="tablabel" id="tlab' . $g . '" onclick="setActiveTalentWrapper(\'' . $g . '\',\'' . $roster_conf['img_url'] . '\');">' . $tree['tree'] . '</div>';
                }

                $output .= '<div id="talentwrapper' . $tree['order'] . '">';

                $output .= '<div id="talentpage' . $tree['order'] . '" style="background:url(\'' . $roster_conf['interface_url'] . $tree['background'] . '.' . $roster_conf['img_suffix'] . '\') no-repeat;">';

                $output .= '<span class="talspent">' . $wordings[$lang]['pointsspent'] . ' ' . $tree['pointsspent'] . '</span>
	<table align="center" width="100%">
	  <tr>';

                while ($c < 4) {
                    $c++;

                    $r = 0;

                    $output .= '
	    <td width="20"></td>
	    <td>
	      <table>';

                    while ($r < 9) {
                        $r++;

                        $output .= '        <tr>
	          <td height="45">';

                        $query4 = 'SELECT * FROM `' . ROSTER_TALENTSTABLE . "` where `member_id` = '$member_id' and `tree` = '" . $tree['tree'] . "' and `column` = '" . $c . "' and `row` ='" . $r . "'";

                        $talents4 = $wowdb->query($query4);

                        if (0 == $wowdb->num_rows($talents4)) {
                            $output .= '<div class="item"><img src="' . $roster_conf['img_url'] . 'pixel.gif" class="icon" alt=""></div>';
                        } else {
                            $talent4 = $wowdb->fetch_assoc($talents4);

                            $path4 = $roster_conf['interface_url'] . $talent4['texture'] . '.' . $roster_conf['img_suffix'];

                            $first_line = true;

                            $talent_tooltip = '';

                            // Compatibility with < 1.7

                            $talent4['tooltip'] = str_replace('<br>', "\n", $talent4['tooltip']);

                            foreach (explode("\n", $talent4['tooltip']) as $line) {
                                if ($first_line) {
                                    $color = 'ffffff;font-size:12px;font-weight: bold;';

                                    $first_line = false;
                                } else {
                                    if ('|c' == mb_substr($line, 0, 2)) {
                                        $color = mb_substr($line, 4, 6);

                                        $line = mb_substr($line, 10, -2);
                                    } elseif (0 === mb_strpos($line, $wordings[$lang]['tooltip_rank'])) {
                                        $color = '00ff00;font-size:11px';
                                    } elseif (0 === mb_strpos($line, $wordings[$lang]['tooltip_next_rank'])) {
                                        $color = 'ffffff;font-size:11px';
                                    } elseif (0 === mb_strpos($line, $wordings[$lang]['tooltip_requires'])) {
                                        $color = 'ff0000';
                                    } else {
                                        $color = 'dfb801';
                                    }
                                }

                                if ('' != $line) {
                                    $talent_tooltip .= '<span style="color:#' . $color . ';">' . $line . '</span><br>';
                                }
                            }

                            if (0 == $talent4['rank']) {
                                $class = 'talenticonGreyed';
                            } else {
                                $class = 'talenticon';
                            }

                            $output .= '<div class="item" ' . makeOverlib($talent_tooltip, '', '', 2) . '><img src="' . $path4 . '" class="' . $class . '" width="40" height="40" alt="">';

                            if ($talent4['rank'] == $talent4['maxrank']) {
                                $output .= '<span class="talvalue yellow">' . $talent4['rank'] . '</span>';
                            } elseif ($talent4['rank'] < $talent4['maxrank'] && $talent4['rank'] > 0) {
                                $output .= '<span class="talvalue green">' . $talent4['rank'] . '</span>';
                            }

                            $output .= '</div>';
                        }

                        $output .= "</td>\n        </tr>\n";
                    }

                    $output .= "      </table></td>\n";
                }

                $output .= "  </tr>\n</table></div></div>\n";
            }

            return $output . $outtalent;
        }
  

        return 'No Talents for ' . $this->data['name'];
    }

    public function printSkills()
    {
        global $skilltypes;

        [$major, $minor] = explode('.', $this->data['version']);

        for ($i = 1; $i < 7; $i++) {
            if ((0 == $major) && ($minor < 96)) {
                $skills = skill_get_many_by_type($this->data['member_id'], $skilltypes[$this->data['clientLocale']][$i]);
            } else {
                $skills = skill_get_many_by_order($this->data['member_id'], $i);
            }

            if (isset($skills[0])) {
                $returnstring .= $skills[0]->outHeader();

                foreach ($skills as $skill) {
                    $returnstring .= $skill->out();
                }
            }
        }

        return $returnstring;
    }

    public function printReputation()
    {
        $reputation = get_reputation($this->data['member_id']);

        $temp = '';

        for ($i = 0, $iMax = count($reputation); $i < $iMax; $i++) {
            $temp = $reputation[$i]->outHeader($temp);

            echo $reputation[$i]->out();
        }
    }

    public function printHonor()
    {
        global $roster_conf, $wowdb, $wordings, $guild_info;

        $lang = $this->data['clientLocale'];

        $icon = '';

        switch (mb_substr($guild_info['faction'], 0, 1)) {
            case 'A':
                $icon = '<img src="' . $roster_conf['img_url'] . 'battleground-alliance.png" alt="">';
                break;
            case 'H':
                $icon = '<img src="' . $roster_conf['img_url'] . 'battleground-horde.png" alt="">';
                break;
        }

        $output = '<div class="honortext">' . $wordings[$lang]['honor'] . ':<span>' . $this->data['honorpoints'] . '</span>' . $icon . '</div>' . "\n";

        $output .= '<div class="today">' . $wordings[$lang]['today'] . '</div>' . "\n";

        $output .= '<div class="yesterday">' . $wordings[$lang]['yesterday'] . '</div>' . "\n";

        $output .= '<div class="lifetime">' . $wordings[$lang]['lifetime'] . '</div>' . "\n";

        $output .= '<div class="divider"></div>' . "\n";

        $output .= '<div class="killsline">' . $wordings[$lang]['kills'] . '</div>' . "\n";

        $output .= '<div class="killsline1">' . $this->data['sessionHK'] . '</div>' . "\n";

        $output .= '<div class="killsline2">' . $this->data['yesterdayHK'] . '</div>' . "\n";

        $output .= '<div class="killsline3">' . $this->data['lifetimeHK'] . '</div>' . "\n";

        $output .= '<div class="honorline">' . $wordings[$lang]['honor'] . '</div>' . "\n";

        $output .= '<div class="honorline1">~' . $this->data['sessionCP'] . '</div>' . "\n";

        $output .= '<div class="honorline2">' . $this->data['yesterdayContribution'] . '</div>' . "\n";

        $output .= '<div class="honorline3">-</div>' . "\n";

        $output .= '<div class="arenatext">' . $wordings[$lang]['arena'] . ':<span>' . $this->data['arenapoints'] . '</span><img src="' . $roster_conf['img_url'] . 'arenapointsicon.png" alt=""></div>' . "\n";

        return $output;
    }

    public function out()
    {
        global $wordings, $roster_conf;

        $lang = $this->data['clientLocale'];

        if ('' != $this->data['name']) {
            ?>
<script type="text/javascript">
<!--
  addTab('page1')
<?php
if ($this->getNumPets($this->data['name'], $this->data['server'])) {
                print '  addTab(\'page2\')' . "\n";
            } ?>
  addTab('page3')
  addTab('page4')
<?php
if ($roster_conf['show_talents']) {
                print '  addTab(\'page5\')' . "\n";
            } ?>
  addTab('page6')
//-->
</script>


<div class="char" id="char"><!-- Begin char -->
  <div class="main"><!-- Begin char-main -->
    <div class="top" id="top"><!-- Begin char-main-top -->
      <div class="headline_1"><?php print $this->data['name']; ?></div>
      <div class="headline_2">Level <?php print($this->data['level'] . ' - ' . $this->data['sex'] . ' ' . $this->data['race'] . ' ' . $this->data['class']); ?></div>
<?php

if (isset($this->data['guild_name'])) {
    echo '      <div class="headline_2">' . $this->data['guild_title'] . ' of ' . $this->data['guild_name'] . "</div>\n";
} ?>
    </div><!-- End char-main-top -->
    <div class="page1" id="page1"><!-- begin char-main-page1 -->
      <div class="left"><!-- begin char-main-page1-left -->
        <div class="equip"><?php print $this->printEquip('Head'); ?></div>
        <div class="equip"><?php print $this->printEquip('Neck'); ?></div>
        <div class="equip"><?php print $this->printEquip('Shoulder'); ?></div>
        <div class="equip"><?php print $this->printEquip('Back'); ?></div>
        <div class="equip"><?php print $this->printEquip('Chest'); ?></div>
        <div class="equip"><?php print $this->printEquip('Shirt'); ?></div>
        <div class="equip"><?php print $this->printEquip('Tabard'); ?></div>
        <div class="equip"><?php print $this->printEquip('Wrist'); ?></div>
      </div><!-- end char-main-page1-left -->
      <div class="middle"><!-- begin char-main-page1-middle -->
        <div class="portrait"><!-- begin char-main-page1-middle-portrait -->
          <div class="resistance"><!-- begin char-main-page1-middle-portrait-resistance -->
            <ul>
                <?php print $this->printRes('res_fire'); ?>
            	<?php print $this->printRes('res_nature'); ?>
            	<?php print $this->printRes('res_arcane'); ?>
            	<?php print $this->printRes('res_frost'); ?>
            	<?php print $this->printRes('res_shadow'); ?>
            </ul>
          </div><!-- end char-main-page1-middle-portrait-resistance -->
          <div class="health_mana"><!-- begin char-main-page1-middle-portrait-health_mana -->
            <div class="health"><?php print $wordings[$lang]['health'] . ': <span class="white">' . $this->data['health']; ?></span></div>
            <?php

if ($this->data['class'] == $wordings[$lang]['Warrior']) {
    print '<div class="mana">' . $wordings[$lang]['rage'] . ': <span class="white">' . $this->data['mana'] . '</span></div>';
} elseif ($this->data['class'] == $wordings[$lang]['Rogue']) {
    print '<div class="mana">' . $wordings[$lang]['energy'] . ': <span class="white">' . $this->data['mana'] . '</span></div>';
} else {
    print '<div class="mana">' . $wordings[$lang]['mana'] . ': <span class="white">' . $this->data['mana'] . '</span></div>';
} ?>

          </div><!-- end char-main-page1-middle-portrait-health_mana -->
          <div class="info"><!-- begin char-main-page1-middle-portrait-info -->
<?php

if ('0' != $this->data['crit']) {
    print	$wordings[$lang]['crit'] . ': <span class="white">' . $this->data['crit'] . '%</span><br>' . "\n";
}

            if ('0' != $this->data['dodge']) {
                print	$wordings[$lang]['dodge'] . ': <span class="white">' . $this->data['dodge'] . '%</span><br>' . "\n";
            }

            if ('0' != $this->data['parry']) {
                print	$wordings[$lang]['parry'] . ': <span class="white">' . $this->data['parry'] . '%</span><br>' . "\n";
            }

            if ('0' != $this->data['block']) {
                print	$wordings[$lang]['block'] . ': <span class="white">' . $this->data['block'] . '%</span><br>' . "\n";
            }

            if ($this->data['talent_points']) {
                print '            ' . $wordings[$lang]['unusedtalentpoints'] . ': <span class="white">' . $this->data['talent_points'] . '</span><br>';
            }

            $TimeLevelPlayedConverted = seconds_to_time($this->data['timelevelplayed']);

            $TimePlayedConverted = seconds_to_time($this->data['timeplayed']);

            print "<br>\n";

            print '            ' . $wordings[$lang]['timeplayed'] . ': <span class="white">' . $TimePlayedConverted[days] . $TimePlayedConverted[hours] . $TimePlayedConverted[minutes] . $TimePlayedConverted[seconds] . '</span><br>' . "\n";

            print '            ' . $wordings[$lang]['timelevelplayed'] . ': <span class="white">' . $TimeLevelPlayedConverted[days] . $TimeLevelPlayedConverted[hours] . $TimeLevelPlayedConverted[minutes] . $TimeLevelPlayedConverted[seconds] . '</span>' . "\n"; ?>
          </div><!-- end char-main-page1-middle-portrait-info -->
          <div class="xp" style="padding-left:12px;"><!-- begin char-main-page1-middle-portrait-xp -->
            <div class="xpbox">
<?php

// Code to write a "Max Exp bar" just like in SigGen
if ('60' == $this->data['level']) {
    $expbar_width = '248';

    $expbar_text = $wordings[$lang]['max_exp'];
} else {
    $expbar_width = $this->printXP();

    [$xp, $xplevel, $xprest] = explode(':', $this->data['exp']);

    if ('0' != $xplevel && '' != $xplevel) {
        $exp_percent = ($xplevel > 0 ? round(($xp / $xplevel) * 100) : 0);

        if ($xprest > 0) {
            $expbar_text = $xp . '/' . $xplevel . ' : ' . $xprest . ' (' . $exp_percent . '%)';
        } else {
            $expbar_text = $xp . '/' . $xplevel . ' (' . $exp_percent . '%)';
        }
    }
} ?>
              <img class="bg" alt="" src="<?php echo $roster_conf['img_url']?>barxpempty.gif">
              <img src="<?php echo $roster_conf['img_url']?>expbar-var2.gif" alt="" class="bit" width="<?php print $expbar_width; ?>">
              <span class="level"><?php print $expbar_text; ?></span>
            </div>
          </div><!-- end char-main-page1-middle-portrait-xp -->
        </div><!-- end char-main-page1-middle-portrait -->
        <div class="bottom"><!-- begin char-main-page1-middle-bottom -->
          <div class="padding">
            <ul class="stats">
              <li><?php print $wordings[$lang]['strength'] . ': ' . $this->printStat('stat_str'); ?></li>
              <li><?php print $wordings[$lang]['agility'] . ': ' . $this->printStat('stat_agl'); ?></li>
              <li><?php print $wordings[$lang]['stamina'] . ': ' . $this->printStat('stat_sta'); ?></li>
              <li><?php print $wordings[$lang]['intellect'] . ': ' . $this->printStat('stat_int'); ?></li>
              <li><?php print $wordings[$lang]['spirit'] . ': ' . $this->printStat('stat_spr'); ?></li>
              <li><?php print $wordings[$lang]['armor'] . ': ' . $this->printStat('stat_armor'); ?></li>
            </ul>
            <ul class="stats">
              <li><?php print $wordings[$lang]['melee_att'] . ' ' . $this->printAtk('melee', 'rating') . "\n"; ?>
                <ul>
                  <li><?php print $wordings[$lang]['power'] . ': ' . $this->printAtk('melee', 'power'); ?></li>
                  <li><?php print $wordings[$lang]['damage'] . ': ' . $this->printAtk('melee', 'range'); ?></li>
                </ul></li>
              <li><?php print $wordings[$lang]['range_att'] . ' ' . $this->printAtk('ranged', 'rating') . "\n"; ?>
                <ul>
                  <li><?php print $wordings[$lang]['power'] . ': ' . $this->printAtk('ranged', 'power'); ?></li>
                  <li><?php print $wordings[$lang]['damage'] . ': ' . $this->printAtk('ranged', 'range'); ?></li>
                </ul></li>
            </ul>
          </div><!-- end char-main-page1-middle-bottom-padding -->
          <div class="hands">
            <div class="weapon0"><?php print $this->printEquip('MainHand'); ?></div>
            <div class="weapon1"><?php print $this->printEquip('SecondaryHand'); ?></div>
            <div class="weapon2"><?php print $this->printEquip('Ranged'); ?></div>
            <div class="ammo"><?php print $this->printEquip('Ammo'); ?></div>
          </div><!-- end char-main-page1-middle-bottom-hands -->
        </div><!-- end char-main-page1-middle-bottom -->
      </div><!-- end char-main-page1-middle -->
      <div class="right"> <!-- begin char-main-page1-right -->
        <div class="equip"><?php print $this->printEquip('Hands'); ?></div>
        <div class="equip"><?php print $this->printEquip('Waist'); ?></div>
        <div class="equip"><?php print $this->printEquip('Legs'); ?></div>
        <div class="equip"><?php print $this->printEquip('Feet'); ?></div>
        <div class="equip"><?php print $this->printEquip('Finger0'); ?></div>
        <div class="equip"><?php print $this->printEquip('Finger1'); ?></div>
        <div class="equip"><?php print $this->printEquip('Trinket0'); ?></div>
        <div class="equip"><?php print $this->printEquip('Trinket1'); ?></div>
      </div><!-- end char-main-page1-right -->
    </div><!-- end char-main-page1 -->
<?php

if ($this->getNumPets($this->data['name'], $this->data['server']) > 0) {
    $petTab = $this->printPet($this->data['name'], $this->data['server']);

    $tab = '
    <div class="page2" id="page2"><!-- begin char-main-page2 -->
      <div class="left"></div>
      <div class="pet"><!-- begin char-main-page2-pet -->
		    ' . $petTab . '
      </div>
      <div class="right"></div>
		</div><!-- end char-main-page2 -->';

    print $tab;
} ?>

    <div class="page3" id="page3"><!-- begin char-main-page3 -->
      <div class="left"></div>
      <div class="reputation"><!-- begin char-main-page3-reputation -->
        <?php print $this->printReputation(); ?>
      </div>
      <div class="right"></div>
    </div><!-- end char-main-page3 -->
    <div class="page4" id="page4"><!-- begin char-main-page4 -->
      <div class="left"></div>
      <div class="skills"><!-- begin char-main-page4-skills -->
        <?php print $this->printSkills(); ?>
      </div>
      <div class="right"></div>
    </div><!-- end char-main-page4 -->
<?php

if ($roster_conf['show_talents']) {
    $talent_tab = '
    <div class="page5" id="page5"><!-- begin char-main-page5 -->
      <div class="left"></div>
      <div class="talents"><!-- begin char-main-page5-talents -->
		    ' . $this->printTalents($this->data['member_id']) . '
      </div>
      <div class="right"></div>
    </div><!-- end char-main-page5 -->';

    print $talent_tab;
} ?>

    <div class="page6" id="page6"><!-- begin char-main-page6 -->
      <div class="left"></div>
      <div class="honor"><!-- begin char-main-page6-honor -->
        <?php print $this->printHonor(); ?>
      </div>
      <div class="right"></div>
    </div><!-- end char-main-page6 -->
  </div><!-- end char-main -->
  <div class="bottomBorder"></div>
  <div class="tabs"><!-- begin char-tabs -->
<?php

print $this->printTab($wordings[$lang]['tab1'], 'page1', true);

            echo "\n";

            if ($this->data['class'] == $wordings[$lang]['Hunter'] || $this->data['class'] == $wordings[$lang]['Warlock']) {
                if ($this->getNumPets($this->data['name'], $this->data['server']) > 0) {
                    print $this->printTab($wordings[$lang]['tab2'], 'page2');

                    echo "\n";
                }
            }

            print $this->printTab($wordings[$lang]['tab3'], 'page3') . "\n";

            print $this->printTab($wordings[$lang]['tab4'], 'page4') . "\n";

            if ($roster_conf['show_talents']) {
                print $this->printTab($wordings[$lang]['tab5'], 'page5');

                echo "\n";
            }

            print $this->printTab($wordings[$lang]['tab6'], 'page6') . "\n";

            echo '  </div><!-- end char-tabs -->
</div><!-- end char -->
';
        } else {
            message_die('Sorry no data in database for ' . $_GET['name'] . ' of ' . $_GET['server'], 'Character Not Found');
        }
    }
}

function char_get_one_by_id($member_id)
{
    global $wowdb;

    $query = 'SELECT a.*, b.*, c.guild_name FROM `' . ROSTER_PLAYERSTABLE . '` a, `' . ROSTER_MEMBERSTABLE . '` b, `' . ROSTER_GUILDTABLE . '` c ' .
    "WHERE a.member_id = b.member_id AND a.member_id = '$member_id' AND a.guild_id = c.guild_id";

    $result = $wowdb->query($query);

    if ($wowdb->num_rows($result) > 0) {
        $data = $wowdb->fetch_assoc($result);

        return new char($data);
    }
  

    return false;
}

function char_get_one($name, $server)
{
    global $wowdb;

    $name = $wowdb->escape($name);

    $server = $wowdb->escape($server);

    $query = 'SELECT a.*, b.*, c.guild_name FROM `' . ROSTER_PLAYERSTABLE . '` a, `' . ROSTER_MEMBERSTABLE . '` b, `' . ROSTER_GUILDTABLE . '` c ' .
    "WHERE a.member_id = b.member_id AND a.name = '$name' AND a.server = '$server' AND a.guild_id = c.guild_id";

    $result = $wowdb->query($query);

    if ($wowdb->num_rows($result) > 0) {
        $data = $wowdb->fetch_assoc($result);

        return new char($data);
    }
  

    return false;
}

function DateCharDataUpdated($name)
{
    global $wowdb, $roster_conf, $phptimeformat;

    $query1 = 'SELECT `dateupdatedutc` FROM `' . ROSTER_PLAYERSTABLE . "` WHERE `name` = '$name'";

    $result1 = $wowdb->query($query1);

    $data1 = $wowdb->fetch_assoc($result1);

    $dateupdatedutc = $data1['dateupdatedutc'];

    [$month, $day, $year, $hour, $minute, $second] = sscanf($dateupdatedutc, '%2d/%2d/%2d %2d:%2d:%2d');

    $localtime = mktime($hour + $roster_conf['localtimeoffset'], $minute, $second, $month, $day, $year, -1);

    return date($phptimeformat[$roster_conf['roster_lang']], $localtime);
}

function seconds_to_time($seconds)
{
    while ($seconds >= 60) {
        if ($seconds >= 86400) {
            // there is more than 1 day

            $days = floor($seconds / 86400);

            $seconds -= ($days * 86400);
        } elseif ($seconds >= 3600) {
            $hours = floor($seconds / 3600);

            $seconds -= ($hours * 3600);
        } elseif ($seconds >= 60) {
            $minutes = floor($seconds / 60);

            $seconds -= ($minutes * 60);
        }
    }

    // convert variables into sentence structure components

    if (!$days) {
        $days = '';
    } else {
        if (1 == $days) {
            $days .= 'd, ';
        } else {
            $days .= 'd, ';
        }
    }

    if (!$hours) {
        $hours = '';
    } else {
        $hours .= 'h, ';
    }

    if (!$minutes) {
        $minutes = '';
    } else {
        $minutes .= 'm, ';
    }

    if (!$seconds) {
        $seconds = '';
    } else {
        $seconds .= 's';
    }

    return ['days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $seconds];
}

/*****************************************************************************************************
 * Code originally from cybrey's 'Bonuses/Advanced Stats' addon with output formatting by dehoskins
 * http://www.wowroster.net/viewtopic.php?t=1506
 *
 * Modified by the roster dev team
 ****************************************************************************************************
 * @param $frontString
 * @param $value
 */

function dbl($frontString, $value)
{
    echo $frontString . ' : ' . $value . '<br>';
}

function getStartofModifier($aString)
{
    $startpos = mb_strlen($aString);

    //Count back till we get to the first number

    while ((false == is_numeric($aString[$startpos])) and (0 != $startpos)) {
        $startpos--;
    }

    //Get start position of the number

    while (is_numeric($aString[$startpos])) {
        $startpos -= 1;
    }

    return $startpos + 1;
}

function getLengthofModifier($aString)
{
    $startpos = getStartofModifier($aString);

    $length = 0;

    while (is_numeric($aString[$startpos + $length])) {
        //$startpos ++;

        $length++;
    }

    return $length;
}

function getModifier($aString)
{
    $startpos = getStartofModifier($aString);

    $modifier = '';

    // Extract the number

    while (is_numeric($aString[$startpos])) {
        $modifier .= $aString[$startpos];

        $startpos++;
    }

    return $modifier;
}

function getString($aString)
{
    return substr_replace($aString, 'XX', getStartofModifier($aString), getLengthofModifier($aString));
}

function getStartofModifierMana($aString)
{
    $startpos = 0;

    while ((false == is_numeric($aString[$startpos])) and ($startpos != mb_strlen($aString))) {
        $startpos++;
    }

    return $startpos;
}

function getLengthofModifierMana($aString)
{
    $startpos = getStartofModifierMana($aString);

    $length = 0;

    while (is_numeric($aString[$startpos + $length])) {
        $length++;
    }

    return $length;
}

function getModifierString($aString)
{
    return substr_replace($aString, 'XX', getStartofModifierMana($aString), getLengthofModifierMana($aString));
}

function getModifierMana($aString)
{
    return mb_substr($aString, getStartofModifierMana($aString), getLengthofModifierMana($aString));
}

function setBonus($modifier, $string, $item_name, $item_color)
{
    global $myBonus, $myTooltip;

    $full = '<span style="color:#' . $item_color . ';">' . $item_name . '</span> : ' . $modifier;

    if (array_key_exists($string, $myBonus)) {
        $myBonus[$string] += $modifier;

        $myTooltip[$string] .= '<br>' . $full;
    } else {
        $myBonus[$string] = $modifier;

        $myTooltip[$string] = $full;
    }
}

function hasNumeric($aString)
{
    $pos = 0;

    while (($pos <= mb_strlen($aString)) and (false == is_numeric($aString[$pos]))) {
        $pos++;
    }

    if ($pos < mb_strlen($aString)) {
        return true;
    }
  

    return false;
}

function sortOutTooltip($tooltip, $item_name, $item_color, $client_lang)
{
    global $wordings;

    $lines = explode(chr(10), $tooltip);

    foreach ($lines as $line) {
        if ((preg_match('^' . $wordings[$client_lang]['tooltip_equip'], $line)) and (false == hasNumeric($line))) {
            setBonus('', $line, $item_name, $item_color);
        } elseif (preg_match('^' . $wordings[$client_lang]['tooltip_equip_restores'], $line)) {
            setBonus(getModifierMana($line), getModifierString($line), $item_name, $item_color);
        } elseif (preg_match('^' . $wordings[$client_lang]['tooltip_equip_when'], $line)) {
            setBonus(getModifierMana($line), getModifierString($line), $item_name, $item_color);
        } elseif (preg_match('^' . $wordings[$client_lang]['tooltip_equip'], $line)) {
            setBonus(getModifier($line), getString($line), $item_name, $item_color);
        } elseif (preg_match('^' . $wordings[$client_lang]['tooltip_set'], $line)) {
            setBonus('', $line, $item_name, $item_color);
        } elseif (preg_match('^' . $wordings[$client_lang]['tooltip_spell_damage'], $line)) {
            setBonus(getModifier($line), $line, $item_name, $item_color);
        } elseif (preg_match('^' . $wordings[$client_lang]['tooltip_healing_power'], $line)) {
            setBonus(getModifier($line), $line, $item_name, $item_color);
        } elseif (preg_match('^' . $wordings[$client_lang]['tooltip_chance_hit'], $line)) {
            setBonus(getModifier($line), getModifierString($line), $item_name, $item_color);
        } elseif (preg_match('^' . $wordings[$client_lang]['tooltip_reinforced_armor'], $line)) {
            setBonus(getModifier($line), getModifierString($line), $item_name, $item_color);
        } elseif (preg_match('^' . $wordings[$client_lang]['tooltip_school_damage'], $line)) {
            setBonus(getModifier($line), getString($line), $item_name, $item_color);
        }
    }
}

function dumpString($aString)
{
    //$aString = str_replace( chr(10), 'TWAT', $aString);

    dbl('STRING', $aString);

    for ($i = 0, $iMax = mb_strlen($aString); $i < $iMax; $i++) {
        dbl($aString[$i], ord($aString[$i]));
    }
}

function dumpBonuses($char, $server)
{
    global $myBonus, $myTooltip, $wordings, $roster_conf, $wowdb;

    $server = $wowdb->escape($server);

    $qry = 'SELECT i.item_tooltip, i.item_name, i.item_color, p.parry, p.dodge, p.block, p.crit, p.mitigation, p.clientLocale
			FROM `' . ROSTER_ITEMSTABLE . '` i, `' . ROSTER_PLAYERSTABLE . "` p
			WHERE i.item_parent = 'equip'
				and i.member_id = p.member_id
				and p.name = '" . $char . "'
				and p.server = '" . $server . "'";

    $result = $wowdb->query($qry) or die_quietly($wowdb->error(), 'Database Error', basename(__FILE__), __LINE__, $qry);

    while ($row = $wowdb->fetch_array($result)) {
        sortOutTooltip($row['item_tooltip'], $row['item_name'], ((mb_strlen($row['item_color']) > 6) ? mb_substr($row['item_color'], 2, 6) : $row['item_color']), $row['clientLocale']);
    }

    $wowdb->data_seek($result, 0);

    $row = $wowdb->fetch_array($result);

    $bt .= border('sgray', 'start', $wordings[$roster_conf['roster_lang']]['itembonuses']) .
        '<table style="width:400px;" class="bodyline" cellspacing="0" cellpadding="0" border="0">' . "\n";

    $row = 0;

    foreach ($myBonus as $key => $value) {
        $bt .= '	<tr>
		<td class="membersRowRight' . (($row % 2) + 1) . '" style="white-space:normal;" ' . makeOverlib($myTooltip[$key], str_replace('XX', $value, $key), '', 2) . '>' .
        str_replace('XX', $value, $key) . '</td>
	</tr>';

        $row++;
    }

    $bt .= '</table>' . border('sgray', 'end');

    if (!empty($myBonus)) {
        return $bt;
    }
}
