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
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Description of overview
 *
 * @author ah105090
 */
class cardbox_overview implements \renderable, \templatable {

    private $topicid;
    private $topics = array();
    private $cards = array();

    public function __construct($list, $offset, $context, $cmid, $cardboxid, $topicid, $usedforemail = false) {

        require_once('card.php');

        global $DB, $PAGE;

        $topics = $DB->get_records('cardbox_topics', array('cardboxid' => $cardboxid));
        $this->topicid = $topicid;
        foreach ($topics as $topic) {
            if ($topic->id == $topicid) {
                $this->topics[] = array('topicid' => $topic->id, 'topic' => $topic->topicname, 'selected' => true);
            } else {
                $this->topics[] = array('topicid' => $topic->id, 'topic' => $topic->topicname, 'selected' => false);
            }
        }

        $perpage = 10;
        $renderer = $PAGE->get_renderer('mod_cardbox');

        if (has_capability('mod/cardbox:approvecard', $context) && !$usedforemail) {
            $allowedtoedit = true;
        } else {
            $allowedtoedit = false;
        }

        if (has_capability('mod/cardbox:seestatus', $context)) {
            $seestatus = true;
        } else {
            $seestatus = false;
        }

        for ($i = $offset; ($i < count($list) && $i < $offset + $perpage); $i++) {
            $card = new cardbox_card($list[$i], $context, $cmid, $allowedtoedit, $seestatus);
            $this->cards[] = $card->export_for_template($renderer);
        }

    }

    public function export_for_template(\renderer_base $output) {
        $data = array();

        if ($this->topicid == -1) {
            $data['nopreference'] = true;
        } else if ($this->topicid == 0) {
            $data['cardswithouttopic'] = true;
        }

        $data['topics'] = $this->topics;
        $data['cards'] = $this->cards;
        return $data;
    }
}
