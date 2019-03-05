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
class cardbox_cardboxmodel {

    private $cardcount;
    private $box0 = array();
    private $box1 = array();
    private $box2 = array();
    private $box3 = array();
    private $box4 = array();

    public function __construct($cmid) {

        global $DB, $USER;

//        $sql = "SELECT progress.*, topic.topicname, content.id as contentid, content.cardside, content.contenttype, content.content, type.name as typename"
//                . " FROM {cardbox_progress} progress"
//                . " INNER JOIN {cardbox_cards} card ON progress.card = card.id"
//                . " LEFT JOIN {cardbox_topics} topic ON card.topic = topic.id"
//                . " INNER JOIN {cardbox_cardcontents} content ON progress.card = content.card"
//                . " INNER JOIN {cardbox_contenttypes} type ON content.contenttype = type.id"
//                . " WHERE progress.userid = ? AND card.cardbox = ?";

//        $sql = "SELECT p.card, p.cardposition, p.lastpracticed, p.repetitions, "
//                . "top.topicname, "
//                . "cont.cardside, cont.content "
//                . "FROM {cardbox_progress} p "
//                . "INNER JOIN {cardbox_cards} c ON c.id = p.card "
//                . "LEFT JOIN {cardbox_topics} top ON c.topic = top.id "
//                . "RIGHT JOIN {cardbox_cardcontents} cont ON cont.card = p.card"; // THIS LINE CAUSES TROUBLE.
                
        
        $sql = "SELECT p.card, p.cardposition, p.lastpracticed, p.repetitions, "
                . "top.topicname, "
                . "cont.cardside, cont.content "
                . "FROM {cardbox_progress} p "
                . "JOIN {cardbox_cardcontents} cont ON cont.card = p.card "
                . "JOIN {cardbox_cards} c ON c.id = cont.card "
                . "LEFT JOIN {cardbox_topics} top ON c.topic = top.id ";
                

        $flashcards = $DB->get_records_sql($sql, array($USER->id, $cmid));
        
        var_dump($flashcards);
   
    }
    
}