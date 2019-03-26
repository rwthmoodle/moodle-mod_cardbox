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
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @authors   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$question = optional_param('question', null, PARAM_ALPHANUM);
$isedit = optional_param('isedit', 0, PARAM_INT);

if (!empty($question)) { // XXX dirty solution. For some reason, the action parameter is lost when sending a form with multiple answers
    if ($isedit === 0) {
        $action = 'addflashcard';
    } else {
        $action = 'editcard';
    }
}

/* *********************************************** Add a new flashcard *********************************************** */

if ($action === 'addflashcard') {

    echo $myrenderer->cardbox_render_tabs($taburl, $action, $context);

    global $USER, $DB;

    require_once('card_form.php');

    $returnurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'practice'));
    $actionurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'addflashcard'));

    // Contextual data to pass on to the card form.
    if (empty($entry)) {
        $entry = new stdClass();
        $entry->id = $cmid;
        $entry->course = $cm->course;
        $entry->action = $action;
    }

    $options = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 3,
                          'accepted_types' => array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'svg'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
    $component = 'mod_cardbox';
    $filearea = 'content';

    $mform = new mod_cardbox_card_form();
    $mform->set_data($entry);

    if ($mform->is_cancelled()) {

        redirect($returnurl);

    // If submitted: get files from filemanager.
    } else if ($formdata = $mform->get_data()) {

        // Create or select a topic for the card.
        switch ($formdata->topic) {
            case -1: // Card belongs to no topic.
                $topicid = null;
                break;
            case 0: // Card belongs to a new topic that is to be created.
                if (!empty($formdata->newtopic)) {
                    $topicid = cardbox_save_new_topic($formdata->newtopic);
                } else {
                    $topicid = null;
                }
                break;
            default: // Card belongs to an already existing topic.
                $topicid = $formdata->topic;
        }

        // Create a new entry in cardbox_cards table.
        $cardid = cardbox_save_new_card($cardbox->id, $topicid);

        // Save the question text if there is any.
        if (!empty($formdata->question['text'])) {
            cardbox_save_new_cardcontent($cardid, 0, 2, $formdata->question);
        }
        // Save the text of the answer/s.
        foreach ($formdata->answer as $answer) {
            cardbox_save_new_cardcontent($cardid, 1, 2, $answer);
        }

        // Get the draft itemid (Files in the drag-and-drop area are automatically saved as drafts in mdl_files even before the form is submitted).
        $draftitemid = file_get_submitted_draft_itemid('cardimage');

        // Copy all the files from the 'real' area, into the draft area.
        file_prepare_draft_area($draftitemid, $context->id, $component, $filearea, 0, array('subdirs'=>true));

        // Save the file.
        if ($draftitemid != null) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'sortorder, id', false)) {          
                foreach ($files as $file) {
                    // Save a reference to the image data in cardbox_cardcontents.
                    $itemid = cardbox_save_new_cardcontent($cardid, 0, 1, $file->get_filename()); // XXX Make contenttype dynamic (SQL join, install.php)
                    // Save the actual image data in moodle.
                    file_save_draft_area_files($draftitemid, $context->id, $component, $filearea, $itemid, $options);
                    break;
                }
            }
        }

        // TODO: check for errors, validate form

        // Give user feedback and go back to practice.
        // 
        redirect($returnurl, get_string('success:addnewcard', 'cardbox'), null, \core\output\notification::NOTIFY_SUCCESS);
    
    } else {

        echo $OUTPUT->heading(get_string('titleforaddflashcard', 'cardbox'));
        $mform->display();

    }
}

/* ************************************************ Edit a flashcard ************************************************* */

if ($action === 'editcard') {

    global $DB;

    require_once('card_form.php');
    $cardid = required_param('cardid', PARAM_INT);

    $returnurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'review'));
    $actionurl = $returnurl; //new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => $action));

    $draftitemid = file_get_submitted_draft_itemid('cardimage'); // name of the filemanager element

    $itemid = $DB->get_field('cardbox_cardcontents', 'id', array('card' => $cardid, 'contenttype' => 1), IGNORE_MISSING);

    $topic = cardbox_get_topic($cardid);
    $answers = cardbox_get_answers($cardid);
    $answercount = count($answers);

    $customdata = array('topic' => $topic, 'answercount' => $answercount);
    $mform = new mod_cardbox_card_form($actionurl, $customdata);

    $options = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 3,
                          'accepted_types' => array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'svg'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
    $component = 'mod_cardbox';
    $filearea = 'content';

    // XXX Vielleicht einmal in der DB fragen, ob schon ein Bild für die Karte vorliegt und je nachdem unterschiedlich weiter?
    
    // Copy the file (if there is on) from the 'real' area into the draft area.
    if (!empty($itemid)) {
        file_prepare_draft_area($draftitemid, $context->id, $component, $filearea, $itemid, $options);
    }

    // Pass the data of this card to the card_form for editing.
    if (empty($entry)) {
        $entry = new stdClass();
        $entry->id = $cmid;
        $entry->course = $cm->course;
        $entry->cardid = $cardid;
        $entry->question = cardbox_get_questiontext($cardid);
        for ($i = 0; $i < $answercount; $i++) {
            $entry->answer[$i] = $answers[$i];
        }
        $entry->cardimage = $draftitemid;
        $entry->isedit = 1;
        $entry->action = 'editcard';
    }
    $mform->set_data($entry);
    
    if ($mform->is_cancelled()) {
        
        $action = 'review';

    // If submitted: get files from filemanager
    } else if ($formdata = $mform->get_data()) {
        
        // Create or select a topic for the card.
        switch ($formdata->topic) {
            case -1: // Card belongs to no topic.
                $topicid = null;
                break;
            case 0: // Card belongs to a new topic that is to be created.
                if (!empty($formdata->newtopic)) {
                    $topicid = cardbox_save_new_topic($formdata->newtopic);
                } else {
                    $topicid = null;
                }
                break;
            default: // Card belongs to an already existing topic.
                $topicid = $formdata->topic;
        }

        // Update the entry in cardbox_cards table and delete the original content items.
        $success = cardbox_edit_card($cardid, $topicid);

        // TODO: Fehlerbehandlung.
        
        // Save the question text if there is any.
        if (!empty($formdata->question)) {
            cardbox_save_new_cardcontent($cardid, 0, 2, $formdata->question);
        }
        // Save the text of the answer/s.
        foreach ($formdata->answer as $answer) {
            cardbox_save_new_cardcontent($cardid, 1, 2, $answer);
        }

        // Get the draft itemid (Files in the drag-and-drop area are automatically saved as drafts in mdl_files even before the form is submitted).
        $draftitemid = file_get_submitted_draft_itemid('cardimage');

        // Copy all the files from the 'real' area, into the draft area.
        file_prepare_draft_area($draftitemid, $context->id, $component, $filearea, 0, array('subdirs'=>true));

        // Save the file.
        if ($draftitemid != null) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'sortorder, id', false)) {          
                echo 'Fehlerbehandlung!';
            } else {
                foreach ($files as $file) {
                    // Save a reference to the image data in cardbox_cardcontents.
                    $itemid = cardbox_save_new_cardcontent($cardid, 0, 1, $file->get_filename()); // XXX Make contenttype dynamic (SQL join, install.php)
                    // Save the actual image data in moodle.
                    file_save_draft_area_files($draftitemid, $context->id, $component, $filearea, $itemid, $options);
                    break;
                }
            }

        }

        $action = 'review';
    
    } else {

        echo $myrenderer->cardbox_render_tabs($taburl, 'review', $context);
        echo $OUTPUT->heading(get_string('titleforcardedit', 'cardbox'));
        $mform->display();

    }

}

/* ************************************************ Settings for Practice ************************************************* */

if ($action === 'choosesettings') {
    
    echo $myrenderer->cardbox_render_tabs($taburl, 'practice', $context);

    require_once('practicesettings_form.php');

    $returnurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'practice'));
    $actionurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'addflashcard'));

    // Contextual data to pass on to the card form.
    if (empty($entry)) {
        $entry = new stdClass();
        $entry->id = $cmid;
        $entry->course = $cm->course;
        $entry->action = 'practice';
    }

    $mform = new mod_cardbox_practicesettings_form();
    $mform->set_data($entry);
    
    if ($mform->is_cancelled()) {

        redirect($returnurl);

    // If submitted: get files from filemanager
    } else if ($formdata = $mform->get_data()) {


        // TODO: check for errors, validate form

        // Give user feedback and go back to practice.
//        redirect($returnurl, get_string('success:addnewcard', 'cardbox'), null, \core\output\notification::NOTIFY_SUCCESS);
    
    } else {

        echo $OUTPUT->heading(get_string('titleforchoosesettings', 'cardbox'));
        $mform->display();

    }

}

/* **************************************************** Practice cards **************************************************** */

if ($action === 'practice') {
    
    echo $myrenderer->cardbox_render_tabs($taburl, $action, $context);

    require_once('model/cardbox.class.php');
    require_once($CFG->dirroot . '/mod/cardbox/classes/output/practice.php');
    // require_once($CFG->dirroot . '/mod/cardbox/classes/output/card.php'); // XXX File entfernen.

    echo $OUTPUT->heading("$cardbox->name");
    
    $correction = optional_param('correction', 0, PARAM_INT); // Self check (default) or automatic check.
    $topic = optional_param('topic', null, PARAM_INT); // Self check or automatic check.
    $case = optional_param('case', 1, PARAM_INT);
    
    // 1. Create a virtual cardbox for this practice session. (model)
    $cardbox = new cardbox_cardboxmodel($cardbox->id, $topic);
    $selection = $cardbox->cardbox_get_card_selection();
    
    if (empty($selection)) {
        $info = get_string('info:nocardsavailable', 'cardbox');
        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>$info</div></span>";
        return;
    }
    
    $cardboxstatus = $cardbox->cardbox_get_status();

    // 2. Give javascript access to the language string repository and add it to the page.
    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('cardbox', 'en'); // Method gets the strings of the language files.
    $PAGE->requires->strings_for_js(array_keys($strings), 'cardbox'); // Method to use the language-strings in javascript.
    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/Chart.bundle.js"));
    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/practice.js"));

    
    $renderer = $PAGE->get_renderer('mod_cardbox');
    $practice = new cardbox_practice($case, $context, $cardbox, null, $correction); // (view controller)
    $data = $practice->export_for_template($renderer);
    
    $params = array($cmid, $selection, $cardboxstatus, $correction, $case, $data); // true means: the user checks their own results.
    $PAGE->requires->js_init_call('startPractice', $params, true);

    // 3. Render the page.
    
//    print_r($data);
    
    echo $renderer->cardbox_render_practice($practice);
    
    // old code which works for the self checking mode (only):
    // 
//    echo $myrenderer->cardbox_render_tabs($taburl, $action, $context);
//
//    require_once('model/cardbox.class.php');
//    require_once($CFG->dirroot . '/mod/cardbox/classes/output/studyview.php');
//    // require_once($CFG->dirroot . '/mod/cardbox/classes/output/card.php'); // XXX File entfernen.
//
//    echo $OUTPUT->heading("$cardbox->name");
//    
//    $correction = optional_param('correction', 0, PARAM_INT); // Self check (default) or automatic check.
//    $topic = optional_param('topic', null, PARAM_INT); // Self check or automatic check.
//
//    // 1. Create a virtual cardbox for this practice session. (model)
//    $cardbox = new cardbox_cardboxmodel($cardbox->id, $topic);
//    $selection = $cardbox->cardbox_get_card_selection();
//    $cardboxstatus = $cardbox->cardbox_get_status();
//
//    // 2. Give javascript access to the language string repository and add it to the page.
//    $stringman = get_string_manager();
//    $strings = $stringman->load_component_strings('cardbox', 'en'); // Method gets the strings of the language files.
//    $PAGE->requires->strings_for_js(array_keys($strings), 'cardbox'); // Method to use the language-strings in javascript.
//    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/Chart.bundle.js"));
//    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/studyview.js"));
//
//    $params = array($cmid, $selection, $cardboxstatus, $correction); // true means: the user checks their own results.
//    $PAGE->requires->js_init_call('startPractice', $params, true);
//
//    // 3. Render the page.
//    $renderer = $PAGE->get_renderer('mod_cardbox');
//    $studyview = new cardbox_studyview($context, $cardbox, null, $correction); // (view controller)
//    echo $renderer->cardbox_render_studyview($studyview);

}

/* **************************************************** Approve/edit cards **************************************************** */

if ($action === 'review') {
    
    echo $myrenderer->cardbox_render_tabs($taburl, $action, $context);

    require_once('model/cardcollection.class.php'); // model.
    require_once($CFG->dirroot . '/mod/cardbox/classes/output/review.php'); // view controller.
    
    echo $OUTPUT->heading("<span id='cardbox-review-headline'>" . get_string('titleforreview', 'cardbox') . "</span>");

    // 1. Create the model.
    $collection = new cardbox_cardcollection($cardbox->id);
    $list = $collection->cardbox_get_card_list();
    
    if (empty($list)) {
        $info = get_string('info:nocardsavailableforreview', 'cardbox');
        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>$info</div></span>";
        return;
    }

    // 2.a) Include scripts to control the behaviour of the page.
    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('cardbox', 'en');
    $PAGE->requires->strings_for_js(array_keys($strings), 'cardbox');
//    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/Chart.bundle.js")); // TODO: Entfernen, falls doch nicht benutzt.
    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/review.js"));

    // 2.b) Call script wrapper function.
    $params = array($cmid, $list);
    $PAGE->requires->js_init_call('startReview', $params, true);

    // 3. Create the view controller.
    $renderer = $PAGE->get_renderer('mod_cardbox');
    $review = new cardbox_review($context, $collection);

    // 4. Render the view.
    echo $renderer->cardbox_render_review($review);

}
