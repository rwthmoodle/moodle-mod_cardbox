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
define('CARD_ANSWERSUGGESTION_INFORMATION', 3);
define ('LONG_DESCRIPTION', 1);
define ('SHORT_DESCRIPTION', 0);
require_once($CFG->dirroot.'/mod/cardbox/constants.php');
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
    $exists = $DB->record_exists_select(
        'cardbox_topics',
        'TRIM(topicname) = :topicname AND cardboxid = :cardboxid',
        [
            'topicname' => $topic->topicname,
            'cardboxid' => $topic->cardboxid
        ]
    );
    if ($exists) {
        throw new moodle_exception('topicalreadyexists', 'cardbox');
    }
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
function cardbox_save_new_card($cardboxid, $context, $accept = false, $topicid = null, $necessaryanswers = 0, $disableautocorrect = 0) {

    global $DB, $USER;

    $cardrecord = new stdClass();
    $cardrecord->cardbox = $cardboxid;
    $cardrecord->topic = $topicid;
    $cardrecord->author = $USER->id;
    $cardrecord->timecreated = time();
    $cardrecord->timemodified = null;
    if ($accept) {
        $cardrecord->approved = 1;
        $cardrecord->approvedby = $USER->id;
    } else {
        $cardrecord->approved = 0;
        $cardrecord->approvedby = null;
    }
    $cardrecord->necessaryanswers = $necessaryanswers;
    $cardrecord->disableautocorrect = $disableautocorrect;
    $cardid = $DB->insert_record('cardbox_cards', $cardrecord, true, false);

    $event = \mod_cardbox\event\card_created::create(['context' => $context,  'objectid' => $cardid]);
    $event->trigger();

    if ($accept) {
        $event = \mod_cardbox\event\card_accepted::create(['context' => $context,  'objectid' => $cardid]);
        $event->trigger();
    }

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
function cardbox_save_new_cardcontent($cardid, $cardside, $contenttype, $name, $area = 0) {

    global $DB;

    $cardcontent = new stdClass();
    $cardcontent->card = $cardid;
    $cardcontent->cardside = $cardside;
    $cardcontent->contenttype = $contenttype;
    $cardcontent->area = $area;
    $cardcontent->content = $name;
    $itemid = $DB->insert_record('cardbox_cardcontents', $cardcontent, true);

    return $itemid;

}

/**
 * Function updates a card that was edited via the card_form.
 *
 * @global obj $DB
 * @param int $cardid
 * @param int $topicid
 * @return bool whether or not the update was successful
 */
function cardbox_edit_card($cardid, $topicid, $context, $necessaryanswers, $disableautocorrect, $accept = false) {
    global $DB, $USER;

    $record = new stdClass();
    $record->id = $cardid;
    $record->topic = $topicid;
    $record->timemodified = time();
    if ($accept) {
        $record->approved = 1;
        $record->approvedby = $USER->id;
    }
    $record->necessaryanswers = $necessaryanswers;
    $record->disableautocorrect = $disableautocorrect;
    $success = $DB->update_record('cardbox_cards', $record);

    if (empty($success)) {
        return false;
    }

    $success = $DB->delete_records('cardbox_cardcontents', array('card' => $cardid));

    $event = \mod_cardbox\event\card_updated::create(['context' => $context,  'objectid' => $cardid]);
    $event->trigger();

    if ($accept) {
        $event = \mod_cardbox\event\card_accepted::create(['context' => $context,  'objectid' => $cardid]);
        $event->trigger();
    }

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
function cardbox_add_new_cards($cardboxid, $topic) {

    global $DB, $USER;

    $sql2 = "SELECT c.id"
            . " FROM {cardbox_cards} c"
            . " WHERE c.cardbox = :cbid AND c.approved = :appr"
            . " AND NOT EXISTS (SELECT card FROM {cardbox_progress} p WHERE p.userid = :uid AND p.card = c.id)";
    $params = ['cbid' => $cardboxid, 'appr' => '1', 'uid' => $USER->id];
    $newcards = $DB->get_fieldset_sql($sql2, $params);

    if (empty($newcards)) {
        return;
    }

    if ($topic != -1) {
        $cards = [];
        foreach ($newcards as $card) {
            if ($DB->get_record_select('cardbox_cards', 'id =' . $card->id, null, 'topic') === $topic) {
                $cards[] = $card;
            }
        }
        $newcards = $cards;
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

    $files = $fs->get_area_files($context->id, 'mod_cardbox', 'content', $itemid, 'sortorder', false);

    foreach ($files as $file) { // find better solution than foreach to get the first and only element.
        $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                                                   $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        $downloadurl = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path() .
                           ':' . $fileurl->get_port() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();
        return $downloadurl;
    }

}
/**
 * Function returns the topic of the card, if a topic was selected.
 *
 * @global obj $DB
 * @param int $cardid
 * @return int
 */
function cardbox_get_topic($cardid) {

    global $DB;

    $topic = $DB->get_field('cardbox_cards', 'topic', array('id' => $cardid), IGNORE_MISSING);

    if (empty($topic)) {
        $topic = -1; // No topic selected.
    }

    return $topic;

}
/**
 * Function returns the amount of necessary answers of the card.
 *
 * @global obj $DB
 * @param int $cardid
 * @return int
 */
function cardbox_get_necessaryanswers($cardid) {
    global $DB;

    $necessaryanswers = $DB->get_field('cardbox_cards', 'necessaryanswers', array('id' => $cardid), IGNORE_MISSING);

    return $necessaryanswers;

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
    $questiontext = $DB->get_field('cardbox_cardcontents', 'content',
        ['card' => $cardid, 'cardside' => CARDBOX_CARDSIDE_QUESTION, 'contenttype' => CARDBOX_CONTENTTYPE_TEXT,
        'area' => CARD_MAIN_INFORMATION], IGNORE_MISSING);
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
    return $DB->get_fieldset_select('cardbox_cardcontents', 'content',
        'card = :cardid AND cardside = :cardside AND contenttype = :contenttype AND area = :area',
        ['cardid' => $cardid, 'cardside' => CARDBOX_CARDSIDE_ANSWER, 'contenttype' => CARDBOX_CONTENTTYPE_TEXT,
        'area' => CARD_MAIN_INFORMATION]);
}

/**
 * Function returns 1...n answer items belonging to the specified card.
 *
 * @global obj $DB
 * @param type $cardid
 * @return string or array
 */
function cardbox_get_notapproved_answers($cardid) {
    global $DB;
    return $DB->get_fieldset_select('cardbox_cardcontents', 'content',
        'card = :cardid AND cardside = :cardside AND contenttype = :contenttype AND area = :area',
        ['cardid' => $cardid, 'cardside' => CARDBOX_CARDSIDE_ANSWER, 'contenttype' => CARDBOX_CONTENTTYPE_TEXT,
        'area' => CARD_ANSWERSUGGESTION_INFORMATION]);
}

/**
 * Function returns the context belonging to the specified question if set.
 *
 * @global obj $DB
 * @param type $cardid
 * @return string or array
 */
function cardbox_get_questioncontext($cardid) {

    global $DB;
    $context = $DB->get_field('cardbox_cardcontents', 'content', ['card' => $cardid, 'cardside' => CARDBOX_CARDSIDE_QUESTION,
     'area' => CARD_CONTEXT_INFORMATION], IGNORE_MISSING);
    if (empty($context)) {
        $context = '';
    }
    return $context;

}

/**
 * Function returns the context belonging to the specified answer if set.
 *
 * @global obj $DB
 * @param type $cardid
 * @return string or array
 */
function cardbox_get_answercontext($cardid) {

    global $DB;
    $context = $DB->get_field('cardbox_cardcontents', 'content', ['card' => $cardid, 'cardside' => CARDBOX_CARDSIDE_ANSWER,
     'area' => CARD_CONTEXT_INFORMATION], IGNORE_MISSING);
    if (empty($context)) {
        $context = '';
    }
    return $context;

}

/**
 * Function returns the status belonging to the specified card.
 *
 * @global obj $DB
 * @param type $cardid
 * @return string or array
 */
function cardbox_get_status($cardid, $userid) {

    global $DB;
    $status = $DB->get_field('cardbox_progress', 'cardposition', array('card' => $cardid, 'userid' => $userid), IGNORE_MISSING);
    if ($status === "0" || $status === false) {
        $status = get_string('newcard', 'cardbox');
    }
    if ($status === "6") {
        $status = get_string('knowncard', 'cardbox');
    }
    return $status;

}

/**
 * Function returns true if the specified card card is approved.
 *
 * @global obj $DB
 * @param type $cardid
 * @return string or array
 */
function cardbox_card_approved($cardid) {

    global $DB;
    $status = $DB->get_field('cardbox_cards', 'approved', array('id' => $cardid), IGNORE_MISSING);
    if ($status === "0") {
        return false;
    } else {
        return true;
    }
}

function cardbox_get_absolute_cardcounts_per_deck($cardboxid) {
    global $DB;
    $cardsperdeck = $DB->get_records_sql(
                        'SELECT cardposition, count(card) AS cardcount
                        FROM {cardbox_progress}
                        where card in (select id from {cardbox_cards} where cardbox = :cardboxid) GROUP by cardposition',
                        ['cardboxid' => $cardboxid]);
    $cardsperdeck = array_column($cardsperdeck, 'cardcount', 'cardposition');

    for ($i = 0; $i < 7; ++$i) {
        if (!array_key_exists($i, $cardsperdeck)) {
            $cardsperdeck[$i] = 0;
        }
    }
    return $cardsperdeck;
}

function cardbox_get_average_cardcounts_per_deck($cardboxid) {
    global $DB;
    $absolutes = cardbox_get_absolute_cardcounts_per_deck($cardboxid);
    $practisingstudentcount = $DB->count_records_sql(
                                'SELECT count(distinct userid)
                                FROM {cardbox_progress}
                                where card in (select id from {cardbox_cards} where cardbox = :cardboxid)',
                                ['cardboxid' => $cardboxid]);
    $averages = [];
    foreach ($absolutes as $position => $absolute) {
        $averages[$position] = $absolute / $practisingstudentcount;
    }
    return $averages;
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
    $imageitemid = $DB->get_field('cardbox_cardcontents', 'id', ['card' => $cardid, 'contenttype' => CARDBOX_CONTENTTYPE_IMAGE], IGNORE_MISSING);
    return $imageitemid;

}
/**
 * Function returns the imagedescription belonging to the specified image if set.
 *
 * @global obj $DB
 * @param type $cardid
 * @return string or array
 */
function cardbox_get_imagedescription($cardid) {

    global $DB;
    $imagedescription = $DB->get_field('cardbox_cardcontents', 'content', ['card' => $cardid, 'cardside' => CARDBOX_CARDSIDE_QUESTION,
     'area' => CARD_IMAGEDESCRIPTION_INFORMATION], IGNORE_MISSING);
    if (empty($imagedescription)) {
        $imagedescription = '';
    }
    return $imagedescription;

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

/**
 * Function converts the timestamp into a human readable format (D. M),
 * taking the user's timezone into account.
 *
 * @param type $timestamp
 * @return type
 */
function cardbox_get_user_date_short($timestamp) {
    return userdate($timestamp, get_string('strftimedateshortmonthabbr', 'cardbox'), $timezone = 99, $fixday = true, $fixhour = true); // Method in lib/moodlelib.php
}

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
        } else {
            // Other cards proceed to the next box.
            $dataobject->cardposition = $dataobject->cardposition + 1;
        }
    } else {
        // Cards that were not answered correctly go back to box one or stay there.
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

    global $CFG, $DB, $PAGE;
    require_once($CFG->dirroot . '/mod/cardbox/classes/output/overview.php');

    $context = context_module::instance($cmid);

    $sm = get_string_manager();

    $topicid = $DB->get_field('cardbox_cards', 'topic', ['id' => $cardid], MUST_EXIST);
    $renderer = $PAGE->get_renderer('mod_cardbox');
    $overview = new cardbox_overview(array($cardid), 0, $context, $cmid, $cardid, $topicid, $sort, $deck, true);

    $recipients = get_enrolled_users($context, 'mod/cardbox:practice');

    foreach ($recipients as $recipient) {
        $modinfo = get_fast_modinfo($cardbox->course, $recipient->id);
        $cm = $modinfo->get_cm($cmid);
        $info = new \core_availability\info_module($cm);
        $information = '';
        if (!$info->is_available($information, false, $recipient->id)) {
            continue;
        }
        $message = new \core\message\message();
        $message->component = 'mod_cardbox';
        $message->name = 'changenotification';
        $message->userfrom = core_user::get_noreply_user();
        $message->userto = $recipient;
        $message->subject = $sm->get_string('changenotification:subject', 'cardbox', null, $recipient->lang);
        $message->fullmessage = $sm->get_string('changenotification:message', 'cardbox', null, $recipient->lang) . '<br>' . $renderer->cardbox_render_overview($overview);
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = $sm->get_string('changenotification:message', 'cardbox', null, $recipient->lang) . '<br>' . $renderer->cardbox_render_overview($overview);
        $message->smallmessage = 'small message';
        $message->notification = 1; // For personal messages '0'. Important: the 1 without '' and 0 with ''.
        $message->courseid = $cardbox->course;

        message_send($message);

    }

}
/**
 * Import cards specified in CSV
 *
 * @param array $importedcards
 * @param int $cardboxid
 */
function cardbox_create_cards_from_import (array $importedcards, int $cardboxid) {
    global $DB, $USER;    
    // First create topics if it doesnt exist
    $topics = []; 
    foreach ($importedcards as $card) {
        $trimmedTopic = trim($card['topic']);
        $params = ['topicname' => $trimmedTopic, 'cardboxid' => $cardboxid];
        $matchingtopic = $DB->get_record('cardbox_topics', $params, 'id', IGNORE_MULTIPLE);
        if ($matchingtopic !== false) {
            $topics[$matchingtopic->id] = $trimmedTopic;
        } else {
            $data = new stdClass();
            $data->topicname = $trimmedTopic;
            $data->cardboxid = $cardboxid;
            $topicid = $DB->insert_record('cardbox_topics', $data);
            $topics[$topicid] = $trimmedTopic;
        }
        $cardentry = new stdClass();
        $cardentry->cardbox = $cardboxid;
        $cardentry->topic = array_search($trimmedTopic, $topics, true);
        $cardentry->author = (int)$USER->id;
        $cardentry->timecreated = time();
        $cardentry->timemodified = null;
        $cardentry->approved = CARD_APPROVED;
        $cardentry->approvedby = (int)$USER->id;
        $cardentry->necessaryanswers = CARDBOX_EVALUATE_ALL;
        $cardentry->disableautocorrect = (int)$card['acdisable'];
        $cardid = $DB->insert_record('cardbox_cards', $cardentry);
        foreach ($card as $column => $value) {
            $cardcontententry = new stdClass();
            $cardcontententry->card = $cardid;
            $cardcontententry->contenttype = CARDBOX_CONTENTTYPE_TEXT;
            // Flag to track if we should insert
            $shouldInsert = false;
            switch (strtolower($column)) {
                case 'ques':
                    $cardcontententry->cardside = CARDBOX_CARDSIDE_QUESTION;
                    $cardcontententry->area = CARD_MAIN_INFORMATION;
                    $cardcontententry->content = $value;
                    $shouldInsert = true;
                    break;
                case 'qcontext':
                    $cardcontententry->cardside = CARDBOX_CARDSIDE_QUESTION;
                    $cardcontententry->area = CARD_CONTEXT_INFORMATION;
                    $cardcontententry->content = $value;
                    $shouldInsert = true;
                    break;
                case 'acontext':
                    $cardcontententry->cardside = CARDBOX_CARDSIDE_ANSWER;
                    $cardcontententry->area = CARD_CONTEXT_INFORMATION;
                    $cardcontententry->content = $value;
                    $shouldInsert = true;
                    break;
                default:
                    if (str_starts_with(strtolower($column), 'ans')) {
                        $cardcontententry->cardside = CARDBOX_CARDSIDE_ANSWER;
                        $cardcontententry->area = CARD_MAIN_INFORMATION;
                        $cardcontententry->content = $value;
                        $shouldInsert = true;
                    }
                    break;
            }
            // Only insert if we processed a valid column
            if ($shouldInsert) {
                $cardcontentid = $DB->insert_record('cardbox_cardcontents', $cardcontententry);
            }
        }
    }
}
function cardbox_import_validate_columns(array $filecolumns, int $descriptiontype) {
    $errors = [];
    $processed = [];
    $filecolumns = array_map('strtolower', $filecolumns);
    if (empty($filecolumns)) {
        $errors[] = get_string('cannotreadtmpfile', 'error');
    }
    if (count($filecolumns) < 2) {
        $errors[] = get_string('csvfewcolumns', 'error');
    }
    if (!in_array('ques', $filecolumns)) {
        $errors[] = 'ERR: '.get_string('qfieldmissing', 'cardbox');
    }
    if (!in_array('ans', $filecolumns) && empty(preg_grep('/^ans[0-9]*$/', $filecolumns))) {
        $errors[] = 'ERR: '.get_string('afieldmissing', 'cardbox');
    }
    $allowed = ['ques', 'ans', 'acontext', 'qcontext', 'topic', 'acdisable'];
    $allowedwithmeaning = [
        'ques' => get_string('ques', 'cardbox'),
        'ans' => get_string('ans', 'cardbox'),
        'acontext' => get_string('acontext', 'cardbox'),
        'qcontext' => get_string('qcontext', 'cardbox'),
        'topic' => get_string('topic', 'cardbox'),
        'acdisable' => get_string('acdisable', 'cardbox'),
    ];
    foreach ($filecolumns as $key => $column) {
        if (cardbox_string_starts_with($column, 'ans')) { // Replace with str_starts_with in PHP 8.0.
            array_push($allowed, $column);
        }
    }
    foreach ($filecolumns as $filecolumn) {
        if (in_array($filecolumn, $allowed) ) {
            if (!in_array($filecolumn, $processed)) {
                array_push($processed, $filecolumn);
            } else if (in_array($filecolumn, $processed)) {
                $errors[] = get_string('duplicatefieldname', 'error', $filecolumn);
            }
        } else {
            if ($descriptiontype == LONG_DESCRIPTION) {
                $errstr = get_string('invalidfieldname', 'error', $filecolumn).'<br> '.get_string('allowedcolumns', 'cardbox').'<ul>';
                foreach ($allowedwithmeaning as $shortname => $meaning) {
                    $errstr .= '<li><b>'.$shortname.'</b> => '.$meaning.'</li>';
                }
                $errstr .= '</ul>';
                $errors[] = $errstr;
            } else {
                $errors[] = get_string('invalidfieldname', 'error', $filecolumn);
            }

        }
    }
    return [$errors];
}

function cardbox_import_validate_row(int $atleastoneanswer, array $rowcols) {
    $matches  = preg_grep ('/^ans[0-9]*$/', array_keys($rowcols));
    $errors = array();
    foreach ($matches as $match) {
        if (!is_null($rowcols[$match])) {
            if (!($rowcols[$match] == "")) {
                $atleastoneanswer++;
            }
        }
    }
    if (is_null($rowcols['ques']) || $rowcols['ques'] == "") {
        $errors[] = get_string('qmissing', 'cardbox');
    }
    if ($atleastoneanswer == 0) {
        $errors[] = get_string('amissing', 'cardbox');
    }
    return $errors;
}

function cardbox_string_starts_with($fullvalue, $searchvalue) {
    return substr_compare($fullvalue, $searchvalue, 0, strlen($searchvalue)) === 0;
}
#----------------- NEW FUNCTIONS ----------------------------------------#
##---------------- ADD CARDS -------------------------------------------##
/**
 * Function to add cards
 *
 * @param stdClass $formdata
 * @param context_module $context
 */
function add_card_to_instance(stdClass $formdata, context_module $context, int $cardboxid, int $cmid) {
    global $DB;
    #--------------- SAVE CARD ---------------------------------------#
    $accept = !empty($formdata->saveandaccept) && has_capability('mod/cardbox:approvecard', $context);
    $topic = assign_topic_to_card(
        !empty($formdata->newtopic) ? NEW_TOPIC_CREATED : $formdata->topic,
        $cardboxid,
        $formdata->newtopic ?? null
    );
    $howmanyansreqd = check_no_of_answers($cardboxid, $formdata->answers);
    $enabledautocheck = property_exists($formdata, 'disableautocorrect')? (int)$formdata->disableautocorrect : 0;
    $cardid = cardbox_save_new_card($cardboxid, $context, $accept, $topic, $howmanyansreqd, $enabledautocheck);
    #--------------- SAVE CARD CONTENT ---------------------------------------#
    $message = '';
    $cardcontentcreated = create_card_content($formdata, $cardid, $context);
    if ($accept) {
        $message = get_string('success:addandapprovenewcard', 'cardbox');
    } else {
        $message = get_string('success:addnewcard', 'cardbox');
    }
    $redirecturl = $actionurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'addflashcard'));
    if ($cardcontentcreated) {
        redirect($redirecturl, $message, null, \core\output\notification::NOTIFY_INFO);
    } else {
        $message = get_string('error:createcard:inconclusive', 'cardbox');
        redirect($redirecturl, $message, null, \core\output\notification::NOTIFY_ERROR);
    }

}
/**
 * Function to assign topic to card
 *
 * @param int $topic
 * @param int $cardboxid
 */
function assign_topic_to_card(
    int $topic,
    ?int $cardboxid = null,
    ?string $newtopic = null
) {
    switch ($topic) {
        case NULL_TOPIC: // Card belongs to no topic.
            return null;
        case NEW_TOPIC_CREATED: // Card belongs to a new topic that is to be created.
            if (!empty($newtopic)) {
                return cardbox_save_new_topic($newtopic, $cardboxid);
            }
            return null;
        default: // Card belongs to an already existing topic.
            return $topic;
    }
}
/**
 * Function to assign how many answers are required
 *
 * @param int $cardboxid
 * @param int $formanswer
 */
function check_no_of_answers(int $cardboxid, int $formanswer) {
    global $DB;
    $necessaryanswerslocked = $DB->get_field(
        'cardbox',
        'necessaryanswerslocked',
        array('id' => $cardboxid),
        IGNORE_MISSING
    );
    $noofanswers = ($necessaryanswerslocked == CANNOT_CHANGE_NO_OF_NECESSARY_ANSWERS)
    ? $necessaryanswerslocked
    : $formanswer;
    return $noofanswers;
}
/**
 * Function to add card contents
 *
 * @param stdClass $formdata
 * @param int $cardid
 */
function create_card_content(stdClass $formdata, int $cardid, context_module $context) {
    // Main question.
    if (!empty(trim($formdata->question['text']))) {
        cardbox_save_new_cardcontent($cardid, CARDBOX_CARDSIDE_QUESTION, CARDBOX_CONTENTTYPE_TEXT, $formdata->question['text'], CARD_MAIN_INFORMATION);
    }
    // Question context.
    if (!empty(trim($formdata->questioncontext['text']))) {
        cardbox_save_new_cardcontent($cardid, CARDBOX_CARDSIDE_QUESTION, CARDBOX_CONTENTTYPE_TEXT,
                                        $formdata->questioncontext['text'], CARD_CONTEXT_INFORMATION);
    }
    // Image
    $imgoptions = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 3,
                          'accepted_types' => array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'svg'), 'return_types' => 1 | 2);
    cardbox_save_filemanager_content(
        'cardimage',
        $context,
        'mod_cardbox',
        'content',
        $cardid,
        CARDBOX_CONTENTTYPE_IMAGE,
        CARD_MAIN_INFORMATION,
        $formdata->imagedescription ?? null,
        CARD_IMAGEDESCRIPTION_INFORMATION,
        $imgoptions
    );
    // Audio
    $audiooptions = array(
        'subdirs' => 0,
        'maxbytes' => 0,
        'areamaxbytes' => 10485760,
        'maxfiles' => 1,
        'accepted_types' => array(
            'mp3',
            'wav',
            'ogg',
            'm4a',
            'aac',
            'flac'
        ),
        'return_types' => FILE_INTERNAL | FILE_EXTERNAL
    );
    cardbox_save_filemanager_content(
        'cardsound',
        $context,
        'mod_cardbox',
        'content',
        $cardid,
        CARDBOX_CONTENTTYPE_AUDIO,
        CARD_MAIN_INFORMATION,
        null,
        null,
        $audiooptions
    );
    // Answer(s)
    for ($i = 1; $i <= 10; $i++) {
        $answer = 'answer'. $i;
        if (!property_exists($formdata, $answer)) {
            continue;
        }
        $answertext = str_replace('&nbsp;', ' ', $formdata->{$answer}['text']);
        if (trim(strip_tags($answertext)) === '') {
            continue;
        }
        cardbox_save_new_cardcontent(
            $cardid,
            CARDBOX_CARDSIDE_ANSWER,
            CARDBOX_CONTENTTYPE_TEXT,
            $answertext,
            CARD_MAIN_INFORMATION
        );
    }
    // Answer context.
    if (!empty(trim($formdata->answercontext['text']))) {
        cardbox_save_new_cardcontent($cardid, 1, CARDBOX_CONTENTTYPE_TEXT,
                                        $formdata->answercontext['text'], CARD_CONTEXT_INFORMATION);
    }
    return 1;
}
/**
 * Function to save file manager files
 *
 * @param string $formfield
 * @param context_module  $context,
 * @param string $component,
 * @param string $filearea,
 * @param int $cardid,
 * @param int $filetype,
 * @param int $contenttype,
 * @param string $description = null,
 * @param int  $descriptiontype = null,
 * @param array $options
 */
function cardbox_save_filemanager_content(
    $formfield,
    $context,
    $component,
    $filearea,
    $cardid,
    $filetype,
    $contenttype,
    $description = null,
    $descriptiontype = null,
    $options = []
) {
    global $USER;

    $draftitemid = file_get_submitted_draft_itemid($formfield);
    if (!$draftitemid) {
        return;
    }

    $fs = get_file_storage();
    $usercontext = context_user::instance($USER->id);

    $files = $fs->get_area_files(
        $usercontext->id,
        'user',
        'draft',
        $draftitemid,
        'sortorder, id',
        false
    );

    if (empty($files)) {
        return;
    }

    $file = reset($files);

    $itemid = cardbox_save_new_cardcontent(
        $cardid,
        0,
        $filetype,
        $file->get_filename(),
        $contenttype
    );

    file_save_draft_area_files(
        $draftitemid,
        $context->id,
        $component,
        $filearea,
        $itemid,
        $options
    );

    if ($description !== null && trim($description) !== '') {
        cardbox_save_new_cardcontent(
            $cardid,
            0,
            $filetype,
            $description,
            $descriptiontype
        );
    } else {
        throw new moodle_exception(get_string('error:imagedescription', 'cardbox'));
    }
}
##---------------- OVERVIEW -------------------------------------------##
/**
 * Function to sort cards on overview page
 *
 * @param int $sort
 * @param array $list
 * @param cardbox_cardcollection $collection
 * @param int $deck,
 * @param context_module $context
 * @param int $cardboxid
 * @return array $list
 */
function sort_cards_on_overview(int $sort, array $list, cardbox_cardcollection $collection, int $deck, context_module $context, int $cardboxid) {
    switch($sort) {
        case SORT_CREATIONDATE_ASC:
            sort($list);
            break;
        case SORT_CREATIONDATE_DESC:
            rsort($list);
            break;
        default:
            $questions = [];
            for ($i = 0; $i < count($list); $i++) {
                $questions[$list[$i]] = $collection->cardbox_get_question($list[$i]);
            }

            if ($sort === SORT_ALPHABETIC_ASC) {
                asort($questions, SORT_STRING);
            } else {
                arsort($questions, SORT_STRING);
            }
            $index = 0;
            foreach ($questions as $key => $value) {
                $list[$index] = $key;
                $index++;
            }

    }
    if ($deck != SHOW_CARDS_OF_ALL_DECKS) {
        $list = filter_cards_deckwise_overview($deck, $context, $list, $cardboxid);
    }
    return $list;

}
/**
 * Function to sort cards on overview page
 *
 * @param int $sort
 * @param array $list
 * @param cardbox_cardcollection $collection
 * @return array $list
 */
function filter_cards_deckwise_overview(int $deck, context_module $context, array $list, int $cardboxid) {
    $allowedtoedit = false;
    $seestatus = false;
    if (has_capability('mod/cardbox:approvecard', $context)) {
        $allowedtoedit = true;
    } else {
        $allowedtoedit = false;
    }

    if (has_capability('mod/cardbox:seestatus', $context)) {
        $seestatus = true;
    } else {
        $seestatus = false;
    }
    $filtereddeck = array();
    $index = 0;
    foreach ($list as $flashcard) {
        $card = new cardbox_card($flashcard, $context, $cardboxid, $allowedtoedit, $seestatus);
        $card->cardbox_getcarddeck($flashcard, $allowedtoedit);
        if ($card->cardbox_getcarddecknumber() == ($deck + 1)) {
            $filtereddeck[$index] = $flashcard;
            $index++;
        }
    }
    $list = $filtereddeck;
    return $list;
}
/**
 * Function to search cards
 *
 * @param string $search
 * @param array $list
 * @return array $list
 */
function get_search_result_overview(string $search, array $list) {
    global $DB;
    $results = [];
    foreach ($list as $entry) {
        $cardcontents = $DB->get_records('cardbox_cardcontents', ['card' => $entry]);
        foreach ($cardcontents as $cardcontent) {
            if (stripos($cardcontent->content, $search) !== false) {
                if (!in_array($cardcontent->card, $results, true)) {
                    $results[] = $entry;
                }
            }
        }
    }
    return $results;
}
/*----------------------------------- I M P O R T - C A R D S -------------------------------*/
/**
 * Show import errors
 *
 * @param csv_import_reader $cir
 * @param array $errarr
 */
function show_import_errors (csv_import_reader $cir) {
    $csvcolumns = $cir->get_columns();
    $errorflag = NO_ERROR_AT_IMPORT;
    $erroroutput = '';
    $columnexceptions = cardbox_import_validate_columns($csvcolumns, LONG_DESCRIPTION);
    if (!empty($columnexceptions[0]) && !empty($columnexceptions[1])) {
        if (!empty($columnexceptions[0])) {
            $erroroutput .= '<div class="alert alert-danger" role="alert">Error(s)<ul>';
            foreach ($columnexceptions[0] as $error) {
                $erroroutput .= '<li>'.$error.'</li>';
            }
            $erroroutput .= '</ul></div>';
            if ($errorflag !== ERRORS_AT_IMPORT) {
                $errorflag = ERRORS_AT_IMPORT;
            }
        } else {
            $erroroutput .= '<div class="alert alert-warning" role="alert">Warning(s)<ul>';
            foreach ($columnexceptions[1] as $warning) {
                $erroroutput .= '<li>'.$warning.'</li>';
            }
            $erroroutput .= '</ul></div>';
            if ($errorflag !== ERRORS_AT_IMPORT) {
                $errorflag = WARNINGS_AT_IMPORT;
            } 
        }
        if ($errorflag == ERRORS_AT_IMPORT) {
            return [ERRORS_AT_IMPORT => $erroroutput];
        } else {
            return [WARNINGS_AT_IMPORT => $erroroutput];
        }
    } else {
        return [NO_ERROR_AT_IMPORT => null];
    }
}
/**
 * Build array out of imported cards
 *
 * @param csv_import_reader $cir
 * @param array $cards
 */
function generate_imported_cards_arr (csv_import_reader $cir) {
    $cards = [];
    $cir->init();
    $singleanswer = true;
    $csvcolumns = $cir->get_columns();
    $checkerrors = show_import_errors($cir);
    if (!array_key_exists(NO_ERROR_AT_IMPORT, $checkerrors)) {
        $card ['FAIL'] = reset($checkerrors);
    } else {
        $ans_columns_count = count(array_filter($csvcolumns, fn($col) => str_starts_with($col, 'ans')));
        if ($ans_columns_count > 1) {
            $singleanswer = false;
        }
        $linecount = 1;
        while ($line = $cir->next()) {
            $eachcard = [];
            for ($i=0; $i < count($csvcolumns); $i++) {
                $arrkey = $csvcolumns[$i];
                $arrval = $line[$i];     
                $eachcard[$arrkey] = $arrval;
            }
            $cards[$linecount] = $eachcard;
            $linecount ++;
        }
    }
    return $cards;
}
function fetch_card_values_for_editing($from, $cmid, $cardid) {
    global $DB;
    $answers = [];
    if ($from === 'review') {
        $returnurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'review'));
    } else {
        $returnurl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid, 'action' => 'overview'));
    }
    $topic = cardbox_get_topic($cardid);
    $answers = cardbox_get_answers($cardid);
    $answersnotapproved = cardbox_get_notapproved_answers($cardid);
    $answers = array_merge($answers, $answersnotapproved);
    $answercount = count($answers);
    $necessaryanswers = cardbox_get_necessaryanswers($cardid);
    $disableautocorrect = $DB->get_field('cardbox_cards', 'disableautocorrect', array('id' => $cardid), IGNORE_MISSING);

}