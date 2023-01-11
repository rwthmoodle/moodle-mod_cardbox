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

defined('MOODLE_INTERNAL') || die();
/**
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class cardbox_cardcollection {

    private $cardbox;
    private $flashcards; // new/unapproved flashcards.

    public function __construct($cardboxid, $topic = null, $getall = false) {

        global $DB;
        $this->cardbox = $cardboxid;

        $approved = '0';
        if ($getall) {
            $approved = '1';
        }

        if (is_null($topic) || $topic == -1) { // no topic preference.
            $this->flashcards = $DB->get_fieldset_select('cardbox_cards', 'id', 'cardbox = ? AND approved = ?', array($cardboxid, $approved));

        } else if ($topic == 0) { // only cards without a topic.
            $this->flashcards = $DB->get_fieldset_select('cardbox_cards', 'id', 'cardbox = ? AND approved = ? AND topic IS NULL', array($cardboxid, $approved));

        } else { // a specific topic preference.
            $this->flashcards = $DB->get_fieldset_select('cardbox_cards', 'id', 'cardbox = ? AND approved = ? AND topic = ?', array($cardboxid, $approved, $topic));
        }

    }

    /**
     * Function returns all flashcards that have yet to be approved.
     *
     * @return array card ids
     */
    public function cardbox_get_card_list($offset = null) {

        if (!empty($offset)) {
            echo "<span class='notification alert alert-danger alert-block fade in' role='alert' style='display:block'>Something went wrong </span>";
        } else {
            return $this->flashcards;
        }

    }

    public function cardbox_get_first_cardid() {
        return $this->flashcards[0];
    }

    public function cardbox_get_cardcontents_initial() {
        return self::cardbox_get_cardcontents($this->flashcards[0]);
    }

    public static function cardbox_get_cardcontents($cardid) {
        global $DB;
        $cardcontents = $DB->get_records('cardbox_cardcontents', array('card' => $cardid, 'area' => CARD_MAIN_INFORMATION));
        $cardcontexts = $DB->get_records('cardbox_cardcontents', array('card' => $cardid, 'area' => CARD_CONTEXT_INFORMATION));
        return array_merge($cardcontents, $cardcontexts);
    }
    /**
     *
     * @global type $DB
     * @param type $cardid
     * @return type
     */
    public static function cardbox_get_topic($cardid) {
        global $DB;
        $sql = "SELECT t.topicname "
                . "FROM {cardbox_cards} c "
                . "LEFT JOIN {cardbox_topics} t ON c.topic = t.id "
                . "WHERE c.id = ?";
        return $DB->get_field_sql($sql, array($cardid), $strictness = IGNORE_MISSING);
    }

    /**
     *
     * @global type $DB
     * @param type $cardid
     * @return type
     */
    public static function cardbox_get_necessaryanswerslocked($cardid) {
        global $DB;

        $cardboxid = $DB->get_field('cardbox_cards', 'cardbox', array('id' => $cardid), IGNORE_MISSING);
        return $DB->get_field('cardbox', 'necessaryanswerslocked', array('id' => $cardboxid), IGNORE_MISSING);
    }

    /**
     *
     * @global type $DB
     * @param type $cardid
     * @return type
     */
    public static function cardbox_get_question($cardid) {
        global $DB;

        $question = $DB->get_field('cardbox_cardcontents', 'content', array('card' => $cardid, 'area' => CARD_MAIN_INFORMATION, 'cardside' => CARDBOX_CARDSIDE_QUESTION));
        return strip_tags($question);
    }

}
