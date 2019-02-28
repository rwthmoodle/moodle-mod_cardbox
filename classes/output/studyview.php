<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @authors   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class cardbox_studyview implements \renderable, \templatable {

    private $frontimages;
    private $fronttexts;
    private $backimages;
    private $backimagesrc;
    private $backtexts;
    private $backtext;
    
    public function __construct($imgurls = null, $texts = null) {
        
        if (!empty($imgurls)) {
            $this->frontimages = array();
            foreach ($imgurls as $imgurl) {
                $this->frontimages[] = array("frontimagesrc" => $imgurl);
            }
        }
        if (!empty($texts)) {
            $this->fronttexts = array();
            foreach ($texts as $text) {
                $this->fronttexts[] = array("fronttext" => $text);
            }
        }
        
        
        
    }
    
    public function export_for_template(\renderer_base $output) {
        
        $data = array();
        $data['frontimages'] = $this->frontimages;
        $data['fronttexts'] = $this->fronttexts;
        
        return $data;
        
        
    }
    
}