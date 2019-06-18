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
    private $selection = null;
    private $selectionalgorithm;
    private $sortingalgorithm;
    
    public function __construct($cardboxid, cardbox_card_selection_interface $selectionalgorithm = null, cardbox_card_sorting_interface $sortingalgorithm = null) {
        
        $this->id = $cardboxid;
        
        // 1. Add any new cards to the user's cardbox system (represented by the cardbox_progress table).
        cardbox_add_new_cards();

        // 2. Access all cards in this user's cardbox system and adjust the overall cardcount.
        $this->cardbox_get_users_cards();

        $this->selectionalgorithm = $selectionalgorithm;
        $this->sortingalgorithm = $sortingalgorithm;
        
//        // 3. Select 21 flashcards for a practice session.
//        if (!empty($this->flashcards) && !empty($selectionalgorithm)) {
//            $this->selectionalgorithm = $selectionalgorithm;
//            $this->cardbox_select_cards_for_practice();    
//        }
//        
//        // 4. Sort the selected cards.
//        if (!empty($this->selection) && !empty($sortingalgorithm)) {
//            $this->sortingalgorithm = $sortingalgorithm;
//            $this->cardbox_sort_cards();
//        }

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
        
        $selection = array();
        
        // Select 21 flashcards for a practice session.
        if (!empty($this->flashcards) && !empty($this->selectionalgorithm)) {
            $this->cardbox_select_cards_for_practice();    
        } else {
            return null;
        }
        
        // Sort the selected cards.
        if (!empty($this->selection) && !empty($this->sortingalgorithm)) {
            $this->cardbox_sort_cards();
        }
        
        // Return the ids of the cards.
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
    public function cardbox_select_cards_for_practice() {

        // Delegate card selection to the selection algorithm instance.
        $this->selection = $this->selectionalgorithm->cardbox_select_cards_for_practice($this->flashcards);

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
        if (isset($this->selection[0])) {
            return $this->selection[0];
        } else {
            return null;
        }
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
}