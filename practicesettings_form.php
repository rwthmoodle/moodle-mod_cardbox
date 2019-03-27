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
 * @author   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die(); //  It must be included from a Moodle page.

require_once("$CFG->libdir/formslib.php"); // moodleform is defined in formslib.php
require_once('locallib.php');

/**
 * This form allows the user to choose parameters for a new practice session.
 * 
 */
class mod_cardbox_practicesettings_form extends moodleform {
    
    function definition() {
        
        //global $CFG, $DB, $USER, $COURSE;

        $mform = $this->_form;

        // Pass contextual parameters to the form (via set_data() in controller.php).
        $mform->addElement('hidden', 'id'); // Course module id.
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'course'); // Course id.
        $mform->setType('course', PARAM_INT);
        
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_INT);
        
        $radioarray = array();
        $radioarray[] = $mform->createElement('radio', 'correctionmode', '', get_string('selfcorrection', 'cardbox'), 0);
        $radioarray[] = $mform->createElement('radio', 'correctionmode', '', get_string('autocorrection', 'cardbox'), 1);
        $mform->addGroup($radioarray, 'correctionmodegroup', get_string('choosecorrectionmode', 'cardbox'), array(' '), false);
        $mform->addHelpButton('correctionmodegroup', 'choosecorrectionmode', 'cardbox');
        
        // Get topics to choose from when creating a new card.
        $topiclist = cardbox_get_topics();
//        if (!empty($topiclist) && count($topiclist) > 1) {
//            $mform->addElement('checkbox', "topic1", get_string('weightopic', 'cardbox'), $topiclist[1]);
//            for ($i = 2; $i <= count($topiclist); $i++) {
//                $mform->addElement('checkbox', "topic$i", '', $topiclist[$i]);
//            }
//            $mform->addHelpButton('topic1', 'prioritisetopics', 'cardbox');
//
//
//        }
        $mform->addElement('select', 'topic', get_string('weightopic', 'cardbox'), $topiclist);
        $mform->addHelpButton('topic', 'weightopic', 'cardbox');
        
        
        $this->add_action_buttons(true, get_string('beginpractice', 'cardbox'));

    }

}