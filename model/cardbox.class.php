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
class cardbox_cardboxmodel { // use this class as a templatable as well?

    private $cardcount = 0;
    private $boxes = array(0 => array(), 1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array(), 6 => array());
    private $countnew;
    private $countknown;
    private $countboxone;
    private $countboxtwo;
    private $countboxthree;
    private $countboxfour;
    private $countboxfive;
    private $selection;
    private static $prioritytopic;

    public function __construct($cardboxid, $topic=null) {

        global $DB, $USER;

        // 1. Add any new cards to the user's cardbox system (represented by the cardbox_progress table).
        cardbox_add_new_cards();
        
        // 2. Access all cards in this user's cardbox system and adjust the overall cardcount.
        $this->cardbox_get_users_cards($cardboxid);

        // 3. Select 21 flashcards for a practice session.
        $this->cardbox_select_cards_for_practice($topic);
        
        // 4. Access and arrange the content of each selected card.
        
        
    }
    /**
     * Function returns the ids of those cards selected for practice.
     *
     * @return array of ints
     */
    public function cardbox_get_card_selection() {
        $selection = array();
        foreach ($this->selection as $card) {
            $selection[] = $card->card;
        }
        return $selection;
    }
    /**
     * Function returns an array specifying how many cards there are in each box
     * (for this user and this cardbox instance).
     *
     * @return array
     */
    public function cardbox_get_status() {
        
        return array(0 => $this->countnew, 1 => $this->countboxone, 2 => $this->countboxtwo, 3 => $this->countboxthree, 4 => $this->countboxfour, 5 => $this->countboxfive, 6 => $this->countknown);

    }
    
    /**
     * Function retrieves all flashcards that
     * 1. belong to the current cardbox plugin instance
     * 2. are registered for the current user in the progress table which is the virtual cardbox
     *
     * Each card is filed into one of the 5 cardboxes.
     *
     * @global obj $DB
     * @global obj $USER
     * @return array of objects or null
     */
    public function cardbox_get_users_cards($cardboxid) {

        global $DB, $USER;

        $sql = "SELECT p.card, p.cardposition, p.lastpracticed, p.repetitions, top.topicname "
                . "FROM {cardbox_progress} p "
                . "LEFT JOIN {cardbox_cards} c ON c.id = p.card "
                . "LEFT JOIN {cardbox_topics} top ON c.topic = top.id "
                . "WHERE p.userid = ? AND c.cardbox = ? "
                . "ORDER BY p.cardposition";

        $flashcards =  $DB->get_records_sql($sql, array($USER->id, $cardboxid));

        if (empty($flashcards)) {
            return;
        }
        
        $this->cardcount = count($flashcards);

        foreach ($flashcards as $card) {
            $this->boxes[$card->cardposition][] = $card;
        }
        
        $this->countnew = count($this->boxes[0]);
        $this->countboxone = count($this->boxes[1]);
        $this->countboxtwo = count($this->boxes[2]);
        $this->countboxthree = count($this->boxes[3]);
        $this->countboxfour = count($this->boxes[4]);
        $this->countboxfive = count($this->boxes[5]);
        $this->countknown = count($this->boxes[6]);

    }
    /**
     * Function contains algorithm for selecting 21 cards for a practice session.
     *
     */
    public function cardbox_select_cards_for_practice($topic = null) {

        global $DB;

        if (!empty($topic) && $topic != -1) {
            self::$prioritytopic = $DB->get_field('cardbox_topics', 'topicname', array('id' => $topic), $strictness=MUST_EXIST);
        }

        $cardsperbox = array(0 => 3, 1 => 4, 2 => 5, 3 => 4, 4 => 3, 5 => 2);
        $selection = array();

        $addextra = 0;

//        $ref = $this->cardbox_count_cards_in_progress();
//        
//        if ($ref < )
        

        // 1. Account for the primacy effect by beginning with difficult cards (which are stored in box 1).
        for ($i = 1; $i <= 5; $i++) {

            $select = $cardsperbox[$i] + $addextra;

            $box = $this->boxes[$i];

            // 1.1 Prioritize the cards within the box.
            if (!empty($box)) {
                if (empty($topic) || $topic == -1) {
                    usort($box, array('cardbox_cardboxmodel', 'cardbox_compare_cards'));
                } else {
                    usort($box, array('cardbox_cardboxmodel', 'cardbox_compare_cards_priority_topic'));
                }
            }

            // 1.2 Select cards from the box.
            for ($j = 0; $j < $select; $j++) {
                if (empty($box[$j])) {
                    break;
                }
                $selection[] = $box[$j];
            }

            // 1.3 If there are not enough cards in the box, select the missing amount from the next box if possible.
            //     (Or from box 0 if this is box 5.)
            $diff = $select - count($box);
            $addextra = ($diff > 0) ? $diff : 0;

        }
        // 2. Account for the recency effect by ending with new cards (which are stored in box 0).
        
        // 2.1 New cards can only be prioritised according to topic, because none of them has been practiced before.
        if ( (!empty($this->boxes[0])) && (!empty($topic)) && ($topic != -1) ) {
            usort($this->boxes[0], array('cardbox_cardboxmodel', 'cardbox_compare_cards_topic'));
        }
        // 2.2 Select new cards from box 0.
        $select = $cardsperbox[0] + $addextra;
        for ($j = 0; $j < $select; $j++) {
            if (empty($this->boxes[0][$j])) {
                break;
            }
            $selection[] = $this->boxes[0][$j];
        }

//        $cardcount = count($selection);
//        $missing = 21 - $cardcount;
//        if ($cardcount < 21) {
//            for ($k = 0; $k < $missing; $k++) {
//                if (empty($this->boxes[1][$k])) {
//                    break;
//                }
//                $selection[] = $this->boxes[1][$k];
//            }
//        }
        
        $this->selection = $selection;
        
        self::$prioritytopic = null;
    }

    public function cardbox_count_cards_in_progress() {
        return $this->countboxone + $this->countboxtwo + $this->countboxthree + $this->countboxfour + $this->countboxfive;
    }
    
    /**
     * This function prioritises cards within a box according to the time they
     * were last practised and the number of repetitions that the user needed
     * so far for this card.
     *
     * @param type $a
     * @param type $b
     * @return int
     */
    static function cardbox_compare_cards($a, $b) {
        
        if ($a->lastpracticed == null) {
            return -1;
        }
        if ($b->lastpracticed == null) {
            return 1;
        }

        if ($a->lastpracticed == $b->lastpracticed) {
            
            if ($a->repetitions == $b->repetitions) {
                return 0;
            }
            // Cards that were difficult for this user in the past get second priority.
            return ($a->repetitions > $b->repetitions) ? -1 : 1;
            
        }
        // Cards that were last practiced longer ago get first priority.
        return ($a->lastpracticed < $b->lastpracticed) ? -1 : 1;
        
    }
    
    /**
     * This function sorts/prioritises cards within a box, favouring those that
     * belong to the specified topic. If neither card or both cards belong to this
     * topic, the usual selection criteria are applied, as specified by cardbox_compare_cards().
     * 
     * @param obj $a
     * @param obj $b
     * @return int -1 means, $a comes first, 1 means, $b comes first
     */
    static function cardbox_compare_cards_priority_topic($a, $b) {
        
        if ($a->topicname == $b->topicname) {
            return self::cardbox_compare_cards($a, $b);
        }
        
        if ( ($a->topicname != self::$prioritytopic) && ($b->topicname != self::$prioritytopic) ) {
            return self::cardbox_compare_cards($a, $b);
        }
        
        if ($a->topicname == self::$prioritytopic) {
            return -1;
        }
        return 1;
    }
    /**
     * Function compares cards, considering only whether or not they are affiliated
     * with the priority topic.
     *
     * @param type $a
     * @param type $b
     * @return int
     */
    static function cardbox_compare_cards_topic($a, $b) {
        if ($a->topicname == $b->topicname) {
            return 0;
        }
        if ( ($a->topicname != self::$prioritytopic) && ($b->topicname != self::$prioritytopic) ) {
            return 0;
        }
        if ($a->topicname == self::$prioritytopic) {
            return -1;
        }
        return 1;
    }
    
    
    public function cardbox_get_first_card() {
        return $this->selection[0];
    }

    /**
     * 
     * @global obj $DB
     * @global obj $USER
     * @param type $cardid
     * @return type
     */
    static function cardbox_get_card($cardid) {

        global $DB, $USER;        
        
        $sql = "SELECT p.card, p.cardposition, p.lastpracticed, p.repetitions, top.topicname "
                . "FROM {cardbox_progress} p "
                . "LEFT JOIN {cardbox_cards} c ON c.id = p.card "
                . "LEFT JOIN {cardbox_topics} top ON c.topic = top.id "
                . "WHERE p.userid = ? AND c.id = ? "
                . "ORDER BY p.cardposition";

        return $DB->get_record_sql($sql, array($USER->id, $cardid), MUST_EXIST);

    }
    
    /**
     * Function returns all content items belonging to this card.
     *
     * @global obj $DB
     * @param type $cardid
     * @return type
     */
    static function cardbox_get_card_contents($cardid) { // TODO: exception handling.

        global $DB;
        $contents = $DB->get_records('cardbox_cardcontents', array('card' => $cardid));
        usort($contents, array('cardbox_cardboxmodel', 'cardbox_compare_cardcontents'));
        return $contents;
    }
    
    /**
     * This function orders the content elements of a card, e.g. groups question and answer elements.
     *
     * @param type $a
     * @param type $b
     * @return int
     */
    static function cardbox_compare_cardcontents($a, $b) {
        
        if ($a->cardside == $b->cardside) {
            
            if ($a->contenttype == $b->contenttype) {
                return 0;
            }
            // Pictures precede text.
            return ($a->contenttype < $b->contenttype) ? -1 : 1;
            
        }
        // Questions precede answers.
        return ($a->cardside < $b->cardside) ? -1 : 1;
        
    }
    
    
    /**
     * Function returns the topic a card belongs to (if any).
     *
     * @global obj $DB
     * @param type $cardid
     * @return string or null
     */
//    public function cardbox_get_card_topic($cardid) {
//        
//        global $DB;
//        
//        $sql = "SELECT t.topicname "
//                . "FROM {cardbox_cards} c JOIN {cardbox_topics} t ON c.topic = t.id "
//                . "WHERE c.id = ?";
//
//        return $DB->get_record_sql($sql, array($cardid), $strictness=IGNORE_MISSING);
//
//    }

}




        // This gets all card contents with the card info duplicated. // working :)
//        $sql = "SELECT cont.id as contentid, p.card, cont.cardside, cont.content, "
//                . "p.cardposition, p.lastpracticed, p.repetitions, "
//                . "top.topicname "
//                . "FROM {cardbox_progress} p "
//                . "JOIN {cardbox_cardcontents} cont ON cont.card = p.card "
//                . "LEFT JOIN {cardbox_cards} c ON c.id = cont.card "
//                . "LEFT JOIN {cardbox_topics} top ON c.topic = top.id "
//                . "WHERE p.userid = ? AND c.cardbox = ? "
//                . "ORDER BY p.card, cont.cardside";
//
//        $flashcards = $DB->get_records_sql($sql, array($USER->id, $cardboxid));