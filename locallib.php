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
 * This file is used when adding/editing a flashcard to a cardbox.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @authors   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function creates a new record in cardbox_topics table.
 * 
 * @global obj $DB
 * @param string $topicname
 * @return int id of the new topic
 */
function cardbox_save_new_topic($topicname) {

    global $DB;
    $topic = new stdClass();
    $topic->topicname = $topicname;
    return $DB->insert_record('cardbox_topics', $topic, true);

}
/**
 * Function returns an array of options for the 'select/create a topic' dropdown
 * in the card_form.
 *
 * @global obj $DB
 * @return type
 */
function cardbox_get_topics($extra = false) {
    
    global $DB;
    $topics = $DB->get_records('cardbox_topics', array());
    $options = array(-1 => get_string('notopic', 'cardbox'));
    if ($extra) {
        $options = array(-1 => get_string('notopic', 'cardbox'), 0 => get_string('addnewtopic', 'cardbox'));
    } else {
        $options = array(-1 => get_string('notopicpreferred', 'cardbox'));
    }
    foreach ($topics as $topic) {
        $options[$topic->id] = $topic->topicname;
    }
    return $options;
}

/**
 * Function creates a new record in cardbox_cards table.
 *
 * @global obj $DB
 * @global obj $USER
 * @param int $cardboxid
 * @param string $topic
 * @return int
 */
function cardbox_save_new_card($cardboxid, $topicid = null) {

    global $DB, $USER;

    $cardrecord = new stdClass();
    $cardrecord->cardbox = $cardboxid;
    $cardrecord->topic = $topicid;
    $cardrecord->author = $USER->id;
    $cardrecord->timecreated = time();
    $cardrecord->timemodified = null;
    $cardrecord->approvedby = null;
    $cardid = $DB->insert_record('cardbox_cards', $cardrecord, true, false);

    return $cardid;

}
/**
 * Function creates a new record in cardbox_cardcontents table.
 *
 * @global obj $DB
 * @param int $cardid
 * @param int $cardside
 * @param int $contenttype
 * @param string $name
 * @return int
 */
function cardbox_save_new_cardcontent($cardid, $cardside, $contenttype, $name) {

    global $DB;

    $cardcontent = new stdClass();
    $cardcontent->card = $cardid;
    $cardcontent->cardside = $cardside; // 0 for question page
    $cardcontent->contenttype = $contenttype; // 1 for image; // XXX Make dynamic (SQL join, install.php)
    $cardcontent->content = $name; // $file->get_filename();
    $itemid = $DB->insert_record('cardbox_cardcontents', $cardcontent, true);

    return $itemid;

}
/**
 * Function selects a set of 21 flashcards for a practice session.
 *
 * @global obj $DB
 */
//function cardbox_select_cards_for_practice() { // XXX ggf. besser 1x alles holen und dann objektorientiert vorgehen.

    // 1. Add up to 3 new cards to the student's cardbox system (progress table).
    //cardbox_add_new_cards();

    // 2. Access the student's cardbox system.
    
    
    // 3. Select 21 flashcards from the student's cardbox system.
    
    
//}

/**
 * This function checks whether there are new flashcards available and if so,
 * adds up to three of the to the users virtual cardbox.
 *
 * @global obj $DB
 * @global obj $USER
 */
function cardbox_add_new_cards() {
    
    global $DB, $USER;

    // 1. Move three new terms (if there are) to the progress table.
    $sql1 = "SELECT MAX(card)"
            . " FROM {cardbox_progress} p"
            . " WHERE p.userid = ?"; // AND p.lastpracticed IS NOT NULL";

    $lastnew = $DB->get_field_sql($sql1, array($USER->id)); // can return null.

    if (empty($lastnew)) {
        $lastnew = 0;
    }
    
    $sql2 = "SELECT c.id"
            . " FROM {cardbox_cards} c"
            . " WHERE c.id > ? AND approvedby IS NOT NULL";
            //. " LIMIT 3"; // Give up limit?
    $newcards = $DB->get_fieldset_sql($sql2, array($lastnew));
    
    if (empty($newcards)) {
        return;
    }

    $dataobjects = array();
    foreach ($newcards as $cardid) {
        $dataobjects[] = array('userid' => $USER->id, 'card' => $cardid, 'cardposition' => 0, 'lastpracticed' => null, 'repetitions' => 0);
    }
    $DB->insert_records('cardbox_progress', $dataobjects);

}

function cardbox_get_download_url($context, $itemid, $filename = null) {
    
    $fs = get_file_storage();
//    $file = $fs->get_file($context, 'mod_cardbox', 'content', $itemid, '/', $filename);
    
//    print_r($file);

    $files = $fs->get_area_files($context->id, 'mod_cardbox', 'content', $itemid, 'sortorder', false);
    
    
    foreach ($files as $file) { // find better solution than foreach to get the first and only element.
        $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path() . ':' . $fileurl->get_port() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();
        return $download_url;
//        if ($content->cardside == 0) {
//        $this->frontimages[] = array("frontimagesrc" => $download_url);
//        } else {
//            $this->backimages[] = array("backimagesrc" => $download_url);
//        }
//        break;
    }
    
    
}