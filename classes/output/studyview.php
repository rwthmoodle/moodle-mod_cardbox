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

class cardbox_studyview implements \renderable, \templatable {

    private $frontimages;
    private $fronttexts;
    private $backimages;
    private $backtexts;
    private $topics;
    private $selfcheck;
    private $autocheck;
    
    /**
     * Function builds the view of a flashcard during practice.
     *
     * @global type $CFG
     * @param type $context
     * @param obj $cardbox
     */
    public function __construct($context, $cardbox = null, $cardid = null, $correction = 0) {

        $this->cardbox_prepare_cardcontents($context, $cardbox, $cardid);
        
        $this->cardbox_prepare_topics_to_study();
        
        $this->cardbox_prepare_user_form($correction);

    }
    
    public function cardbox_prepare_cardcontents($context, $cardbox, $cardid) {
        
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');
        require_once('model/cardbox.class.php');

        if (!empty($cardbox)) {
            $card = $cardbox->cardbox_get_first_card();
            
        } else {
            $card = cardbox_cardboxmodel::cardbox_get_card($cardid);

        }
        $contents = cardbox_cardboxmodel::cardbox_get_card_contents($card->card);
        $topic = $card->topicname;

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
                $this->fronttexts[] = array("fronttext" => $content->content);

            } else {
                $this->backtexts[] = array("backtext" => $content->content);
            }
        }
//        if (!empty($this->frontimages) && empty($this->backimages)) {
//            $this->backimages[] = $this->frontimages[0];
//        }
 
    }
    /**
     * Function includes the list of topics in the practice options modal.
     * The user can then choose to prioritise one of the topics in the
     * selection of cards for a practice session.
     *
     * @global type $CFG
     */
    public function cardbox_prepare_topics_to_study() {
        
        global $CFG;
        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');

        $this->topics = array();

        $topiclist = cardbox_get_topics();

        foreach ($topiclist as $key => $value) {
            $this->topics[] = array('value' => $key, 'label' => $value);
        }
    }
    /**
     * Function determines which constellation of buttons and input fields
     * (which partial template) to include. It depends on the user's choice
     * of correction mode (selfcheck or automatic check).
     *
     * @param int $correction
     */
    public function cardbox_prepare_user_form($correction) {
        
        if ($correction == 0) {
            $this->selfcheck = true;
            $this->autocheck = false;
        } else {
            $this->selfcheck = false;
            $this->autocheck = true;
        }
    }

    public function export_for_template(\renderer_base $output) {

        global $OUTPUT;

        $data = array();
        $data['frontimages'] = $this->frontimages;
        $data['fronttexts'] = $this->fronttexts;
        $data['backimages'] = $this->backimages;
        $data['backtexts'] = $this->backtexts;
        $data['topics'] = $this->topics;
        $data['helpbuttoncorrectionmode'] = $OUTPUT->help_icon('choosecorrectionmode', 'cardbox');
        $data['helpbuttontopic'] = $OUTPUT->help_icon('weightopic', 'cardbox');
        $data['selfcheck'] = $this->selfcheck;
        $data['autocheck'] = $this->autocheck;

        return $data;

    }
    
}



//class cardbox_studyview implements \renderable, \templatable {
//
//    private $frontimages;
//    private $fronttexts;
//    private $backimages;
//    private $backimagesrc;
//    private $backtexts;
//    private $backtext;
//    
//    public function __construct($imgurls = null, $texts = null) {
//        
//        if (!empty($imgurls)) {
//            $this->frontimages = array();
//            foreach ($imgurls as $imgurl) {
//                $this->frontimages[] = array("frontimagesrc" => $imgurl);
//            }
//        }
//        if (!empty($texts)) {
//            $this->fronttexts = array();
//            foreach ($texts as $text) {
//                $this->fronttexts[] = array("fronttext" => $text);
//            }
//        }
//
//    }
//    
//    public function export_for_template(\renderer_base $output) {
//        
//        $data = array();
//        $data['frontimages'] = $this->frontimages;
//        $data['fronttexts'] = $this->fronttexts;
//        
//        return $data;
//        
//        
//    }
//    
//}