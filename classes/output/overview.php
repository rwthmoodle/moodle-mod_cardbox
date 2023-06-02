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
    private $deckid;
    private $sort;
    private $desc;
    private $topics = array();
    private $cards = array();
    private $decks = array();

    public function __construct($list, $offset, $context, $cmid, $cardboxid, $topicid, $usedforemail = false,  $sort, $deck) {

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

        $this->deckid = $deck;
        for ($i = 1; $i < 6; $i++) {
            if ($deck == $i) {
                $this->decks[] = array('deck' => $i, 'selected' => true);
            } else {
                $this->decks[] = array('deck' => $i, 'selected' => false);
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

        $this->sort = $sort;

    }

    public function export_for_template(\renderer_base $output) {
        $data = array();

        if ($this->topicid == -1) {
            $data['nopreference'] = true;
        } else if ($this->topicid == 0) {
            $data['cardswithouttopic'] = true;
        }

        if ($this->deckid == -1) {
            $data['nopreferencedeck'] = true;
        }
        if ($this->deckid == 0) {
            $data['newcard'] = true;
        }
        if ($this->deckid == 6) {
            $data['masteredcard'] = true;
        }
        $data['decks'] = $this->decks;
        $data['topics'] = $this->topics;
        $data['sortca'] = $this->sort === 1;
        $data['sortad'] = $this->sort === 2;
        $data['sortaa'] = $this->sort === 3;
        $data['cards'] = $this->cards;
        return $data;
    }
}
