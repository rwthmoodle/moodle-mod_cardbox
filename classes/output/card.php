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
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class cardbox_card implements \renderable, \templatable {

    private $cmid;
    private $cardid;
    private $topic;
    private $question = array('images' => array(), 'texts' => array());
    private $answer = array('images' => array(), 'texts' => array());
    private $allowedtoedit = false;

    public function __construct($cardid, $context, $cmid, $allowedtoedit) {
        
        require_once('model/cardcollection.class.php');
        require_once('locallib.php');
        
        $this->cmid = $cmid;
        $this->cardid = $cardid;
        
        if ($allowedtoedit) {
            $this->allowedtoedit = true;
        }
        
        $contents = cardbox_cardcollection::cardbox_get_cardcontents($cardid);

        $this->topic = cardbox_cardcollection::cardbox_get_topic($cardid);
        
        if (empty($this->topic)) {
            $this->topic = get_string('notopic', 'cardbox');
        }
        
        $fs = get_file_storage();
        foreach ($contents as $content) {

            if ($content->contenttype == 1) { // XXX: make dynamic!

                $download_url = cardbox_get_download_url($context, $content->id, $content->content);    
                if ($content->cardside == 0) {
                    $this->question['images'][] = array('imagesrc' => $download_url);
                } else {
                    $this->answer['images'][] = array('imagesrc' => $download_url);
                }

            } else if ($content->cardside == 0) {
                
                $content->content = format_text($content->content);
                $this->question['texts'][] = array('text' => $content->content);

            } else {
                
                $content->content = format_text($content->content);
                $this->answer['texts'][] = array('text' => $content->content);
            }
        }
        
    }
    
    public function export_for_template(\renderer_base $output) {
        
        $data = array();
        $data['cmid'] = $this->cmid;
        $data['cardid'] = $this->cardid;
        $data['topic'] = $this->topic;
        $data['question'] = $this->question;
        $data['answer'] = $this->answer;
        $data['allowedtoedit'] = $this->allowedtoedit;
        //$data['cards'] = true;
        return $data;
        
    }
}