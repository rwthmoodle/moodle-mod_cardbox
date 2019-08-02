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

class cardbox_review implements \renderable, \templatable {
    
    private $question = array('images' => array(), 'sounds' => array(), 'texts' => array());
    private $answer = array('images' => array(), 'sounds' => array(), 'texts' => array());
    private $cardid;
    
    /**
     * 
     * @param obj $context
     * @param obj $collection
     * @param int $cardid
     */
    public function __construct($context, $collection = null, $cardid = null) {
        
        require_once('model/cardcollection.class.php');
        require_once('locallib.php');

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

        $fs = get_file_storage();
        foreach ($contents as $content) {

            if ($content->contenttype == 1) { // XXX: make dynamic!

                $download_url = cardbox_get_download_url($context, $content->id, $content->content);    
                if ($content->cardside == 0) {
                    $this->question['images'][] = array('imagesrc' => $download_url);
                } else {
                    $this->answer['images'][] = array('imagesrc' => $download_url);
                }

            } else if ($content->contenttype == 3) { // audio files

                $download_url = cardbox_get_download_url($context, $content->id, $content->content);    
                if ($content->cardside == 0) {
                    $this->question['sounds'][] = array('soundsrc' => $download_url);
                } else {
                    $this->answer['sounds'][] = array('soundsrc' => $download_url);
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
    /**
     * 
     * @param \renderer_base $output
     * @return boolean
     */
    public function export_for_template(\renderer_base $output) {
        $data['cardid'] = $this->cardid;
        if (!empty($this->topic)) {
            $data['topic'] = $this->topic;
        } else {
            $data['topic'] = get_string('notopic', 'cardbox');
        }
        $data['question'] = $this->question;
        $data['answer'] = $this->answer;
        $data['cards'] = true;
        return $data;
    }
}
