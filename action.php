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
 * In this file, incoming AJAX request from  practice.js are handled.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/cardbox/locallib.php');

$cmid = required_param('id', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($cmid, 'cardbox');
$cardbox = $DB->get_record('cardbox', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
require_sesskey();

$context = context_module::instance($cmid);

$action = required_param('action', PARAM_ALPHA); // ...'$action' determines what is to be done; see below.


/* * ********************** move card to the next box and return next card *********************** */

if ($action === 'updateandnext') {

    require_once($CFG->dirroot . '/mod/cardbox/locallib.php');
    require_once($CFG->dirroot . '/mod/cardbox/classes/output/practice.php');

    $cardid = required_param('cardid', PARAM_INT);
    $iscorrect = required_param('iscorrect', PARAM_INT);
    $next = required_param('next', PARAM_INT);
    $isrepetition = required_param('isrepetition', PARAM_INT);
    $case = optional_param('case', 1, PARAM_INT);
    $cardsleft = required_param('cardsleft', PARAM_INT);
    $correction = required_param('mode', PARAM_INT);

    $dataobject = $DB->get_record('cardbox_progress', array('userid' => $USER->id, 'card' => $cardid), $fields = '*', MUST_EXIST);
    if (empty($dataobject)) {
        echo json_encode(['status' => 'error', 'reason' => 'nocardboxentryfound']);
    }
    $lastposition = $dataobject->cardposition;

    $cardisdue = cardbox_is_card_due($dataobject);

    // 1. Update the card entry in the DB if
    // a) this is the first time the card was answered in this session and
    // b) the card is (over)due and/or was answered incorrectly.
    if ($isrepetition == 0 && ($cardisdue == true || $iscorrect == 0) ) {

        $success = cardbox_update_card_progress($dataobject, $iscorrect);

        if (empty($success)) {
            echo json_encode(['status' => 'error', 'reason' => 'failedtoupdate']);
        }

    }

    // 2. Get next card and pass it to javascript for rendering.
    if ($next != 0) {
        $renderer = $PAGE->get_renderer('mod_cardbox');
        $practice = new cardbox_practice($case, $context, $next, $cardsleft, !$correction);
        $newdata = $practice->export_for_template($renderer);

        echo json_encode(['status' => 'success', 'lastposition' => $lastposition, 'newdata' => $newdata]);

    } else {
        echo json_encode(['status' => 'finished', 'lastposition' => $lastposition]);
    }

}

/* * ********************** Save performance at the end of a practice session *********************** */

if ($action === 'saveperformance') {

    global $DB, $USER;

    $countright = required_param('countright', PARAM_INT);
    $countwrong = required_param('countwrong', PARAM_INT);
    $starttime = required_param('starttime', PARAM_TEXT);
    $percentcorrect = 100 * $countright / ($countright + $countwrong);

    $data = new stdClass();
    $data->userid = $USER->id;
    $data->cardboxid = $cardbox->id;
    $data->timeofpractice = time();
    $data->numberofcards = $countright + $countwrong;
    $data->duration = $data->timeofpractice - $starttime;
    $data->percentcorrect = round($percentcorrect, 0, PHP_ROUND_HALF_UP);
    $success = $DB->insert_record('cardbox_statistics', $data);

    if (empty($success)) {
        echo json_encode(['status' => 'error', 'reason' => 'failedtosaveperformance']);
    } else {
        echo json_encode(['status' => 'success']);
    }

}

/* ****************************************** Suggest answer for a card **************************************************** */


if ($action === 'savesuggestedanswer') {

    require_once($CFG->dirroot . '/mod/cardbox/classes/output/practice.php');

    $cardid = required_param('cardid', PARAM_INT);
    $case = optional_param('case', 1, PARAM_INT);
    $userinput = required_param('userinput', PARAM_TEXT);

    if (!(empty($userinput) || $userinput === "")) {
        cardbox_save_new_cardcontent($cardid, CARDBOX_CARDSIDE_ANSWER, CARDBOX_CONTENTTYPE_TEXT,
                                     $userinput, CARD_ANSWERSUGGESTION_INFORMATION);
    }

}
