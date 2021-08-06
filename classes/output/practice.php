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

class cardbox_practice implements \renderable, \templatable {

    private $question = array('images' => array(), 'sounds' => array(), 'texts' => array());
    private $answer = array('images' => array(), 'sounds' => array(), 'texts' => array());
    private $case;
    private $case1 = false; // question_selfcheck.
    private $case2 = false; // question_autocheck.
    private $case3 = false; // answer_selfcheck.
    private $case4 = false; // answer_autocheck.
    private $inputfields = array();
    private $questioncontext = null;
    private $answercontext = null;
    private $necessaryanswers = 0;
    private $casesensitive = 0;

    /**
     * Function builds the view of a flashcard during practice.
     *
     * @global type $CFG
     * @param type $context
     * @param obj $cardbox
     */
    public function __construct($case, $context, $cardid) {

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

        $this->cardbox_prepare_cardcontents($context, $cardid);

    }
    
    public function cardbox_prepare_cardcontents($context, $cardid) {
        
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');
        require_once('model/cardbox.class.php');

        $table = 'cardbox_contenttypes';
        $image = $DB->get_field($table, 'id', array('name' => 'image'), MUST_EXIST);
        $sound = $DB->get_field($table, 'id', array('name' => 'audio'), MUST_EXIST);
        
        $contents = cardbox_cardboxmodel::cardbox_get_card_contents($cardid);

        $this->casesensitive = cardbox_cardboxmodel::cardbox_get_casesensitive($cardid);

        $fs = get_file_storage();
        $solutioncount = 0;
        foreach ($contents as $content) {

            if ($content->area == 1 && $content->cardside == 0) { //check if there is context for the question

                $this->questioncontext = $content->content;

            } else if ($content->area == 1 && $content->cardside == 1) { //check if there is context for the answer
                
                $this->answercontext = $content->content;

            } else if ($content->contenttype == $image) { // images

                $download_url = cardbox_get_download_url($context, $content->id, $content->content);    
                if ($content->cardside == 0) {
                    if ($content->area == 2) {
                        $this->question['images'][0] += array('imagealt' => $content->content);
                        continue;
                    }
                    $this->question['images'][] = array('imagesrc' => $download_url);
                } else {
                    $this->answer['images'][] = array('imagesrc' => $download_url);
                }

            } else if ($content->contenttype == $sound) { // audio files

                $download_url = cardbox_get_download_url($context, $content->id, $content->content);    
                if ($content->cardside == 0) {
                    $this->question['sounds'][] = array('soundsrc' => $download_url);
                } else {
                    $this->answer['sounds'][] = array('soundsrc' => $download_url);
                }

            } else if ($content->cardside == 0) {

                $content->content = $content->content; // cardbox_format_string($content->content);

                $this->question['texts'][] = array('text' => $content->content, 'puretext' => strip_tags($content->content));

            } else {

                $content->content = $content->content; // cardbox_format_string($content->content);

                $this->answer['texts'][] = array('text' => $content->content, 'puretext' => strip_tags($content->content));
                $solutioncount++;
                $this->inputfields[] = array('number' => $solutioncount);
            }
        }
        $this->necessaryanswers = $DB->get_field('cardbox_cards', 'necessaryanswers', array('id' => $cardid), IGNORE_MISSING);
        if ($this->necessaryanswers != 0) {
            $this->inputfields = ['number' => '1'];
        }
    }

    public function export_for_template(\renderer_base $output) {

        $data = array();
        $data['question'] = $this->question;
        $data['answer'] = $this->answer;
        $data['case1'] = $this->case1;
        $data['case2'] = $this->case2;
        $data['case3'] = $this->case3;
        $data['case4'] = $this->case4;
        $data['inputfields'] = $this->inputfields;
        $data['contextquestion'] = $this->questioncontext;
        $data['contextanswer'] = $this->answercontext;
        $data['necessaryanswers'] = $this->necessaryanswers;
        $data['casesensitive'] = $this->casesensitive;
        $data['contextquestionavailable'] = $this->questioncontext != null;
        $data['contextansweravailable'] = $this->answercontext != null;

        return $data;

    }

}
