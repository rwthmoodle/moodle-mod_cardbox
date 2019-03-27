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
 * @author   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class cardbox_review implements \renderable, \templatable {
    
    private $frontimages;
    private $fronttexts;
    private $backimages;
    private $backtexts;
    private $cardid;
    
    /**
     * 
     * @param obj $context
     * @param obj $collection
     * @param int $cardid
     */
    public function __construct($context, $collection = null, $cardid = null) {
        
        require_once('model/cardcollection.class.php');
        
        if (!empty($collection)) {
            $contents = $collection->cardbox_get_cardcontents_initial();
            $this->cardid = $collection->cardbox_get_first_cardid();

        } else if (!empty($cardid)) {
            $contents = cardbox_cardcollection::cardbox_get_cardcontents($cardid);
            $this->cardid = $cardid;

        } else {
            // TODO: Fehlerbehandlung
        }
        $this->topic = cardbox_cardcollection::cardbox_get_topic($this->cardid);

        $this->frontimages = array();
        $this->fronttexts = array();
        $this->backimages = array();
        $this->backtexts = array();

        $fs = get_file_storage();
        foreach ($contents as $content) {

            if ($content->contenttype == 1) { // XXX: make dynamic!

                $download_url = cardbox_get_download_url($context, $content->id, $content->content);    
                if ($content->cardside == 0) {
                    $this->frontimages[] = array("frontimagesrc" => $download_url);
                } else {
                    $this->backimages[] = array("backimagesrc" => $download_url);
                }

            } else if ($content->cardside == 0) {
                $this->fronttexts[] = array("fronttext" => format_text($content->content));

            } else {
                $this->backtexts[] = array("backtext" => format_text($content->content));
            }
        }

    }

    public function export_for_template(\renderer_base $output) {
        $data['cardid'] = $this->cardid;
        $data['topic'] = $this->topic;
        $data['frontimages'] = $this->frontimages;
        $data['fronttexts'] = $this->fronttexts;
        $data['backimages'] = $this->backimages;
        $data['backtexts'] = $this->backtexts;
        $data['cards'] = true;
        return $data;
    }
}