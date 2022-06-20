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
 * Description of start
 *
 */
class cardbox_start implements \renderable, \templatable {

    private $topics;
    private $autocorrectionoption = false;
    private $amountcards;

    public function __construct($autocorrection, $cardboxid) {

        $this->cardbox_prepare_topics_to_study($cardboxid);

        if ($autocorrection == 1) {
            $this->autocorrectionoption = true;
        }

        $this->cardbox_define_amount_of_cards_to_study();

    }

    /**
     * Function includes the list of topics in the practice options modal.
     * The user can then choose to prioritise one of the topics in the
     * selection of cards for a practice session.
     *
     * @global type $CFG
     */
    public function cardbox_prepare_topics_to_study($cardboxid) {

        global $CFG;
        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');

        $this->topics = array();
        $this->choicestopics = array();

        $topiclist = cardbox_get_topics($cardboxid);

        foreach ($topiclist as $key => $value) {
            $this->topics[] = array('value' => $key, 'label' => $value);
            if ($key === -1) {
                $this->choicestopics[] = array('value' => $key, 'label' => 'all');
            } else {
                $this->choicestopics[] = array('value' => $key, 'label' => $value);
            }
        }

    }

    public function cardbox_define_amount_of_cards_to_study() {
        $this->amountcards = array();
        $this->amountcards[] = array('value' => 0, 'label' => get_string('undefined', 'cardbox'));
        $this->amountcards[] = array('value' => 10, 'label' => 10);
        $this->amountcards[] = array('value' => 20, 'label' => 20);
        $this->amountcards[] = array('value' => 30, 'label' => 30);
        $this->amountcards[] = array('value' => 40, 'label' => 40);
        $this->amountcards[] = array('value' => 50, 'label' => 50);

    }

    /**
     * Function returns an array with data. The keys of the array have matching variables
     * in the template. These are replaced with the array values by the renderer.
     *
     * @global type $OUTPUT
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {

        global $OUTPUT;

        $data['autoenabled'] = $this->autocorrectionoption;
        $data['autodisabled'] = !$this->autocorrectionoption;
        $data['topics'] = $this->topics;
        $data['choicestopics'] = $this->choicestopics;
        $data['helpbuttonpracticeall'] = $OUTPUT->help_icon('practiceall', 'cardbox');
        $data['amountcards'] = $this->amountcards;
        return $data;

    }

}
