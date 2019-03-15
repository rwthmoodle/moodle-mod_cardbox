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
 * In this file, incoming AJAX request studyview.js are handled.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @authors   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/cardbox/locallib.php');

$cmid = required_param('id', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($cmid, 'cardbox');
$cardbox = $DB->get_record('cardbox', array('id'=> $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
require_sesskey();

$context = context_module::instance($cmid);

$action = required_param('action', PARAM_ALPHA); // ...'$action' determines what is to be done; see below.


if ($action === 'review') {
    
    require_once($CFG->dirroot . '/mod/cardbox/classes/output/review.php');
    
    $cardid = required_param('cardid', PARAM_INT);
    $newstatus = required_param('status', PARAM_TEXT);
    $nextcard = optional_param('nextcard', 0, PARAM_INT);

    $dataobject = new stdClass();
    $dataobject->id = $cardid;
    switch($newstatus) {
        
        case 'approve':
            $dataobject->approvedby = $USER->id;
            $success = $DB->update_record('cardbox_cards', $dataobject, false);
            break;
        
        case 'reject':
            break;
        
        case 'skip':
            $success = 1;
            break;
        
    }
    
    if (empty($success)) {
        echo json_encode(['status' => 'error', 'reason' => get_string('error:updateafterreview', 'cardbox')]); // TODO: check double string entries.
    }
    
//    $success = $DB->update_record('cardbox_cards', $dataobject, false);
    
    
    if ($nextcard != 0) {
        $renderer = $PAGE->get_renderer('mod_cardbox');
        $review = new cardbox_review($context, null, $nextcard);
        $newdata = $review->export_for_template($renderer);

        echo json_encode(['status' => 'success', 'newdata' => $newdata]);

    } else {
        echo json_encode(['status' => 'finished']);
    }
    
    
}


/* * ********************** move card to the next box and return next card *********************** */

if ($action === 'updateandnext') {

    require_once($CFG->dirroot . '/mod/cardbox/classes/output/studyview.php');

    $cardid = required_param('cardid', PARAM_INT);
    $iscorrect = required_param('iscorrect', PARAM_INT);
    $next = required_param('next', PARAM_INT);
    $isrepetition = required_param('isrepetition', PARAM_INT);

    $lastposition = -1;
    if ($isrepetition == 0) {

        // 1. Update card entry. XXX in class card auslagern?
        $dataobject = $DB->get_record('cardbox_progress', array('userid' => $USER->id, 'card' => $cardid), $fields='*', MUST_EXIST);

        if (empty($dataobject)) {
            echo json_encode(['status' => 'error', 'reason' => 'nocardboxentryfound']);
        }
        $lastposition = $dataobject->cardposition;

        $dataobject->lastpracticed = time(); 
        if ($iscorrect == 1) {
            $dataobject->cardposition++; // TODO What happens after box 5?
        } else {
            $dataobject->cardposition = 1;
        }
        $dataobject->repetitions++;
        $success = $DB->update_record('cardbox_progress', $dataobject, false);

        if (empty($success)) {
            echo json_encode(['status' => 'error', 'reason' => 'failedtoupdate']);
        }
    }
    

    // 2. Get next card and pass it to javascript for rendering.
    if ($next != 0) {
        $renderer = $PAGE->get_renderer('mod_cardbox');
        $studyview = new cardbox_studyview($context, null, $next);
        $newdata = $studyview->export_for_template($renderer);

        echo json_encode(['status' => 'success', 'lastposition' => $lastposition, 'newdata' => $newdata]);

    } else {
        echo json_encode(['status' => 'finished', 'lastposition' => $lastposition]);
    }
    

}




//$cardboxinstanceid = required_param('instanceid', PARAM_PATH);


//$pdfannotator = $DB->get_record('pdfannotator', array('id' => $documentid), '*', MUST_EXIST);
//$cm = get_coursemodule_from_instance('pdfannotator', $documentid, $pdfannotator->course, false, MUST_EXIST);
//$context = context_module::instance($cm->id);

//require_course_login($pdfannotator->course, true, $cm);
//require_capability('mod/pdfannotator:view', $context);
