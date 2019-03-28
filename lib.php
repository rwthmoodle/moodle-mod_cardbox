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
//    require_once("$CFG->dirroot/mod/cardbox/locallib.php");
    $cmid = $data->coursemodule;
    $data->timecreated = time();
    $data->timemodified = time();
    cardbox_set_display_options($data);

    $data->id = $DB->insert_record('cardbox', $data);

    // We need to use context now, so we need to make sure all needed info is already in db.
    $DB->set_field('course_modules', 'instance', $data->id, array('id' => $cmid));
//    pdfannotator_set_mainfile($data);

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
//    require_once("$CFG->dirroot/mod/cardbox/locallib.php");
    $cardbox->timemodified = time();
    $cardbox->id = $cardbox->instance;
    $cardbox->revision++;

    cardbox_set_display_options($cardbox); // Can be deleted or extended.

    $DB->update_record('cardbox', $cardbox);

    $completiontimeexpected = !empty($cardbox->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cardbox->coursemodule, 'cardbox', $cardbox->id, $completiontimeexpected);

    return true;
    
}
/**
 * The <modname>_delete_instance function is passed the id of your module which you can use to delete the records from any database tables associated with that id. For example, in the certificate module the id in the certificate table is passed, and then used to delete the certificate from the database, any issues of this certificate and any files associated with it on the filesystem.
 * @param type $cardbox
 */
function cardbox_delete_instance($cardbox) {
    
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
 * Serves the cardbox files.
 *
 * @package  mod_cardbox
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not to force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
//function cardbox_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
//    global $CFG, $DB;
//    require_once("$CFG->libdir/resourcelib.php");
//
//    if ($context->contextlevel != CONTEXT_MODULE) {
//        return false;
//    }
//
//    require_course_login($course, true, $cm);
//    if (!has_capability('mod/cardbox:view', $context)) {
//        return false;
//    }
//
//    if ($filearea !== 'content') { // content
//        // Intro is handled automatically in pluginfile.php.
//        return false;
//    }
//
//    array_shift($args); // Ignore revision - designed to prevent caching problems only.
//
//    $fs = get_file_storage();
//    $relativepath = implode('/', $args);
//    $fullpath = rtrim("/$context->id/mod_cardbox/$filearea/0/$relativepath", '/');
//    do {
//        if (!$file = $fs->get_file_by_hash(sha1($fullpath))) {
//            if ($fs->get_file_by_hash(sha1("$fullpath/."))) {
//                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.htm"))) {
//                    break;
//                }
//                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.html"))) {
//                    break;
//                }
//                if ($file = $fs->get_file_by_hash(sha1("$fullpath/Default.htm"))) {
//                    break;
//                }
//            }
//            $cardbox = $DB->get_record('cardbox', array('id' => $cm->instance), 'id, legacyfiles', MUST_EXIST);
//            if ($cardbox->legacyfiles != RESOURCELIB_LEGACYFILES_ACTIVE) {
//                return false;
//            }
//            if (!$file = resourcelib_try_file_migration('/' . $relativepath, $cm->id, $cm->course, 'mod_cardbox', 'content', 0)) { // image statt content
//                return false;
//            }
//            // File migrate - update flag.
//            $cardbox->legacyfileslast = time();
//            $DB->update_record('cardbox', $cardbox);
//        }
//    } while (false);
//
//    // Should we apply filters?
//    // $mimetype = $file->get_mimetype();
//    $filter = 0;
//    // Finally send the file.
//    send_stored_file($file, null, $filter, $forcedownload, $options);
//}

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
    require_login($course, true, $cm);
    // 4. Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('mod/cardbox:view', $context)) {
        return false;
    }
    // 5. Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = (int)array_shift($args); // The first item in the $args array.
//    if ($itemid != 0) {
//        return false;
//    }
//    / Use the itemid to retrieve any relevant data records and perform any security checks to see if the
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
//    send_stored_file($file, 0, 0, true, $options); // download MUST be forced - security!
}
