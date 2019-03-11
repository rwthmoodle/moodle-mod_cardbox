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
 * @authors   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cardbox_cardboxmodel { // use this class as a templatable as well?

    private $cardcount = 0;
    private $boxes = array(0 => array(), 1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array());
    private $selection;

    public function __construct($cardboxid) {

        global $DB, $USER;

        // 1. Add any new cards to the user's cardbox system (represented by the cardbox_progress table).
        cardbox_add_new_cards();
        
        // 2. Access all cards in this user's cardbox system and adjust the overall cardcount.
        $this->cardbox_get_users_cards($cardboxid);

        // 3. Select 21 flashcards for a practice session.
        $this->cardbox_select_cards_for_practice();
        
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
            // Blaue Box printen
            return;
        }
        
        $this->cardcount = count($flashcards);
        
        foreach ($flashcards as $card) {
            $this->boxes[$card->cardposition][] = $card;
        }

    }
    /**
     * Function contains algorithm for selecting 21 cards for a practice session.
     *
     */
    public function cardbox_select_cards_for_practice() {

        $cardsperbox = array(0 => 3, 1 => 4, 2 => 5, 3 => 4, 4 => 3, 5 => 2);
        $selection = array();
        $newvocab = array();
        
        // 0. If there are not enough cards in the last box, select the missing amount from the first box if possible.
        $initialdiff = $cardsperbox[5] - count($this->boxes[5]);
        $addextra = ($initialdiff <= 0) ? 0 : $initialdiff;
        
        for ($i = 0; $i < 6; $i++) {

            $select = $cardsperbox[$i] + $addextra;

            $box = $this->boxes[$i];

            // 1. Prioritize the cards within the box.
            if (!empty($box)) {
                usort($box, array('cardbox_cardboxmodel', 'cardbox_compare_cards'));
            }

            // 2. Select cards from the box.
            for ($j = 0; $j < $select; $j++) {
                if (empty($box[$j])) {
                    break;
                }
                if ($i != 0) {
                    $selection[] = $box[$j];
                } else {
                    $newvocab[] = $box[$j];
                }
            }

            // 3. If there are not enough cards in the box, select the missing amount from the next box if possible.
            $diff = $select - count($box);
            if ($diff > 0) {
                $addextra = $diff;
            } else {
                $addextra = 0;
            }

        }
        // 4. Account for the (primacy and) recency effect by (beginning with difficult terms and) ending with new ones.
        foreach ($newvocab as $voc) {
            $selection[] = $voc;
        }

        $this->selection = $selection;
    }

    /**
     * This function prioritises cards within a box according to
     * the time they were last practised and the number of repetitions
     * that the user needed so far for this card.
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
    
    
    public function cardbox_get_first_card() {
        return $this->selection[0];
    }
    
//    public function cardbox_get_card($number) {
//        return $this->selection[$number];
//    }
    
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