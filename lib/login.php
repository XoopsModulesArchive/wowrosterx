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
 * $Id: login.php 341 2006-12-10 06:20:26Z zanix $
 *
 ******************************/
require_once('../../../mainfile.php');
global $xoopsOption,$xoopsUser,$xoopsTpl,$xoopsDB;

if (!defined('ROSTER_INSTALLED')) {
    exit('Detected invalid access to this file!');
}

class RosterLogin
{
    public $allow_login;

    public $message;

    public $script_filename;

    /**
     * Constructor for Roster Login class
     * Accepts an action for the form
     * And an array of additional fields
     *
     * @param string $script_filename
     */

    public function __construct($script_filename)
    {
        $this->script_filename = $script_filename;

        $this->allow_login = false;

        $this->checkLogin();
    }

    public function checkLogin()
    {
        global $xoopsUser;

        $this->allow_login = $xoopsUser->getVar(uname);
    }

    public function getAuthorized()
    {
        return $this->allow_login;
    }

    public function getMessage()
    {
        return '';
    }

    public function getLoginForm()
    {
        return '';
    }
}
