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
    
    public function __construct($cardboxid, $getall = false) {

        global $DB;
        $this->cardbox = $cardboxid;
        $this->flashcards = $DB->get_fieldset_select('cardbox_cards', 'id', 'cardbox = ? AND approved = ?', array($cardboxid, '0'));

    }
    
    /**
     * Function returns all flashcards that have yet to be approved.
     *
     * @return array card ids
     */
    public function cardbox_get_card_list() {
        return $this->flashcards;
    }
    
    public function cardbox_get_first_cardid() {
        return $this->flashcards[0];
    }

//    public function cardbox_get_card_for_review($cardid) {
//
//        global $DB;
//
//        $sql = "SELECT c.id, t.topicname "
//                . "FROM {cardbox_cards} c "
//                . "LEFT JOIN {cardbox_topics} t ON c.topic = t.id "
//                . "WHERE c.cardbox = ? AND approvedby IS NULL";
//
//        return $DB->get_record_sql($sql, array($this->cardbox, $cardid), MUST_EXIST);
//    }
    
    public function cardbox_get_cardcontents_initial() {
        return self::cardbox_get_cardcontents($this->flashcards[0]);
    }
    
    static function cardbox_get_cardcontents($cardid) {

        global $DB;
        return $DB->get_records('cardbox_cardcontents', array('card' => $cardid));

    }
    /**
     * 
     * @global type $DB
     * @param type $cardid
     * @return type
     */
    static function cardbox_get_topic($cardid) { // XXX move to locallib
        global $DB;
        $sql = "SELECT t.topicname "
                . "FROM {cardbox_cards} c "
                . "LEFT JOIN {cardbox_topics} t ON c.topic = t.id "
                . "WHERE c.id = ?";
        return $DB->get_field_sql($sql, array($cardid), $strictness=IGNORE_MISSING);
    }
  
}