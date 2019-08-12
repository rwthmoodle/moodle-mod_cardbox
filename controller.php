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
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/* *********************************************** Add a new flashcard *********************************************** */

if ($action === 'addflashcard') {

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
                          'accepted_types' => array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'svg'), 'return_types'=> 1 | 2);
    $component = 'mod_cardbox';
    $filearea = 'content';

    $customdata = array('cardboxid' => $cardbox->id, 'cmid' => $cmid);
    $mform = new mod_cardbox_card_form(null, $customdata);
    $mform->set_data($entry);

    if ($mform->is_cancelled()) {

        if (has_capability('mod/cardbox:practice', $context)) { // for students and other participants.
            $action = 'practice';

        } else if (has_capability('mod/cardbox:approvecard', $context)) {
            $action = 'review';

        } else { // for guests.
            redirect($actionurl, '');
        }
        
    // If submitted: get files from filemanager.
    } else if ($formdata = $mform->get_data()) {
        
        if (!empty($formdata->submitbutton)) {
            $submitbutton = $formdata->submitbutton;
        } else {
            $submitbutton = null;
        }

        // Create or select a topic for the card.
        switch ($formdata->topic) {
            case -1: // Card belongs to no topic.
                $topicid = null;
                break;
            case 0: // Card belongs to a new topic that is to be created.
                if (!empty($formdata->newtopic)) {
                    $topicid = cardbox_save_new_topic($formdata->newtopic, $cardbox->id);
                } else {
                    $topicid = null;
                }
                break;
            default: // Card belongs to an already existing topic.
                $topicid = $formdata->topic;
        }

        // Create a new entry in cardbox_cards table.
        $cardid = cardbox_save_new_card($cardbox->id, $submitbutton, $context, $topicid);

        // Save the question text if there is any.
        if (!empty($formdata->question['text'])) {
            cardbox_save_new_cardcontent($cardid, 0, 2, $formdata->question['text']);
        }
        // Save the text of the answer/s.
        foreach ($formdata->answer as $answer) {
            cardbox_save_new_cardcontent($cardid, 1, 2, $answer['text']);
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

        // Get the draft itemid (Files in the drag-and-drop area are automatically saved as drafts in mdl_files even before the form is submitted).
        $draftitemid2 = file_get_submitted_draft_itemid('cardsound');
        
        // Copy all the audio files from the 'real' area, into the draft area.
        file_prepare_draft_area($draftitemid2, $context->id, $component, $filearea, 0, array('subdirs'=>true));

        // Save the audio file.
        if ($draftitemid2 != null) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid2, 'sortorder, id', false)) {          
                foreach ($files as $file) {
                    // Save a reference to the image data in cardbox_cardcontents.
                    $itemid2 = cardbox_save_new_cardcontent($cardid, 0, 3, $file->get_filename()); // XXX Make contenttype dynamic (SQL join, install.php)
                    // Save the actual image data in moodle.
                    file_save_draft_area_files($draftitemid2, $context->id, $component, $filearea, $itemid2, $options);
                    break;
                }
            }
        }
        
        // Get the draft itemid (Files in the drag-and-drop area are automatically saved as drafts in mdl_files even before the form is submitted).
        $draftitemid3 = file_get_submitted_draft_itemid('answerimage');

        // Copy all the files from the 'real' area, into the draft area.
        file_prepare_draft_area($draftitemid3, $context->id, $component, $filearea, 0, array('subdirs'=>true));

        // Save the file.
        if ($draftitemid3 != null) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid3, 'sortorder, id', false)) {          
                foreach ($files as $file) {
                    // Save a reference to the image data in cardbox_cardcontents.
                    $itemid3 = cardbox_save_new_cardcontent($cardid, 1, 1, $file->get_filename()); // XXX Make contenttype dynamic (SQL join, install.php)
                    // Save the actual image data in moodle.
                    file_save_draft_area_files($draftitemid3, $context->id, $component, $filearea, $itemid3, $options);
                    break;
                }
            }
        }

        // Get the draft itemid (Files in the drag-and-drop area are automatically saved as drafts in mdl_files even before the form is submitted).
        $draftitemid4 = file_get_submitted_draft_itemid('answersound');
        
        // Copy all the audio files from the 'real' area, into the draft area.
        file_prepare_draft_area($draftitemid2, $context->id, $component, $filearea, 0, array('subdirs'=>true));

        // Save the audio file.
        if ($draftitemid2 != null) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid4, 'sortorder, id', false)) {          
                foreach ($files as $file) {
                    // Save a reference to the image data in cardbox_cardcontents.
                    $itemid4 = cardbox_save_new_cardcontent($cardid, 1, 3, $file->get_filename()); // XXX Make contenttype dynamic (SQL join, install.php)
                    // Save the actual image data in moodle.
                    file_save_draft_area_files($draftitemid4, $context->id, $component, $filearea, $itemid4, $options);
                    break;
                }
            }
        }
        
        if (!empty($submitbutton) && $submitbutton == get_string('saveandaccept', 'cardbox') && has_capability('mod/cardbox:approvecard', $context)) {
            $message = get_string('success:addandapprovenewcard', 'cardbox');
        } else {
            $message = get_string('success:addnewcard', 'cardbox');
        }      
                
        redirect($actionurl, $message, null, \core\output\notification::NOTIFY_INFO);
        // TODO: check for errors, validate form
        
    } else {

        $PAGE->set_url('/mod/cardbox/view.php', array('id' => $cm->id, 'action' => 'addflashcard'));
        echo $OUTPUT->header(); // Display course name, navigation bar at the very top and "Dashboard->...->..." bar.
        
        if ($mform->is_submitted() && empty($mform->is_validated())) {
            $info = get_string('error:createcard', 'cardbox');
            echo "<span class='notification'><div class='alert alert-danger alert-block fade in' role='alert'>" . $info . "</div></span>";
        }
        
        
        echo $OUTPUT->heading(format_string($cardbox->name));
        echo $myrenderer->cardbox_render_tabs($taburl, $action, $context);
        //echo $OUTPUT->heading(get_string('titleforaddflashcard', 'cardbox'));
//        $info = get_string('titleforaddflashcard', 'cardbox');
//        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>" . $info . "</div></span>";
        $mform->display();

    }

}

/* ************************************************ Edit a flashcard ************************************************* */

if ($action === 'editcard') {
    
    require_capability('mod/cardbox:approvecard', $context);

    global $DB;

    require_once('card_form.php');
    $cardid = required_param('cardid', PARAM_INT);
    $nextcardid = optional_param('next', 0, PARAM_INT);

    $from = optional_param('from', 'review', PARAM_ALPHA);
    
    if ($from === 'review') {
        $returnurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'review'));
    } else {
        $returnurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'overview'));
    }

    $actionurl = $returnurl; //new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => $action));

    $draftitemid = file_get_submitted_draft_itemid('cardimage'); // name of the filemanager element
    $itemid = $DB->get_field('cardbox_cardcontents', 'id', array('card' => $cardid, 'contenttype' => 1), IGNORE_MISSING);
    
    $draftitemid2 = file_get_submitted_draft_itemid('cardsound'); // name of the filemanager element
    $itemid2 = $DB->get_field('cardbox_cardcontents', 'id', array('card' => $cardid, 'contenttype' => 3), IGNORE_MISSING);

    $topic = cardbox_get_topic($cardid);
    $answers = cardbox_get_answers($cardid);
    $answercount = count($answers);

    $customdata = array('topic' => $topic, 'answercount' => $answercount, 'cardboxid' => $cardbox->id, 'cmid' => $cmid);
    $mform = new mod_cardbox_card_form($actionurl, $customdata);

    $options = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 3,
                          'accepted_types' => array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'svg'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
    $component = 'mod_cardbox';
    $filearea = 'content';

    // XXX Vielleicht einmal in der DB fragen, ob schon ein Bild für die Karte vorliegt und je nachdem unterschiedlich weiter?
    
    // Copy the picture file (if there is on) from the 'real' area into the draft area.
    if (!empty($itemid)) {
        file_prepare_draft_area($draftitemid, $context->id, $component, $filearea, $itemid, $options);
    }
    
    // Copy the audio file (if there is on) from the 'real' area into the draft area.
    if (!empty($itemid2)) {
        file_prepare_draft_area($draftitemid2, $context->id, $component, $filearea, $itemid2, $options);
    }

    // Pass the data of this card to the card_form for editing.
    if (empty($entry)) {
        $entry = new stdClass();
        $entry->id = $cmid;
        $entry->course = $cm->course;
        $entry->cardid = $cardid;
        $entry->question['text'] = cardbox_get_questiontext($cardid);
        $entry->question['format'] = '1';
        for ($i = 0; $i < $answercount; $i++) {
            $entry->answer[$i]['text'] = $answers[$i];
            $entry->answer[$i]['format'] = '1';
            $entry->from = $from;
        }
        $entry->cardimage = $draftitemid;
        $entry->cardsound = $draftitemid2;
        $entry->action = 'editcard';
        $entry->next = $nextcardid;
    }
    $mform->set_data($entry);
    
    if ($mform->is_cancelled()) {
        
        $action = 'review';

    // If submitted: get files from filemanager
    } else if ($formdata = $mform->get_data()) {
        
        if (!empty($formdata->submitbutton)) {
            $submitbutton = $formdata->submitbutton;
        } else {
            $submitbutton = null;
        }

        // Create or select a topic for the card.
        switch ($formdata->topic) {
            case -1: // Card belongs to no topic.
                $topicid = null;
                break;
            case 0: // Card belongs to a new topic that is to be created.
                if (!empty($formdata->newtopic)) {
                    $topicid = cardbox_save_new_topic($formdata->newtopic, $cardbox->id);
                } else {
                    $topicid = null;
                }
                break;
            default: // Card belongs to an already existing topic.
                $topicid = $formdata->topic;
        }

        // Update the entry in cardbox_cards table and delete the original content items.
        $success = cardbox_edit_card($cardid, $topicid, $submitbutton, $context);

        // TODO: Fehlerbehandlung.
        
        // Save the question text if there is any.
        if (!empty($formdata->question)) {
            cardbox_save_new_cardcontent($cardid, 0, 2, $formdata->question['text']);
        }
        // Save the text of the answer/s.
        foreach ($formdata->answer as $answer) {
            cardbox_save_new_cardcontent($cardid, 1, 2, $answer['text']);
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
        
        // Get the draft itemid (Files in the drag-and-drop area are automatically saved as drafts in mdl_files even before the form is submitted).
        $draftitemid2 = file_get_submitted_draft_itemid('cardsound');
        
        // Copy all the audio files from the 'real' area, into the draft area.
        file_prepare_draft_area($draftitemid2, $context->id, $component, $filearea, 0, array('subdirs'=>true));

        // Save the audio file.
        if ($draftitemid2 != null) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid2, 'sortorder, id', false)) {          
                foreach ($files as $file) {
                    // Save a reference to the image data in cardbox_cardcontents.
                    $itemid2 = cardbox_save_new_cardcontent($cardid, 0, 3, $file->get_filename()); // XXX Make contenttype dynamic (SQL join, install.php)
                    // Save the actual image data in moodle.
                    file_save_draft_area_files($draftitemid2, $context->id, $component, $filearea, $itemid2, $options);
                    break;
                }
            }
        }

        if ($from === 'overview') { // i.e. if the card had already been approved and has possibly been practiced.
            cardbox_send_change_notification($cmid, $cardbox, $cardid);
        }

        if (!empty($nextcardid) && $submitbutton == get_string('saveandaccept', 'cardbox') && has_capability('mod/cardbox:approvecard', $context)) {
            $cardid = $nextcardid;
        }
        
        $action = $from;

    } else {

        $PAGE->set_url('/mod/cardbox/view.php', array('id' => $cm->id, 'action' => 'editcard', 'from' => $from));
        echo $OUTPUT->header(); // Display course name, navigation bar at the very top and "Dashboard->...->..." bar.
        
        if ($mform->is_submitted() && empty($mform->is_validated())) {
            $info = get_string('error:createcard', 'cardbox');
            echo "<span class='notification'><div class='alert alert-danger alert-block fade in' role='alert'>" . $info . "</div></span>";
        }
        
        echo $OUTPUT->heading(get_string('titleforcardedit', 'cardbox'));
        echo $myrenderer->cardbox_render_tabs($taburl, 'review', $context);
        
        $mform->display();

    }

}

/* **************************************************** Practice cards **************************************************** */

if ($action === 'practice') {
    
    require_once('model/cardbox.class.php');
    require_once('model/card_selection_algorithm.php');
    require_once('model/card_sorting_algorithm.php');

    $PAGE->set_url('/mod/cardbox/view.php', array('id' => $cm->id, 'action' => 'practice'));
    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($cardbox->name));
    echo $myrenderer->cardbox_render_tabs($taburl, $action, $context);
    //echo $OUTPUT->heading("$cardbox->name");

    $renderer = $PAGE->get_renderer('mod_cardbox');

    $startnow = optional_param('start', false, PARAM_BOOL);
    $correction = optional_param('mode', 0, PARAM_INT); // automatic check against solution (default) or self check.
    $topic = optional_param('topic', null, PARAM_INT); // topic to prioritize.
    $practiceall = optional_param('practiceall', true, PARAM_BOOL);
    $openmodal = true;

    // 1. Create a virtual cardbox for this practice session, i.e. create the model.
    $select = new cardbox_card_selection_algorithm($topic, $practiceall);
    $sort = new cardbox_card_sorting_algorithm();
    $cardboxmodel = new cardbox_cardboxmodel($cardbox->id, $select, $sort);
    
    $cardcount = $cardboxmodel->cardbox_count_cards();
    $duecardcount = $cardboxmodel->cardbox_count_due_cards();

    // Inform the user that their cardbox is empty.
    if (empty($cardcount)) {
        
        $info = get_string('info:nocardsavailable', 'cardbox');
        $help = $OUTPUT->help_icon('help:nocardsavailable', 'cardbox');
        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>" . $info . " " . $help . "</div></span>";
        return;
    
    // Inform the user that all of their cards have the status 'mastered' and are no longer repeated.
    } else if ($cardcount == $cardboxmodel->cardbox_count_mastered_cards()) {
        
        $info = get_string('info:nocardsavailableforpractice', 'cardbox');
        $help = $OUTPUT->help_icon('help:nocardsavailableforpractice', 'cardbox');
        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>" . $info . " " . $help . "</div></span>";
        return;
    
    // Inform the user that none of their cards are due for practice right now.
    } else if (empty($duecardcount)) {
        
        $info_part1 = get_string('info:nocardsdueforpractice', 'cardbox');
        $info_part2 = get_string('help:practiceanyway', 'cardbox');
        $help = $OUTPUT->help_icon('help:nocardsdueforpractice', 'cardbox');
        echo "<span id='nocardsduenotification' class='notification'><div class='alert alert-info alert-block fade in' role='alert'>" . $info_part1 . " " . $help . "<br>" . $info_part2 . "</div></span>";
        $openmodal = false;
    }

    if ($startnow && !( empty($duecardcount) &&  $practiceall == false)) {

        require_once($CFG->dirroot . '/mod/cardbox/classes/output/practice.php');

        $selection = $cardboxmodel->cardbox_get_card_selection();
        //$cardboxstatus = $cardboxmodel->cardbox_get_status();

        // 2. Create a view controller.
        if ($correction % 2 == 0) {
            $case = 2;
        } else {
            $case = 1;
        }
        $practice = new cardbox_practice($case, $context, $selection[0]);
        $data = $practice->export_for_template($renderer);

        // 3. Give javascript access to the language string repository and to the relevant model data and add it to the page.
        $stringman = get_string_manager();
        $strings = $stringman->load_component_strings('cardbox', 'en'); // Method gets the strings of the language files.
        $PAGE->requires->strings_for_js(array_keys($strings), 'cardbox'); // Method to use the language-strings in javascript.
        $PAGE->requires->js(new moodle_url("/mod/cardbox/js/Chart.bundle.js"));
        $PAGE->requires->js(new moodle_url("/mod/cardbox/js/practice.js"));
        $params = array($cmid, $selection, $case, $data); // true means: the user checks their own results.
        $PAGE->requires->js_init_call('startPractice', $params, true);

        // 3. Render the page.
        echo $renderer->cardbox_render_practice($practice);
        
    } else { // Render a modal dialogue that asks the user to select their practice preferences.

        require_once($CFG->dirroot . '/mod/cardbox/classes/output/start.php');

        $PAGE->requires->js(new moodle_url("/mod/cardbox/js/start.js"));
        $PAGE->requires->js_init_call('startOptions', array($cmid, $openmodal), true);

        $start = new cardbox_start($cardbox->autocorrection, $cardbox->id);

        echo $renderer->cardbox_render_practice_start($start);

    }

}

/* **************************************************** View progress **************************************************** */

if ($action === 'statistics') {

    require_once('model/cardbox.class.php');
    require_once($CFG->dirroot . '/mod/cardbox/classes/output/statistics.php');

    $PAGE->set_url('/mod/cardbox/view.php', array('id' => $cm->id, 'action' => 'statistics'));
    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($cardbox->name));
    echo $myrenderer->cardbox_render_tabs($taburl, $action, $context);
    //echo $OUTPUT->heading("$cardbox->name"); // XXX

    $renderer = $PAGE->get_renderer('mod_cardbox');
    
    // 1. Create a virtual cardbox for this user, i.e. create the model.
    $cardboxmodel = new cardbox_cardboxmodel($cardbox->id);
    $boxcount = $cardboxmodel->cardbox_get_status();
    
    // 2. Create a view controller.
    $statistics = new cardbox_statistics($cardbox->id); // XXX auch hier das cardboxmodel nutzen.
    $performance = $statistics->export_for_template($renderer);
    
    // 3. Give javascript access to the language string repository and to the relevant model data and add it to the page.
    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('cardbox', 'en'); // Method gets the strings of the language files.
    $PAGE->requires->strings_for_js(array_keys($strings), 'cardbox'); // Method to use the language-strings in javascript.
    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/Chart.bundle.js"));
    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/statistics.js"));
    $params = array($cmid, $boxcount, $performance); // true means: the user checks their own results.
    $PAGE->requires->js_init_call('displayCharts', $params, true);
        
    // 4. Render the page.
    echo $renderer->cardbox_render_statistics($statistics);

}

/* **************************************************** Approve/edit cards **************************************************** */

if ($action === 'review') {
    
    $PAGE->set_url('/mod/cardbox/view.php', array('id' => $cm->id, 'action' => 'review'));
    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($cardbox->name));
    echo $myrenderer->cardbox_render_tabs($taburl, $action, $context);

    require_once('model/cardcollection.class.php'); // model.
    require_once($CFG->dirroot . '/mod/cardbox/classes/output/review.php'); // view controller.
    
    // echo $OUTPUT->heading("<span id='cardbox-review-headline'>" . get_string('titleforreview', 'cardbox') . "</span>");

    // echo "<h4>" . get_string('titleforreview', 'cardbox') . "</h4>";
    
    // 1. Create the model.
    $collection = new cardbox_cardcollection($cardbox->id);
    $list = $collection->cardbox_get_card_list();
    
    if (empty($list)) {
        $info = get_string('info:nocardsavailableforreview', 'cardbox');
        echo "<span id='cardbox-review-notification' class='notification'><div class='alert alert-info alert-block fade in' role='alert'>$info</div></span>";
        return;
    } else {
        $info = get_string('titleforreview', 'cardbox');
        echo "<span id='cardbox-review-notification' class='notification'><div class='alert alert-info alert-block fade in' role='alert'>" . $info . "</div></span>";
    }

    // 2.a) Include scripts to control the behaviour of the page.
    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('cardbox', 'en');
    $PAGE->requires->strings_for_js(array_keys($strings), 'cardbox');
//    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/Chart.bundle.js")); // TODO: Entfernen, falls doch nicht benutzt.
    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/review.js"));

    // 2.b) Call script wrapper function.
    if (empty($cardid)) {
        $cardid = 0;
    }
    $params = array($cmid, $list, $cardid);
    $PAGE->requires->js_init_call('startReview', $params, true);

    // 3. Create the view controller.
    $renderer = $PAGE->get_renderer('mod_cardbox');
    if (empty($cardid)) {
        $review = new cardbox_review($context, $collection);
    } else {
        $review = new cardbox_review($context, null, $cardid);
    }

    // 4. Render the view.
    echo $renderer->cardbox_render_review($review);

}

/* **************************************************** Overview of all cards **************************************************** */

//if ($action === 'overview') {
//
//    $page = optional_param('page', 0, PARAM_INT);
//    $perpage = 10;
//    $offset = $page * $perpage;
//    
//    require_once('model/cardcollection.class.php'); // model.
//    require_once($CFG->dirroot . '/mod/cardbox/classes/output/overview.php');
//    
//    $PAGE->set_url('/mod/cardbox/view.php', array('id' => $cm->id, 'action' => 'overview'));
//    echo $OUTPUT->header();
//    echo $OUTPUT->heading("$cardbox->name");
//    echo $myrenderer->cardbox_render_tabs($taburl, $action, $context);
//
//    // 1. Create the model.
//    $collection = new cardbox_cardcollection($cardbox->id, true);
//    $list = $collection->cardbox_get_card_list();
//
//    $context = context_module::instance($cmid);
//
//    if (empty($list)) {
//        $info = get_string('info:nocardsavailableforoverview', 'cardbox');
//        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>$info</div></span>";
//        return;
//        
//    } else {
//        
//        $totalcount = count($list);
//        $baseurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'overview'));
//        
//        $info = get_string('intro:overview', 'cardbox');
//        echo "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>$info</div></span>";
//        
//        // 2. Create a view controller.
//        $overview = new cardbox_overview($list, $offset, $context, $cmid);
//        
//        // 4. Render the page.
//        $renderer = $PAGE->get_renderer('mod_cardbox');
//        echo $renderer->cardbox_render_overview($overview);
//        echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
//    }
//
//}
