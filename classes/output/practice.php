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

class cardbox_practice implements \renderable, \templatable {

    private $question = array('images' => array(), 'texts' => array());
    private $answer = array('images' => array(), 'texts' => array());
    private $case;
    private $case1 = false; // question_selfcheck.
    private $case2 = false; // question_autocheck.
    private $case3 = false; // answer_selfcheck.
    private $case4 = false; // answer_autocheck.
    private $topics;
    private $inputfields = array();

    /**
     * Function builds the view of a flashcard during practice.
     *
     * @global type $CFG
     * @param type $context
     * @param obj $cardbox
     */
    public function __construct($case, $context, $cardbox = null, $cardid = null, $correction = 0) {

        switch ($case) {
            case 1:
                $this->case1 = true;
                $this->case = 1;
                break;
            case 2:
                $this->case2 = true;
                $this->case = 2;
                break;
            case 3:
                $this->case3 = true;
                $this->case = 3;
                break;
            case 4:
                $this->case4 = true;
                $this->case = 4;
                break;
            default:
                // TODO Error handling.
        }
        
        $this->cardbox_prepare_cardcontents($context, $cardbox, $cardid);
        
        $this->cardbox_prepare_topics_to_study();
        
//        $this->cardbox_prepare_user_form($correction);

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

        $fs = get_file_storage();
        $solutioncount = 0;
        foreach ($contents as $content) {

            if ($content->contenttype == 1) { // XXX: make dynamic!

                $download_url = cardbox_get_download_url($context, $content->id, $content->content);    
                if ($content->cardside == 0) {
                    $this->question['images'][] = array('imagesrc' => $download_url);
                } else {
                    $this->answer['images'][] = array('imagesrc' => $download_url);
                }

            } else if ($content->cardside == 0) {
                $this->question['texts'][] = array('text' => $content->content);

            } else {
                $this->answer['texts'][] = array('text' => $content->content);
                $solutioncount++;
                $this->inputfields[] = array('number' => $solutioncount);
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
//    public function cardbox_prepare_user_form($correction) {
//        
//        if ($correction == 0) {
//            $this->selfcheck = true;
//            $this->autocheck = false;
//        } else {
//            $this->selfcheck = false;
//            $this->autocheck = true;
//        }
//    }

    public function export_for_template(\renderer_base $output) {

        global $OUTPUT;

        $data = array();
        $data['question'] = $this->question;
        $data['answer'] = $this->answer;
        $data['case1'] = $this->case1;
        $data['case2'] = $this->case2;
        $data['case3'] = $this->case3;
        $data['case4'] = $this->case4;
        $data['topics'] = $this->topics;
        $data['inputfields'] = $this->inputfields;
        $data['helpbuttoncorrectionmode'] = $OUTPUT->help_icon('choosecorrectionmode', 'cardbox');
        $data['helpbuttontopic'] = $OUTPUT->help_icon('weightopic', 'cardbox');
//        $data['selfcheck'] = $this->selfcheck;
//        $data['autocheck'] = $this->autocheck;

        return $data;

    }
    
}