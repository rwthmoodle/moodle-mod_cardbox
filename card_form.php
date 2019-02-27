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
 * This file is used when adding/editing a cardbox module to a course.
 * It contains the elements that will be displayed on the form responsible
 * for creating/installing an instance of cardbox.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @authors   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die(); //  It must be included from a Moodle page.

// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class mod_cardbox_card_form extends moodleform {

    function definition() {
        global $CFG, $DB, $USER, $COURSE;

        $mform = $this->_form;

        // Pass contextual parameters to the form (via set_data() in controller.php).
        $mform->addElement('hidden', 'id'); // Course module id.
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'course'); // Course id.
        $mform->setType('course', PARAM_INT);
        
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_INT);
        
        // Enter a prompt or question. // XXX Make width / number of columns dynamic
        $mform->addElement('textarea', 'question', get_string('enterquestion', 'cardbox'), 'wrap="virtual" rows="2" cols="105"');

        // Enter an image instead or as a supplement
        $options = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
                          'accepted_types' => array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'svg'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
        $mform->addElement('filemanager', 'cardimage', get_string('image', 'cardbox'), null, $options);
        
        // Enter 1...n correct answers. // XXX Make width / number of columns dynamic
        $torepeat = array($mform->createElement('textarea', 'question', get_string('enteranswer', 'cardbox'), 'wrap="virtual" rows="2" cols="105"'));
        $initialrepeats = 1;
        $options = array();
        $repeathiddenname = 'answer_repeat';
        $addfieldsname = 'answer_add_fields';
        $addfieldsno = 1;
        $addstring = get_string('addanswer', 'cardbox');
        $this->repeat_elements($torepeat, $initialrepeats, $options, $repeathiddenname, $addfieldsname, $addfieldsno, $addstring);
        
        $this->add_action_buttons(true, get_string('savecard', 'cardbox'));

    }
}
