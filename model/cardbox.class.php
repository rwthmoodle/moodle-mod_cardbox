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

    private $id;
    private $flashcards;
    private $cardcount = 0;
    private $duecardcount = 0;
    private $boxes = array(0 => array(), 1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array(), 6 => array());
    private $countnew;
    private $countknown;
    private $countboxone;
    private $countboxtwo;
    private $countboxthree;
    private $countboxfour;
    private $countboxfive;
    private $selection;
    private $algorithm;
    private $sortingalgorithm;
    
    public function __construct($cardboxid, $topic = null, cardbox_card_selection_interface $algorithm = null, cardbox_card_sorting_interface $sortingalgorithm = null, $practiceall = true) {

        global $DB, $USER;
        
        $this->id = $cardboxid;
        
        // 1. Add any new cards to the user's cardbox system (represented by the cardbox_progress table).
        cardbox_add_new_cards();

        // 2. Access all cards in this user's cardbox system and adjust the overall cardcount.
        $this->cardbox_get_users_cards();

        // 3. Select 21 flashcards for a practice session.
        if (!empty($this->flashcards) && !empty($algorithm)) {
            $this->algorithm = $algorithm;
            $this->cardbox_select_cards_for_practice($topic, $practiceall);    
        }
        
        // 4. Sort the selected cards.
        if (!empty($this->selection) && !empty($sortingalgorithm)) {
            $this->sortingalgorithm = $sortingalgorithm;
            $this->cardbox_sort_cards();
        }

    }
    /**
     * Function returns the number of cards in the user's cardbox.
     *
     * @return int
     */
    public function cardbox_get_card_count() {
        return $this->cardcount;
    }
    
    public function cardbox_count_due_cards() {
        return $this->duecardcount;
    }
    
    public function cardbox_count_known_cards() {
        return $this->countknown;
    }
    /**
     * Function returns the ids of those cards selected for practice.
     *
     * @return array of ints
     */
    public function cardbox_get_card_selection() {
        if (empty($this->selection)) {
            return null;
        }
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
     * 2. are registered for the current user in the progress table which is the virtual representation of a cardbox system
     *
     * Each card is filed into one of the 5 cardboxes.
     *
     * @global obj $DB
     * @global obj $USER
     * @return array of objects or null
     */
    public function cardbox_get_users_cards() {

        global $DB, $USER;

        $sql = "SELECT p.card, p.cardposition, p.lastpracticed, p.repetitions, top.topicname "
                . "FROM {cardbox_progress} p "
                . "LEFT JOIN {cardbox_cards} c ON c.id = p.card "
                . "LEFT JOIN {cardbox_topics} top ON c.topic = top.id "
                . "WHERE p.userid = ? AND c.cardbox = ? "
                . "ORDER BY p.cardposition";

        $flashcards =  $DB->get_records_sql($sql, array($USER->id, $this->id));

        if (empty($flashcards)) {
            $this->cardcount = 0;
            return;
        }

        $this->flashcards = $flashcards;
        $this->cardcount = count($flashcards);

        foreach ($flashcards as $card) {
            $this->boxes[$card->cardposition][] = $card;
            if (cardbox_is_card_due($card)) {
                $this->duecardcount++;
            }
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
     * @global obj $DB
     * @param type $topic
     */
    public function cardbox_select_cards_for_practice($topic = null, $practiceall) {

        // Delegate card selection to the selection algorithm instance.
        $this->selection = $this->algorithm->cardbox_select_cards_for_practice($this->flashcards, $topic, $practiceall);

    }
    /**
     * Function contains algorithm for sorting the selected cards into a reasonable order.
     */
    public function cardbox_sort_cards() {
        
        // Delegate card sorting to the sorting algorithm instance.
        $sortedselection = $this->sortingalgorithm->cardbox_sort_cards_for_practice($this->selection);
        $this->selection = $sortedselection;
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