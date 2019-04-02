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
 * Description of statistics
 *
 */
class cardbox_statistics implements \renderable, \templatable {
    
    private $dates;
    private $performances;
    
    public function __construct($cardboxid) {
        
        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');
        
        $this->dates = array();//new stdClass();
        $this->performances = array(); //new stdClass();
        
        $data = $DB->get_records('cardbox_statistics', array('userid' => $USER->id, 'cardboxid' => $cardboxid), '', 'timeofpractice, percentcorrect');
        
        foreach ($data as $record) {
            $this->dates[] = cardbox_get_user_date($record->timeofpractice);
            $this->performances[] = $record->percentcorrect;
        }
    }

    public function export_for_template(\renderer_base $output) {

        $data = array();
        $data['dates'] = $this->dates;
        $data['performances'] = $this->performances;
        return $data;

    }
}