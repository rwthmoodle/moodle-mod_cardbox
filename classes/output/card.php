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
define ('MASTERED_POSITION', 6);
class cardbox_card implements \renderable, \templatable {

    private $cmid;
    private $cardid;
    private $topic;
    private $question = array('images' => array(), 'texts' => array());
    private $answer = array('images' => array(), 'texts' => array());
    private $multipleanswers = false;
    private $allowedtoedit = false;
    private $questioncontext = null;
    private $answercontext = null;
    private $seestatus = false;
    private $status;
    private $howmanyanswersnecessary;
    private $decktext;
    private $repsnummer;
    private $acimgurl;
    public function __construct($cardid, $context, $cmid, $allowedtoedit, $seestatus) {

        require_once('model/cardcollection.class.php');
        require_once('locallib.php');

        global $CFG, $USER;
        $this->cmid = $cmid;
        $this->cardid = $cardid;
        $answercount = 0;

        if ($allowedtoedit) {
            $this->allowedtoedit = true;
        }

        if ($seestatus) {
            $this->seestatus = true;
        }

        $this->status = cardbox_get_status($cardid, $USER->id);

        $contents = cardbox_cardcollection::cardbox_get_cardcontents($cardid);

        $this->topic = cardbox_cardcollection::cardbox_get_topic($cardid);

        $necessaryanswers = cardbox_get_necessaryanswers($cardid);

        if ($necessaryanswers === "1") {
            $this->allansnecessary = false;
            $this->howmanyanswersnecessary = get_string("oneanswersnecessary", "cardbox");
        } else {
            $this->allansnecessary = true;
            $this->howmanyanswersnecessary = get_string("allanswersnecessary", "cardbox");
        }

        $this->cardbox_getcarddeck($cardid, $allowedtoedit);
        $this->getcardreps_ifmastered($cardid, $allowedtoedit);

        if (empty($this->topic)) {
            $this->topic = get_string('notopic', 'cardbox');
        }

        $fs = get_file_storage();
        foreach ($contents as $content) {

            if ($content->area == CARD_CONTEXT_INFORMATION && $content->cardside == CARDBOX_CARDSIDE_QUESTION) {
                // Check if there is context for the question.

                $this->questioncontext = format_text($content->content);

            } else if ($content->area == CARD_CONTEXT_INFORMATION && $content->cardside == CARDBOX_CARDSIDE_ANSWER) {
                // Check if there is context for the answer.

                $this->answercontext = format_text($content->content);

            } else if ($content->contenttype == CARDBOX_CONTENTTYPE_IMAGE) {

                $downloadurl = cardbox_get_download_url($context, $content->id, $content->content);
                if ($content->cardside == CARDBOX_CARDSIDE_QUESTION) {
                    if ($content->area == CARD_IMAGEDESCRIPTION_INFORMATION) {
                        $this->question['images'][0] += array('imagealt' => $content->content);
                        continue;
                    }
                    $this->question['images'][] = array('imagesrc' => $downloadurl);
                } else {
                    $this->answer['images'][] = array('imagesrc' => $downloadurl);
                    $answercount++;
                }

            } else if ($content->cardside == CARDBOX_CARDSIDE_QUESTION && $content->contenttype == CARDBOX_CONTENTTYPE_AUDIO) {

                $downloadurl = cardbox_get_download_url($context, $content->id, $content->content);
                $this->question['sounds'][] = array('soundsrc' => $downloadurl);

            } else if ($content->cardside == CARDBOX_CARDSIDE_QUESTION) {

                $content->content = format_text($content->content);
                $this->question['texts'][] = array('text' => $content->content);

            } else {

                $content->content = format_text($content->content);
                $this->answer['texts'][] = array('text' => $content->content);
                $answercount++;
            }
        }
        if ($answercount > 1) {
            $this->multipleanswers = true;
        }

    }
    public function getcardreps_ifmastered(int $cardid, bool $allowedtoedit) {
        global $DB, $USER;
        $showreps = "";
        if ($allowedtoedit) {
            $cardrepssum = $DB->get_records_sql(
                            'SELECT SUM(repetitions) as repssum
                            FROM {cardbox_progress} where
                            cardposition = :cardposition and
                            card = :cardid',
                            ['cardid' => $cardid, 'cardposition' => MASTERED_POSITION]);
            $usercount = $DB->count_records_sql(
                'SELECT count(distinct userid)
                            FROM {cardbox_progress} where
                            cardposition = :cardposition and
                            card = :cardid',
                            ['cardposition' => MASTERED_POSITION, 'cardid' => $cardid]
            );
            foreach ($cardrepssum as $record) {
                if (!empty($usercount)) {
                    $showreps = $record->repssum / $usercount;
                }
            }
        } else {
            $cardrepssum = $DB->get_records_sql(
                'SELECT SUM(repetitions) as repssum
                FROM {cardbox_progress} where
                cardposition = :cardposition and
                card = :cardid and userid = :userid group by card',
                ['cardid' => $cardid, 'cardposition' => MASTERED_POSITION, 'userid' => $USER->id]);
            foreach ($cardrepssum as $record) {
                $showreps = $record->repssum;
            }
        }
        if ($showreps != "") {
            $this->reps = true;
            $this->repsnummer = round($showreps);
        } else {
            $this->reps = false;
        }
    }
    public function cardbox_getcarddeck(int $cardid, bool $allowedtoedit) {
        global $CFG, $DB, $USER;
        $acval = $DB->get_field('cardbox_cards', 'disableautocorrect', ['id' => $cardid]);
        if ($acval == 1) {
            $this->disableautocorrect = true;
            $this->acimgurl = get_string("autocorrecticon", "cardbox");
        } else {
            $this->disableautocorrect = false;
        }

        if ($allowedtoedit) {
            $decktostudentcount = $DB->get_records_sql(
                'SELECT cardposition, count(userid) as users FROM {cardbox_progress}
                    where card = :cardid
                        group by cardposition',
                            ['cardid' => $cardid]);
            $totalstudent = 0;
            $weightedsum = 0;
            foreach ($decktostudentcount as $carddecktostudent) {
                $totalstudent += $carddecktostudent->users;
                $weightedsum += ($carddecktostudent->cardposition + 1) * $carddecktostudent->users;
            }
            if ($totalstudent != 0) {
                $this->deck = round($weightedsum / $totalstudent);
            } else {
                $this->deck = 1;
            }
        } else if ($DB->record_exists('cardbox_progress', ['userid' => $USER->id, 'card' => $cardid])) {
            $this->deck = $DB->get_field('cardbox_progress', 'cardposition',
                                         ['userid' => $USER->id, 'card' => $cardid], IGNORE_MISSING);
        } else {
            $this->deck = null;
        }

        if ($allowedtoedit) {
            if ($this->deck == 1 || $this->deck == null ) {
                $this->decktext = ucfirst(get_string('new', 'cardbox'));
                $this->deckimgurl = $CFG->wwwroot . '/mod/cardbox/pix/new.svg';
            } else if ($this->deck == 7) {
                $this->decktext = ucfirst(get_string('known', 'cardbox'));
                $this->deckimgurl = $CFG->wwwroot . '/mod/cardbox/pix/mastered.svg';
            } else {
                $deck = $this->deck - 1;
                $this->decktext = $deck;
                $this->deckimgurl = $CFG->wwwroot . '/mod/cardbox/pix/'.$deck.'.svg';
            }
        } else {
            if ($this->deck == 0 || $this->deck == null) {
                $this->decktext = $this->decktext = ucfirst(get_string('new', 'cardbox'));
                $this->deckimgurl = $CFG->wwwroot . '/mod/cardbox/pix/new.svg';
            } else if ($this->deck == 6) {
                $this->decktext = ucfirst(get_string('known', 'cardbox'));
                $this->deckimgurl = $CFG->wwwroot . '/mod/cardbox/pix/mastered.svg';
            } else {
                $this->decktext = $this->deck;
                $this->deckimgurl = $CFG->wwwroot . '/mod/cardbox/pix/'.$this->deck.'.svg';
            }
        }

    }

    public function cardbox_getcarddecknumber() {
        return $this->deck;
    }
    
    public function export_for_template(\renderer_base $output) {

        global $OUTPUT;

        $data = array();
        $data['cmid'] = $this->cmid;
        $data['cardid'] = $this->cardid;
        $data['topic'] = strtoupper($this->topic);
        $data['question'] = $this->question;
        $data['answer'] = $this->answer;
        $data['multipleanswers'] = $this->multipleanswers;
        $data['answercontext'] = $this->answercontext;
        $data['questioncontext'] = $this->questioncontext;
        $data['contextquestionavailable'] = $this->questioncontext != null;
        $data['contextansweravailable'] = $this->answercontext != null;
        $data['allowedtoedit'] = $this->allowedtoedit;
        $data['seestatus'] = $this->seestatus;
        $data['status'] = $this->status;
        $data['helpicon'] = $OUTPUT->help_icon('cardposition', 'cardbox');
        $data['deck'] = $this->deck;
        $data['deckimgurl'] = $this->deckimgurl;
        $data['howmanyanswersnecessary'] = $this->howmanyanswersnecessary;
        $data['reps'] = $this->reps;
        $data['repsnummer'] = $this->repsnummer;
        $data['decktext'] = $this->decktext;
        $data['acimgurl'] = $this->acimgurl;
        $data['disableautocorrect'] = $this->disableautocorrect;
        $data['allansnecessary'] = $this->allansnecessary;
        return $data;

    }
}
