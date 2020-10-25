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
 * $Id: xmlparse.php 341 2006-12-10 06:20:26Z zanix $
 *
 ******************************/

// XML parsing by Swipe
// http://wowroster.net

if (!defined('ROSTER_INSTALLED')) {
    exit('Detected invalid access to this file!');
}

class xmlParser
{
    public $xml_obj = null;

    public $output = [];

    public $message;

    public function __construct()
    {
        $this->xml_obj = xml_parser_create('');

        xml_set_object($this->xml_obj, $this);

        xml_set_character_dataHandler($this->xml_obj, 'dataHandler');

        xml_set_elementHandler($this->xml_obj, 'startHandler', 'endHandler');
    }

    public function parse($path)
    {
        if (function_exists('curl_init')) {
            $fp = curl_init($path);

            $ch = 1;
        } else {
            $fp = @fopen($path, 'rb');

            $ch = 0;
        }

        if (!$fp) {
            $this->message = "Cannot open XML data file: $path";

            return false;
        }

        if (!$ch) {
            while ($data = fread($fp, 4096)) {
                if (!xml_parse($this->xml_obj, $data, feof($fp))) {
                    $this->message = sprintf(
                        'XML error: %s at line %d',
                        xml_error_string(xml_get_error_code($this->xml_obj)),
                        xml_get_current_line_number($this->xml_obj)
                    );

                    xml_parser_free($this->xml_obj);

                    return false;
                }
            }
        } else {
            curl_setopt($fp, CURLOPT_HEADER, 0);

            curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1);

            $data = curl_exec($fp);

            if (curl_errno($fp)) {
                $ch_err = 1;
            } else {
                $ch_err = 0;
            }

            curl_close($fp);

            if (!xml_parse($this->xml_obj, $data, $ch_err)) {
                $this->message = sprintf(
                    'XML error: %s at line %d',
                    xml_error_string(xml_get_error_code($this->xml_obj)),
                    xml_get_current_line_number($this->xml_obj)
                );

                xml_parser_free($this->xml_obj);

                return false;
            }
        }

        return true;
    }

    public function startHandler($parser, $name, $attribs)
    {
        $_content = [ 'name' => $name ];

        if (!empty($attribs)) {
            $_content['attrs'] = $attribs;
        }

        $this->output[] = $_content;
    }

    public function dataHandler($parser, $data)
    {
        if (!empty($data)) {
            $_output_idx = count($this->output);

            $this->output[$_output_idx - 1]['content'] = $data;
        }
    }

    public function endHandler($parser, $name)
    {
        if (count($this->output) > 1) {
            $_data = array_pop($this->output);

            $_output_idx = count($this->output);

            $this->output[$_output_idx - 1]['child'][] = $_data;
        }
    }
}
