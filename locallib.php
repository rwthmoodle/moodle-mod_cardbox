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
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function creates a new record in cardbox_topics table.
 * 
 * @global obj $DB
 * @param string $topicname
 * @return int id of the new topic
 */
function cardbox_save_new_topic($topicname, $cardboxid) {

    global $DB;
    $topic = new stdClass();
    $topic->topicname = $topicname;
    $topic->cardboxid = $cardboxid;

    return $DB->insert_record('cardbox_topics', $topic, true);

}
/**
 * Function returns an array of options for the 'select/create a topic' dropdown
 * in the card_form.
 *
 * @global obj $DB
 * @param type $cardboxid
 * @param type $extra
 * @return type
 */
function cardbox_get_topics($cardboxid, $extra = false) {
    
    global $DB;
    $topics = $DB->get_records('cardbox_topics', array('cardboxid' => $cardboxid));
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
function cardbox_save_new_card($cardboxid, $submitbutton = null, $context, $topicid = null) {

    global $DB, $USER;

    $cardrecord = new stdClass();
    $cardrecord->cardbox = $cardboxid;
    $cardrecord->topic = $topicid;
    $cardrecord->author = $USER->id;
    $cardrecord->timecreated = time();
    $cardrecord->timemodified = null;
    if (!empty($submitbutton) && $submitbutton == get_string('saveandaccept', 'cardbox') && has_capability('mod/cardbox:approvecard', $context)) {
        $cardrecord->approved = 1;
        $cardrecord->approvedby = $USER->id;
    } else {
        $cardrecord->approved = 0;
        $cardrecord->approvedby = null;
    }

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

function cardbox_update_cardcontent($cardid, $cardside, $contenttype, $name) {
    
    global $DB;
    
    $existsalready = $DB->record_exists('cardbox_cardcontents', array('card' => $cardid, 'cardside' => $cardside, 'contenttype' => $contenttype));
    
    
}


/**
 * Function updates a card that was edited via the card_form.
 *
 * @global obj $DB
 * @param int $cardid
 * @param int $topicid
 * @return bool whether or not the update was successful
 */
function cardbox_edit_card($cardid, $topicid, $submitbutton = null, $context) {

    global $DB, $USER;
    
    $record = new stdClass();
    $record->id = $cardid;
    $record->topic = $topicid;
    $record->timemodified = time();
    
    if (!empty($submitbutton) && $submitbutton == get_string('saveandaccept', 'cardbox') && has_capability('mod/cardbox:approvecard', $context)) {
        $record->approved = 1;
        $record->approvedby = $USER->id;
    }

    $success = $DB->update_record('cardbox_cards', $record);
    
    if (empty($success)) {
        return false;
    }
    
    $success = $DB->delete_records('cardbox_cardcontents', array('card' => $cardid));
    
    return $success;

}
/**
 * Function deletes a card, its contents and topic.
 * 
 * @global obj $DB
 * @param int $cardid
 * @return boolean
 */
function cardbox_delete_card($cardid) {
    
    global $DB;
    
    // Check whether the card exists.
    $card = $DB->get_record('cardbox_cards', array('id' => $cardid), '*', MUST_EXIST);
    
    if (empty($card)) {
        return false;
    }
    
    // Delete its contents.
    $success = $DB->delete_records('cardbox_cardcontents', array('card' => $cardid));
    
    if (empty($success)) {
        return false;
    }
    
    // Delete its topic if no other card uses it.
    if (!empty($card->topic)) {
        $count = $DB->count_records('cardbox_cards', array('topic' => $card->topic));
        if ($count == 1) {
            $DB->delete_records('cardbox_topics', array('id' => $card->topic));
        }
    }

    // Delete the card itself.
    return $DB->delete_records('cardbox_cards', array('id' => $cardid));
    
}

/**
 * This function checks whether there are new cards available in the DB
 * and if so, adds them to the users virtual cardbox system.
 * 
 * @global obj $DB
 * @global obj $USER
 * @return type
 */
function cardbox_add_new_cards() {
    
    global $DB, $USER;

    $sql2 = "SELECT c.id"
            . " FROM {cardbox_cards} c"
            . " WHERE approved = ? AND NOT EXISTS (SELECT card FROM {cardbox_progress} p WHERE p.userid = ? AND p.card = c.id)";
    $newcards = $DB->get_fieldset_sql($sql2, array('1', $USER->id));

    if (empty($newcards)) {
        return;
    }

    $dataobjects = array();
    foreach ($newcards as $cardid) {
        $dataobjects[] = array('userid' => $USER->id, 'card' => $cardid, 'cardposition' => 0, 'lastpracticed' => null, 'repetitions' => 0);
    }
    $success = $DB->insert_records('cardbox_progress', $dataobjects);
    return $success;

}
/**
 * 
 * @param type $context
 * @param type $itemid
 * @param type $filename
 * @return type
 */
function cardbox_get_download_url($context, $itemid, $filename = null) {
    
    $fs = get_file_storage();
//    $file = $fs->get_file($context, 'mod_cardbox', 'content', $itemid, '/', $filename);

    $files = $fs->get_area_files($context->id, 'mod_cardbox', 'content', $itemid, 'sortorder', false);
    
    
    foreach ($files as $file) { // find better solution than foreach to get the first and only element.
        $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path() . ':' . $fileurl->get_port() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();
        return $download_url;
    }
    
}
/**
 * Function returns the topic of the card, if a topic was selected.
 *
 * @global obj $DB
 * @param int $cardid
 * @return int
 */
function cardbox_get_topic($cardid) { // XXX opject-oriented with card class?
    
    global $DB;
    
    $topic = $DB->get_field('cardbox_cards', 'topic', array('id' => $cardid), IGNORE_MISSING);

    if (empty($topic)) {
        $topic = -1; // no topic selected.
    }
    
    return $topic;

}
/**
 * Function returns the question text (if there is one) of the specified card.
 *
 * @global obj $DB
 * @param int $cardid
 * @return string
 */
function cardbox_get_questiontext($cardid) {

    global $DB;
    $questiontext = $DB->get_field('cardbox_cardcontents', 'content', array('card' => $cardid, 'cardside' => 0, 'contenttype' => 2), IGNORE_MISSING);
    if (empty($questiontext)) {
        $questiontext = '';
    }
    return $questiontext;

}
/**
 * Function returns 1...n answer items belonging to the specified card.
 *
 * @global obj $DB
 * @param type $cardid
 * @return string or array
 */
function cardbox_get_answers($cardid) {
    
    global $DB;
    return $DB->get_fieldset_select('cardbox_cardcontents', 'content', 'card = ? AND cardside = ? AND contenttype = ?', array($cardid, 1, 2));

}
/**
 * Function returns 0...1 image item ids belonging to the specified card.
 *
 * @global obj $DB
 * @param type $cardid
 * @return type
 */
function cardbox_get_image_itemid($cardid) {

    global $DB;
    $imageitemid = $DB->get_field('cardbox_cardcontents', 'id', array('card' => $cardid, 'contenttype' => 1), IGNORE_MISSING);
    return $imageitemid;

}
/**
 * Function converts the timestamp into a human readable format (D. M Y),
 * taking the user's timezone into account.
 *
 * @param type $timestamp
 * @return type
 */
function cardbox_get_user_date($timestamp) {
    return userdate($timestamp, get_string('strftimedate', 'cardbox'), $timezone = 99, $fixday = true, $fixhour = true); // Method in lib/moodlelib.php
}

//function cardbox_get_user_datetime($timestamp) {
//    return userdate($timestamp, $format = '', $timezone = 99, $fixday = true, $fixhour = true); // Method in lib/moodlelib.php
//}
/**
 *
 * @param type $timestamp
 * @return string
 */
function cardbox_get_user_datetime_shortformat($timestamp) {
    $shortformat = get_string('strftimedatetime', 'cardbox'); // Format strings in moodle\lang\en\langconfig.php.
    $userdatetime = userdate($timestamp, $shortformat, $timezone = 99, $fixday = true, $fixhour = true); // Method in lib/moodlelib.php
    return $userdatetime;
}
/**
 * 
 * @param type $carddata
 * @return boolean
 */
function cardbox_is_card_due($carddata) {

    if ($carddata->cardposition == 0) {
        return true;
    } else if ($carddata->cardposition > 5) {
        return false;
    }
    
    $now = new DateTime("now");
    
    $spacing = array();
    $spacing[1] = new DateInterval('P1D');
    $spacing[2] = new DateInterval('P3D');
    $spacing[3] = new DateInterval('P7D');
    $spacing[4] = new DateInterval('P16D');
    $spacing[5] = new DateInterval('P34D');  
        
    $last = new DateTime("@$carddata->lastpracticed");
    $interval = $spacing[$carddata->cardposition];
    $due = $last->add($interval);
    
    if ($due > $now) {
        return false;
        
    } else {
        return true;
    }
}
/**
 * 
 * @param type $dataobject
 * @param type $iscorrect
 * @return type
 */
function cardbox_update_card_progress($dataobject, $iscorrect) {

    global $DB;
    
    // Cards that were answered correctly proceed.
    if ($iscorrect == 1) {
        
        // New cards proceed straight to box two.
        if ($dataobject->cardposition == 0) {
            $dataobject->cardposition = 2;

        // Other cards proceed to the next box.
        } else {
            $dataobject->cardposition = $dataobject->cardposition + 1;
        }
    
    // Cards that were not answered correctly go back to box one or stay there.
    } else {
        $dataobject->cardposition = 1;
    }
    
    $dataobject->lastpracticed = time();
    $dataobject->repetitions = $dataobject->repetitions + 1;

    $success = $DB->update_record('cardbox_progress', $dataobject, false);
    
    return $success;
}

/**
 * This function sends system and/or email notifications to
 * inform students that an already approved card was edited.
 * 
 * @param type $cardbox
 */
function cardbox_send_change_notification($cmid, $cardbox, $cardid) {

    global $CFG, $PAGE;
    require_once($CFG->dirroot . '/mod/cardbox/classes/output/overview.php');

    $context = context_module::instance($cmid);

    $sm = get_string_manager();

    //new cardbox_card($cardid, $cardbox->context, $cmid, false);
    $renderer = $PAGE->get_renderer('mod_cardbox');
    $overview = new cardbox_overview(array($cardid), 0, $context, $cmid, true);

    $recipients = get_enrolled_users($context, 'mod/cardbox:practice');

    foreach ($recipients as $recipient) {
        $message = new \core\message\message();
        $message->component = 'mod_cardbox';
        $message->name = 'changenotification';
        $message->userfrom = core_user::get_noreply_user();
        $message->userto = $recipient;
        $message->subject = $sm->get_string('changenotification:subject', 'cardbox', null, $recipient->lang);
        $message->fullmessage = $sm->get_string('changenotification:message', 'cardbox', null, $recipient->lang) . '<br>' . $renderer->cardbox_render_overview($overview);
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = $sm->get_string('changenotification:message', 'cardbox', null, $recipient->lang) . '<br>' . $renderer->cardbox_render_overview($overview); //'<p>' . $sm->get_string('remindergreeting', 'cardbox', $recipient->username, $recipient->lang) . '</p><p>' . $sm->get_string('remindermessagebody', 'cardbox', null, $recipient->lang) . '</p><p><em>' . $sm->get_string('reminderfooting', 'cardbox', $info, $recipient->lang) . '</em></p>';
        $message->smallmessage = 'small message';
        $message->notification = 1; // For personal messages '0'. Important: the 1 without '' and 0 with ''.
        //$message->contexturl = 'http://GalaxyFarFarAway.com';
        //$message->contexturlname = 'Context name';
//            $message->replyto = "random@example.com";
//                $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
//            $message->set_additional_content('email', $content);
        $message->courseid = $cardbox->course;

        message_send($message);

    }
    
    
}