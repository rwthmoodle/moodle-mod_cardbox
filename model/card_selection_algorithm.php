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
defined('MOODLE_INTERNAL') || die();
require_once('card_selection_interface.php');

class cardbox_card_selection_algorithm implements cardbox_card_selection_interface {

    private static $prioritytopic;
    private $spacing;
    private $practiceall;

    public function __construct($topicid = null, $practiceall = true) {

        global $DB;

        $this->spacing = array();
        $this->spacing[1] = new DateInterval('P1D');
        $this->spacing[2] = new DateInterval('P3D');
        $this->spacing[3] = new DateInterval('P7D');
        $this->spacing[4] = new DateInterval('P16D');
        $this->spacing[5] = new DateInterval('P34D');

        $this->practiceall = $practiceall;

        if (!empty($topicid) && $topicid != -1) {
            self::$prioritytopic = $DB->get_field('cardbox_topics', 'topicname', array('id' => $topicid), $strictness = MUST_EXIST);
        }

    }
    /**
     * This function creates a priority queue from the user's cards
     * and then selects the first 21 items of the queue for practice.
     *
     * @global type $CFG
     * @param type $cards
     * @return type
     */
    public function cardbox_select_cards_for_practice($cards = null) {

        if (empty($cards)) {
            return null;
        }

        $now = new DateTime("now");

        $priorityqueue = [];
        $selection = [];

        // 1. Calculate the ideal date and time of repetition for each card and add due cards to the queue.
        foreach ($cards as $card) {

            if ($card->cardposition > 5) {
                continue;

            } else if ($card->cardposition == 0) {
                $card->duedatetime = $now;

            } else {
                $last = new DateTime("@$card->lastpracticed");
                $card->duedatetime = $last->add($this->spacing[$card->cardposition]);
            }

            if ( ($card->duedatetime <= $now) || $this->practiceall ) {
                $priorityqueue[] = $card;
            }

        }

        // 2. Sort the cards according to their ideal repetition date times, deck, number of repetitions and time of last practice.
        // There is an option to prioritise cards by topic first.
        if (!empty(self::$prioritytopic)) {
            usort($priorityqueue, array('cardbox_card_selection_algorithm', 'cardbox_compare_cards_priority_topic'));
        } else {
            usort($priorityqueue, array('cardbox_card_selection_algorithm', 'cardbox_compare_cards_1st_level'));
        }

        // 3. Pick the first 21 cards from the queue.
        for ($i = 0; ( ($i < count($priorityqueue)) && ($i < 21)); $i++) {
            $card = $priorityqueue[$i];
            $selection[] = $card;
        }

        return $selection;
    }

    public function cardbox_count_due_and_not_due($cards, $now) {

        $result = array('due' => 0, 'notdue' => 0);

        foreach ($cards as $card) {

            $last = new DateTime("@$card->lastpracticed");
            $card->duedatetime = $last->add($this->spacing[$card->cardposition]);
            if ($card->duedatetime <= $now) {
                $result['due']++;
            } else {
                $result['notdue']++;
            }

        }

        return $result;

    }

    /**
     * This function sorts/prioritises cards within a box, favouring those that
     * belong to the specified topic. If neither card or both cards belong to this
     * topic, the usual selection criteria are applied, as specified by
     * cardbox_compare_cards_1st_level().
     *
     * @param obj $a
     * @param obj $b
     * @return int -1 means, $a comes first, 1 means, $b comes first
     */
    public static function cardbox_compare_cards_priority_topic($a, $b) {

        if ($a->topicname == $b->topicname) {
            return self::cardbox_compare_cards_1st_level($a, $b);
        }

        if ( ($a->topicname != self::$prioritytopic) && ($b->topicname != self::$prioritytopic) ) {
            return self::cardbox_compare_cards_1st_level($a, $b);
        }

        if ($a->topicname == self::$prioritytopic) {
            return -1;
        }
        return 1;
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
    public static function cardbox_compare_cards_1st_level($a, $b) {

        // Differences in due datetime that are only up to 3 hours
        // are ignored in favour of second level priorities.
        $diff = $a->duedatetime->diff($b->duedatetime);
        if ($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && (
                $diff->h < 3 || (
                    $diff->h == 3 && $diff->i == 0 && $diff->s == 0 && $diff == 0
                )
            )
        ) {
            return self::cardbox_compare_cards_2nd_level($a, $b);
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
    public static function cardbox_compare_cards_2nd_level($a, $b) {

        if ($a->cardposition == $b->cardposition) {
            return self::cardbox_compare_cards_3rd_level($a, $b);
        }

        // Prioritise cards from lower decks over those from higher decks.
        return ($a->cardposition < $b->cardposition) ? -1 : 1;

    }
    /**
     * This function sorts cards according to the number of repetitions a user
     * needed to get the card into its current position in the cardbox system.
     *
     * @param type $a
     * @param type $b
     * @return type
     */
    public static function cardbox_compare_cards_3rd_level($a, $b) {

        if ($a->repetitions == $b->repetitions) {
            return self::cardbox_compare_cards_4th_level($a, $b);
        }
        // Cards that were difficult for this user in the past get third priority.
        return ($a->repetitions > $b->repetitions) ? -1 : 1;

    }
    /**
     * This function sorts cards according to the time they were last practiced.
     * If both cards are due within a time interval of 3 hours, they are on the
     * same deck and were repeated the same amount of times, then this is the
     * last sorting criterion.
     *
     * @param type $a
     * @param type $b
     * @return int
     */
    public static function cardbox_compare_cards_4th_level($a, $b) {

        if ($a->lastpracticed == $b->lastpracticed) { // practically never happens because of the precision of timestamps.
            return 0;
        }
        // Cards that were last practiced longer ago get fourth priority.
        return ($a->lastpracticed < $b->lastpracticed) ? -1 : 1;

    }

}
