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

require_once('card_sorting_interface.php');

class cardbox_card_sorting_algorithm implements cardbox_card_sorting_interface {

    public function cardbox_sort_cards_for_practice($cardselection) {

        usort($cardselection, array('cardbox_card_sorting_algorithm', 'cardbox_compare_cards_for_sorting'));        
        return $cardselection;
        
    }

    /**
     * This function sorts cards according to their position in the Leitner system.
     * Cards from box one move to the head of the queue and new cards move to its tail.
     * This sorting uses the effects of primacy and recency to help students remember
     * difficult and new facts.
     * 
     * @param stdClass object representing a card $a
     * @param stdClass object representing a card $b
     */
    static function cardbox_compare_cards_for_sorting($a, $b) {

        if ($a->cardposition == $b->cardposition) {
            return 0;
        }
        if ($a->cardposition == 1 || $b->cardposition == 0) {
            return -1;
            
        } else if ($a->cardposition == 0 || $b->cardposition == 1) {
            return 1;
            
        }
        return 0;
    }

}
