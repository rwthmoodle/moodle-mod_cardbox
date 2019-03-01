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
function cardbox_get_topics() {
    
    global $DB;
    $topics = $DB->get_records('cardbox_topics', array());
    $options = array(-1 => get_string('notopic', 'cardbox'), 0 => get_string('addnewtopic', 'cardbox'));
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
