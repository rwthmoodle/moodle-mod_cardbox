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

/* *********************************************** Display page Add a flashcard *********************************************** */

if ($action === 'addflashcard') {

    global $USER;
    
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

    // Load any preexisting files into the draftarea.
    //file_prepare_standard_filemanager($data, $filearea, $options, $context, $component, $filearea, $data->id);

    $mform = new mod_cardbox_card_form();
    $mform->set_data($entry);
    
    if ($mform->is_cancelled()) {

        redirect($returnurl);

    // If submitted: get files from filemanager
    } else if ($mform->get_data()) {

        // Get the draft itemid (Files in the drag-and-drop area are automatically saved as drafts in mdl_files even before the form is submitted.)
        $draftitemid = file_get_submitted_draft_itemid('cardimage');
        
        // Copy all the files from the 'real' area, into the draft area
        file_prepare_draft_area($draftitemid, $context->id, $component, $filearea, 0, array('subdirs'=>true));

        if ($draftitemid != null) {

            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'sortorder, id', false)) {            
                echo 'Fehlerbehandlung!';
            } else {
                foreach ($files as $file) {
                    $fileid = $file->get_id();
                    file_save_draft_area_files($draftitemid, $context->id, $component, $filearea, 3, $options);
                    break;
                }

            }

// @Ahmad
//            $fs = get_file_storage();
//            $usercontext = context_user::instance($USER->id);
//            if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'sortorder, id', false)) {            
//                echo 'Fehlerbehandlung!';
//            }
            
//            $options = array('subdirs' => true, 'embed' => false);
//            file_save_draft_area_files($draftitemid, $context->id, $component, $filearea, 0, $options);

// @Ahmad
//            $file = reset($files);
//            file_set_sortorder($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), 1);

            
        }

//        redirect($returnurl);
    
    } else {

        echo $OUTPUT->heading(get_string('titleforaddflashcard', 'cardbox'));
        $mform->display();

    } 
}

if ($action === 'practice') {
    
    echo $OUTPUT->heading(get_string('titleforpractice', 'cardbox'));
    
    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'mod_cardbox', 'content', false, 'sortorder', false)) {
            foreach ($files as $file) {
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                // Display the image
                $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path() . ':' . $fileurl->get_port() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();
            echo '<a href="' . $download_url . '">' . $file->get_filename() . '</a><br/>';
            }
    } else {
            echo '<p>Please upload an image first</p>';
    }  
}