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
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Description of algorithm
 *
 * @author Anna Heynkes
 */
require_once('cardselectionalgorithm.php');
require_once('cardselectionalgorithm.php');

class cardbox_algorithm implements cardbox_cardselectionalgorithm {

    private static $now;
    private $spacing;
    private $availableBeforeDue;
        
    public function __construct() {

//        $this->now = new DateTime('now');

        $this->spacing = array();
        $this->spacing[0] = new DateInterval('P0D');
        $this->spacing[1] = new DateInterval('P1D');
        $this->spacing[2] = new DateInterval('P3D');
        $this->spacing[3] = new DateInterval('P7D');
        $this->spacing[4] = new DateInterval('P16D');
        $this->spacing[5] = new DateInterval('P34D');
    }

    public function cardbox_select_cards_for_practice($cards) {

        global $CFG;
//        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');
        
        // 1. For each card: Calculate how much time has elapsed since or will elapse until the perfect time of repetition.
        
        // 1. Calculate the ideal date and time of repetition for each card.
        foreach($cards as $card) {
            $last = new DateTime("@$card->lastpracticed");
            $card->duedatetime = $last->add($this->spacing[$card->cardposition]);
//            $card->timesinceoptimum = $this->now->diff($card->duedatetime); // negative time diff means the card isn't due for repetion yet.
        }
        
        // 2. Sort the cards according to their ideal repetition date times, deck, number of repetitions and time of last practice.
        usort($cards, array('cardbox_algorithm', 'cardbox_compare_cards_1st_level'));
        
        // 3. Pick the first 21 cards from the queue.
        
        // Determine whether there are cards that are not due yet?
        
        
        // 4. (vllt. als Extrafunktion) Sortiere die Karten nach Primacy-/Recency-Effekt
   
    }

    /**
     * This function sorts cards according to the times at which they are due for repetition.
     *
     * Implicitly, this also favours cards from lower decks, because their repetition intervalls
     * are smaller and thus they are more likely to be overdue. At the same time, it is ensured
     * that cards from higher decks get a turn, too, as the current time approaches or moves
     * past their due date.
     *
     * @param type $a
     * @param type $b
     * @return int
     */
    static function cardbox_compare_cards_1st_level($a, $b) {

        $timespan = 6;
        $ignore = new DateInterval('PT'.$timespan.'H');
        // Differences in due datetime that are only up to a quarter of a day (i.e. 6 hours)
        // are ignored in favour of second level priorities.
        if ($a->duedatetime->diff($b->duedatetime) <= $ignore ) {
            return cardbox_algorithm::cardbox_compare_cards_2nd_level($a, $b);
        }
        
        // Cards that are dues sooner get priority over cards that are due at a later time (whether in the past or future).
        if ($a->duedatetime < $b->duedatetime) {
            return -1;
        } else {
            return 1;
        }

    }
    /**
     * This function sorts cards according to their position in the Leitner cardbox system, i.e.
     * according to the number of times they were answered correctly.
     *
     * @param type $a
     * @param type $b
     * @return int
     */
    static function cardbox_compare_cards_2nd_level($a, $b) {
        
        // Prioritise cards from lower decks over those from higher decks.
        if ($a->cardposition < $b->cardposition) {
            return -1;
        }
        if ($a->cardposition > $b->cardposition) {
            return 1;
        }

        return cardbox_algorithm::cardbox_compare_cards_3rd_level($a, $b);

    }
    /**
     * This function sorts cards according to the number of repetitions a user
     * needed to get the card into its current position in the cardbox system.
     *
     * @param type $a
     * @param type $b
     * @return type
     */
    static function cardbox_compare_cards_3rd_level($a, $b) {
    
        if ($a->repetitions == $b->repetitions) {
            return cardbox_algorithm::cardbox_compare_cards_4th_level($a, $b);
        }
        // Cards that were difficult for this user in the past get third priority.
        return ($a->repetitions > $b->repetitions) ? -1 : 1;

    }
    /**
     * This function sorts cards according to the time they were last practiced.
     * If both cards are due within a time interval of 6 hours, they are on the
     * same deck and were repeated the same amount of times, then this is the
     * last sorting criterion.
     * 
     * @param type $a
     * @param type $b
     * @return int
     */
    static function cardbox_compare_cards_4th_level($a, $b) {

        if ($a->lastpracticed == $b->lastpracticed) { // practically never happens because of the precision of timestamps.
            return 0;
        }
        // Cards that were last practiced longer ago get fourth priority.
        return ($a->lastpracticed < $b->lastpracticed) ? -1 : 1;

    }
    
}
