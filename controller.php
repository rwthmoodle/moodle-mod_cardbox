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

/* *********************************************** Add a new flashcard *********************************************** */

$question = optional_param('question', null, PARAM_ALPHANUM);

if (!empty($question)) { // XXX dirty solution. For some reason, the action parameter is lost when sending a form with multiple answers
    $action = 'addflashcard';
}

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
                          'accepted_types' => array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'svg'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
    $component = 'mod_cardbox';
    $filearea = 'content';

    $mform = new mod_cardbox_card_form();
    $mform->set_data($entry);
    
    if ($mform->is_cancelled()) {

        redirect($returnurl);

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
            default: // Card belongs to an already existing topic
                $topicid = $formdata->topic;
        }

        // Create a new entry in cardbox_cards table
        $cardid = cardbox_save_new_card($cardbox->id, $topicid);

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

        // TODO: check for errors, validate form

        // Give user feedback and go back to practice.
        redirect($returnurl, get_string('success:addnewcard', 'cardbox'), null, \core\output\notification::NOTIFY_SUCCESS);
    
    } else {

        echo $OUTPUT->heading(get_string('titleforaddflashcard', 'cardbox'));
        $mform->display();

    } 
}

/* **************************************************** Practice cards **************************************************** */

if ($action === 'practice') {

    require_once($CFG->dirroot . '/mod/cardbox/classes/output/studyview.php');
    require_once($CFG->dirroot . '/mod/cardbox/classes/output/card.php');

    echo $OUTPUT->heading("$cardbox->name");
    
    // 1. Give javascript access to the language string repository and add it to the page.
    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('cardbox', 'en'); // Method gets the strings of the language files.
    $PAGE->requires->strings_for_js(array_keys($strings), 'cardbox'); // Method to use the language-strings in javascript.
    $PAGE->requires->js(new moodle_url("/mod/cardbox/js/studyview.js"));
    
    // If needed:
//    $params = array($pdfannotator->id);
//    $PAGE->requires->js_init_call('startOverview', $params, true); // 1. name of JS function, 2. parameters.
    
    // 2. Capability check. // TODO
    
    
    
    $imgurls = array();
    
    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'mod_cardbox', 'content', false, 'sortorder', false)) {
            foreach ($files as $file) {
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                // Display the image
                $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path() . ':' . $fileurl->get_port() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();
                $imgurls[] = $download_url;
                break; // TODO: wieder entfernen
//                echo '<a href="' . $download_url . '">' . $file->get_filename() . '</a><br/>';
            }
    } else {
            echo '<p>Please upload an image first</p>';
    }  
    
    // 3. Render the page.
    $renderer = $PAGE->get_renderer('mod_cardbox');
    $studyview = new cardbox_studyview($imgurls, array('Regenpfeifer')); // maybe add parameters
    echo $renderer->cardbox_render_studyview($studyview);
    
}

/* **************************************************** Approve/edit cards **************************************************** */

if ($action === 'review') {
    
    require_once('model/cardbox.class.php');
    
//    cardbox_select_cards_for_practice();
    
    $cardbox = new cardbox_cardboxmodel($cmid);
    
    
    
    
    
}