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
 * File listing all constants
 * @package   mod_cardbox
 * @copyright 2026 IT Center RWTH Aachen
 * @author    Amrita Deb Dutta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * IMPORT CARDS
 */
define ('LOAD_FORM_AND_CSVPREVIEW', 1);
define ('PROCESSCSV_AND_CREATE_CARDS', 2);
/**
 * ADD CARDS
 */
define('NEW_TOPIC_CREATED', 0);
define('NULL_TOPIC', -1);
define ('CAN_CHANGE _NO_OF_NECESSARY_ANSWERS', 0);
define ('CANNOT_CHANGE_NO_OF_NECESSARY_ANSWERS', 1);
define('CARDBOX_EVALUATE_ALL', 0);
define('CARDBOX_EVALUATE_ONE', 1);
define('CARDBOX_CARDSIDE_QUESTION', 0);
define('CARDBOX_CARDSIDE_ANSWER', 1);
define('CARD_MAIN_INFORMATION', 0);
define('CARD_CONTEXT_INFORMATION', 1);
define('CARD_IMAGEDESCRIPTION_INFORMATION', 2);
define('CARDBOX_CONTENTTYPE_IMAGE', 0);
define('CARDBOX_CONTENTTYPE_TEXT', 1);
define('CARDBOX_CONTENTTYPE_AUDIO', 2);
/**
 * OVERVIEW OF CARDS
 */
define('SORT_CREATIONDATE_ASC', 0);
define('SORT_CREATIONDATE_DESC', 1);
define('SORT_ALPHABETIC_ASC', 2);
define('SORT_ALPHABETIC_DESC', 3);
define('SHOW_CARDS_OF_ALL_DECKS', -1);
define('SHOW_CARDS_OF_NEW_DECK', 0);
define('SHOW_CARDS_OF_DECK_1', 1);
define('SHOW_CARDS_OF_DECK_2', 2);
define('SHOW_CARDS_OF_DECK_3', 3);
define('SHOW_CARDS_OF_DECK_4', 4);
define('SHOW_CARDS_OF_DECK_5', 5);
define('SHOW_CARDS_OF_MASTERED_DECK', 6);