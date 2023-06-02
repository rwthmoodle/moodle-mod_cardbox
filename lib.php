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

function cardbox_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_COLLABORATION;
        default:
            return null;
    }
}

/**
 * The cardbox_add_instance function is passed the variables from the mod_form.php file
 * as an object when you first create an activity and click submit. This is where you can
 * take that data, do what you want with it and then insert it into the database if you wish.
 * This is only called once when the module instance is first created, so this is where you
 * should place the logic to add the activity.
 *
 * @param type $cardbox
 */
function cardbox_add_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");
    $cmid = $data->coursemodule;
    $data->timecreated = time();
    $data->timemodified = time();
    cardbox_set_display_options($data);

    $data->id = $DB->insert_record('cardbox', $data);

    // We need to use context now, so we need to make sure all needed info is already in db.
    $DB->set_field('course_modules', 'instance', $data->id, array('id' => $cmid));

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'cardbox', $data->id, $completiontimeexpected);

    return $data->id;
}
/**
 * The cardbox_update_instance function is passed the variables from the mod_form.php file
 * as an object whenever you update an activity and click submit. The id of the instance you
 * are editing is passed as the attribute instance and can be used to edit any existing values
 * in the database for that instance.
 *
 * @param type $cardbox
 */
function cardbox_update_instance($cardbox) {

    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");
    $cardbox->timemodified = time();
    $cardbox->id = $cardbox->instance;
    $cardbox->revision++;

    cardbox_set_display_options($cardbox); // Can be deleted or extended.

    $DB->update_record('cardbox', $cardbox);

    $completiontimeexpected = !empty($cardbox->completionexpected) ? $cardbox->completionexpected : null;
    \core_completion\api::update_completion_date_event($cardbox->coursemodule, 'cardbox', $cardbox->id, $completiontimeexpected);

    return true;

}
/**
 * The cardbox__delete_instance function is passed the id of your module which you can use
 * to delete the records from any database tables associated with that id.
 *
 * @param int $cardboxinstanceid
 */
function cardbox_delete_instance($cardboxinstanceid) {

    global $DB;

    if (!$cardbox = $DB->get_record('cardbox', array('id' => $cardboxinstanceid))) {
        return false;
    }
    if (!$cm = get_coursemodule_from_instance('cardbox', $cardboxinstanceid)) {
        return false;
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        return false;
    }

    \core_completion\api::update_completion_date_event($cm->id, 'cardbox', $cardboxinstanceid, null);

    // 1.1 Get all the cards of this cardbox.
    $cards = $DB->get_records('cardbox_cards', ['cardbox' => $cardboxinstanceid]);

    foreach ($cards as $card) {
        // 1.2 Delete all their contents.
        if (!$DB->delete_records('cardbox_cardcontents', ['card' => $card->id]) == 1) {
            return false;
        }
        // 1.3 Delete their references in the students cardboxes.
        if (!$DB->delete_records('cardbox_progress', ['card' => $card->id]) == 1) {
            return false;
        }
    }

    // 1.4 Delete the cards themselves.
    if (!$DB->delete_records('cardbox_cards', ['cardbox' => $cardboxinstanceid]) == 1) {
        return false;
    }

    // 2. Delete any topics affiliated with this cardbox.
    if (!$DB->delete_records('cardbox_topics', ['cardboxid' => $cardboxinstanceid]) == 1) {
        return false;
    }

    // 3. Delete the cardbox instance from the cardbox table of the plugin.
    if (!$DB->delete_records('cardbox', ['id' => $cardboxinstanceid]) == 1) {
        return false;
    }

    return true;

}

/**
 * Updates display options based on form input.
 *
 * Shared code used by pdfannotator_add_instance and pdfannotator_update_instance.
 * keep it, if you want defind more disply options
 * @param object $data Data object
 */
function cardbox_set_display_options($data) {
    $displayoptions = array();
    $displayoptions['printintro'] = (int) !empty($data->printintro);
    $data->displayoptions = serialize($displayoptions);
}


/**
 * Serve the files from the MYPLUGIN file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function mod_cardbox_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB;
    // 1. Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }
    // 2. Make sure the filearea is one of those used by the plugin.
    if ($filearea != 'content') {
        return false;
    }
    // 3. Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    // Disabled, so that students can see images in changenotification emails:

    // 4. Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('mod/cardbox:view', $context)) {
        return false;
    }
    // 5. Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = (int)array_shift($args); // The first item in the $args array.
    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.

    // 6. Extract the filename / filepath from the $args array.
    $filename = array_pop($args);
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }
    // 7. Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_cardbox', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }
    // 8. We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}
