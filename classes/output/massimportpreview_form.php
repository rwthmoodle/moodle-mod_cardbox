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
 * Bulk user upload forms
 *
 * @package    mod_cardbox
 * @copyright  2021 Amrita Deb, RWTH Aachen University <Deb@itc.rwth-aachen.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_cardbox\output;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot.'/mod/cardbox/locallib.php');

class massimportpreview_form extends \moodleform {
    public function definition ($action = null, $preselected = null) {
        $mform = $this->_form;
        $data = $this->_customdata;

        $mform->addElement('hidden', 'cardboxid');
        $mform->setType('cardboxid', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHANUM);
        $mform->setDefault('action', 'massimport');

        $mform->addElement('hidden', 'step');
        $mform->setType('step', PARAM_INT);

        $mform->addElement('hidden', 'iid');
        $mform->setType('iid', PARAM_INT);

        $mform->addElement('hidden', 'count');
        $mform->setType('count', PARAM_INT);

        $mform->addElement('hidden', 'error');
        $mform->setType('error', PARAM_INT);

        $reviewbtngrp = array();
        if ($data['error'] == 0) {
            $reviewbtngrp[] =& $mform->createElement('submit', 'importbtn', get_string('massimport', 'cardbox'));
        }
        $reviewbtngrp[] =& $mform->createElement('submit', 'rejectbtn', get_string('cancel', 'cardbox'));
        $mform->addGroup($reviewbtngrp, 'reviewbtnarr', '', array(''), false);
        $mform->setType('reviewbtnarr', PARAM_RAW);
        $mform->closeHeaderBefore('reviewbtnarr');

        $this->set_data($data);
    }
}
