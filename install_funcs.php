<?php
/* install_funcs.inc 2006-11-04 10:56:12
   WoW Roster X - WoW Roster for Xoops - A XOOPS CMS Module
   Copyright (c) 2006 Mike DeShane, US
   URL:     www.pkcomp.net
   Contact: mdeshane@pkcomp.net

   XOOPS
   Copyright (c) 2000 XOOPS.org
   URL:     https://www.xoops.org/

   This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

 */

//The next comment block is for PHPDocumentor
/**
 * WoW Roster X - WoW Roster for Xoops
 *
 * Module installation helper functions
 *
 * @author Mike DeShane (mdeshane@pkcomp.net)
 * @copyright 2006 Mike DeShane, US
 *
 * @param mixed $module
 * @param mixed $oldVersion
 */

/**
 * Module update function
 *
 * @global xoopsDB xoopsDatabase object
 * @param xoopsModule &$module Handle to current module
 * @param int $oldVersion version of module prior to update
 * @return bool True if success else False
 */
function xoops_module_update_wrx(&$module, $oldVersion)
{
    return true;
}//end function

/**
 * Module install function
 *
 * @param xoopModule &$module Handle to current module
 * @return bool True if success else False
 */
function xoops_module_install_wrx(&$module)
{
    //The basic SQL install is done via the SQL script

    return true;
}//end function

/**
 * Module uninstall function
 *
 * @param xoopModule &$module Handle to current module
 * @return bool True if success else False
 */
function xoops_module_uninstall_wrx(&$module)
{
    return true;
}//end function

/*
 * This file was generated by XBS ModGen, (c) 2006 A Kitson, UK. See http://xoobs.net
 * ModGen is a Module Code Generator for the Xoops CMS.  See http://xoops.org
 */
