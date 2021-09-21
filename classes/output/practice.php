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

    private $topic;
    private $question = array('images' => array(), 'sounds' => array(), 'texts' => array());
    private $answer = array('images' => array(), 'sounds' => array(), 'texts' => array());
    private $case;
    private $case1 = false; // question_selfcheck.
    private $case2 = false; // question_autocheck.
    private $case3 = false; // answer_selfcheck.
    private $case4 = false; // answer_autocheck.
    private $case5 = false; // suggest_answer.
    private $inputfields = array();
    private $questioncontext = null;
    private $answercontext = null;
    private $necessaryanswers = 0;
    private $casesensitive = 0;
    private $answercount = 0;
    private $cardsleft;

    /**
     * Function builds the view of a flashcard during practice.
     *
     * @global type $CFG
     * @param type $context
     * @param obj $cardbox
     */
    public function __construct($case, $context, $cardid, $cardsleft) {

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
            case 5:
                $this->case5 = true;
                $this->case = 5;
                break;
            default:
                // TODO Error handling.
        }
        $this->cardsleft = $cardsleft;
        $this->cardbox_getcarddeck($cardid);
        $this->cardbox_prepare_cardcontents($context, $cardid);

    }
    
    public function cardbox_prepare_cardcontents($context, $cardid) {
        
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');
        require_once('model/cardbox.class.php');

        $contents = cardbox_cardboxmodel::cardbox_get_card_contents($cardid);

        $topic = cardbox_get_topic($cardid);
        if ($topic === 0 || $topic == "NULL") {
            $this->topic = "";
        } else {
            $this->topic = strtoupper($DB->get_field('cardbox_topics', 'topicname', array('id' => $topic)));
        }

        $this->casesensitive = cardbox_cardboxmodel::cardbox_get_casesensitive($cardid);

        $fs = get_file_storage();
        $solutioncount = 0;
        foreach ($contents as $content) {

            if ($content->area == CARD_CONTEXT_INFORMATION && $content->cardside == CARDBOX_CARDSIDE_QUESTION) { //check if there is context for the question

                $this->questioncontext = strip_tags($content->content);

            } else if ($content->area == CARD_CONTEXT_INFORMATION && $content->cardside == CARDBOX_CARDSIDE_ANSWER) { //check if there is context for the answer

                $this->answercontext = strip_tags($content->content);

            } else if ($content->contenttype == CARDBOX_CONTENTTYPE_IMAGE) { // images

                $download_url = cardbox_get_download_url($context, $content->id, $content->content);    
                if ($content->cardside == CARDBOX_CARDSIDE_QUESTION) {
                    if ($content->area == CARD_IMAGEDESCRIPTION_INFORMATION) {
                        $this->question['images'][0] += array('imagealt' => $content->content);
                        continue;
                    }
                    $this->question['images'][] = array('imagesrc' => $download_url);
                } else {
                    $this->answer['images'][] = array('imagesrc' => $download_url);
                }

            } else if ($content->contenttype == CARDBOX_CONTENTTYPE_AUDIO) { // audio files

                $download_url = cardbox_get_download_url($context, $content->id, $content->content);    
                if ($content->cardside == CARDBOX_CARDSIDE_QUESTION) {
                    $this->question['sounds'][] = array('soundsrc' => $download_url);
                } else {
                    $this->answer['sounds'][] = array('soundsrc' => $download_url);
                }

            } else if ($content->cardside == CARDBOX_CARDSIDE_QUESTION) {

                $content->content = $content->content; // cardbox_format_string($content->content);

                $this->question['texts'][] = array('text' => strip_tags($content->content), 'puretext' => strip_tags($content->content));

            } else {

                $content->content = $content->content; // cardbox_format_string($content->content);

                if ($content->area === "3") {
                    continue;
                }
                $this->answer['texts'][] = array('text' => strip_tags($content->content), 'puretext' => strip_tags($content->content));
                $solutioncount++;
                $this->inputfields[] = array('number' => $solutioncount);
            }
        }
        $this->answercount = $solutioncount;
        $this->necessaryanswers = $DB->get_field('cardbox_cards', 'necessaryanswers', array('id' => $cardid), IGNORE_MISSING);
        if ($this->necessaryanswers != 0) {
            $this->inputfields = ['number' => '1'];
        }
    }
    public function cardbox_getcarddeck(int $cardid) {
        global $CFG, $DB, $USER;
        if ($DB->record_exists('cardbox_progress', ['userid' => $USER->id, 'card' => $cardid])) {
            $this->deck = $DB->get_field('cardbox_progress', 'cardposition', ['userid' => $USER->id, 'card' => $cardid], IGNORE_MISSING);
            if ($this->deck == 0){
                $this->deckimgurl = $CFG->wwwroot . '/mod/cardbox/pix/new.svg';
            } else if ($this->deck == 6) {
                $this->deckdeckimgurlimg = $CFG->wwwroot . '/mod/cardbox/pix/mastered.svg';
            } else {
                $this->deckimgurl = $CFG->wwwroot . '/mod/cardbox/pix/'.$this->deck.'.svg';
            }
        } else {
            $this->deck = null;
            $this->deckimgurl = $CFG->wwwroot . '/mod/cardbox/pix/new.svg';
        }

    }
    public function export_for_template(\renderer_base $output) {

        $data = array();
        $data['topic'] = $this->topic;
        $data['question'] = $this->question;
        $data['answer'] = $this->answer;
        $data['case1'] = $this->case1;
        $data['case2'] = $this->case2;
        $data['case3'] = $this->case3;
        $data['case4'] = $this->case4;
        $data['case5'] = $this->case5;
        $data['inputfields'] = $this->inputfields;
        $data['contextquestion'] = $this->questioncontext;
        $data['contextanswer'] = $this->answercontext;
        $data['necessaryanswers'] = $this->necessaryanswers;
        $data['casesensitive'] = $this->casesensitive;
        $data['contextquestionavailable'] = $this->questioncontext != null;
        $data['contextansweravailable'] = $this->answercontext != null;
        $data['icon'] = "";
        $data['morethanonesolution'] = ($this->answercount > 1);
        $data['cardsleft'] = $this->cardsleft;
        $data['deck'] = $this->deck;
        $data['deckimgurl'] = $this->deckimgurl;
        return $data;

    }

}
