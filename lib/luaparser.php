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
 * $Id: luaparser.php 333 2006-12-04 09:43:30Z zanix $
 *
 ******************************/

if (!defined('ROSTER_INSTALLED')) {
    exit('Detected invalid access to this file!');
}

/**
 * Wrapper function so that you can parse a file instead of an array.
 * @param mixed      $file_name
 * @param null|mixed $file_type
 * @return false|mixed
 * @return false|mixed
 * @author six
 */
function ParseLuaFile($file_name, $file_type = null)
{
    if (file_exists($file_name) && is_readable($file_name)) {
        if ('gz' == $file_type) {
            $file_as_array = gzfile($file_name);
        } else {
            $file_as_array = file($file_name);
        }

        return(ParseLuaArray($file_as_array));
    }

    return(false);
}

/**
 * Main LUA parsing function
 * @param mixed $file_as_array
 * @return false|mixed
 * @return false|mixed
 * @author six, originally mordon
 */
function ParseLuaArray($file_as_array)
{
    if (!is_array($file_as_array)) {
        // return false if not presented with an array

        return(false);
    }  

    // Parse the contents of the array

    $stack = [ [ '',  [] ] ];

    $stack_pos = 0;

    $last_line = '';

    foreach ($file_as_array as $line) {
        // join lines ending in \\ together

        if ('\\' == mb_substr($line, -2, 1)) {
            $last_line .= mb_substr($line, 0, -2) . "\n";

            continue;
        }

        if ('' != $last_line) {
            $line = trim($last_line . $line);

            $last_line = '';
        } else {
            $line = trim($line);
        }

        $line = rtrim($line, ',');

        // Look for a key value pair

        if (mb_strpos($line, '=')) {
            [$name, $value] = explode('=', $line, 2);

            $name = trim($name);

            $value = trim($value);

            if ('[' == $name[0]) {
                $name = trim($name, '[]"');
            }

            if ('{' == $value) {
                $stack_pos++;

                $stack[$stack_pos] = [$name, []];
            } else {
                if ('"' == $value[0]) {
                    $value = mb_substr($value, 1, -1);
                } elseif ('true' == $value) {
                    $value = true;
                } elseif ('false' == $value) {
                    $value = false;
                } elseif ('nil' == $value) {
                    $value = null;
                }

                $stack[$stack_pos][1][$name] = $value;
            }
        } elseif ('}' == $line) {
            $hash = $stack[$stack_pos];

            $stack_pos--;

            $stack[$stack_pos][1][$hash[0]] = $hash[1];
        }
    }

    return($stack[0][1]);
}
