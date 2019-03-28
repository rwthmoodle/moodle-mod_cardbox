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
    
    public function __construct($autocorrection, $cardboxid) {
        
        $this->cardbox_prepare_topics_to_study($cardboxid);
        
        if ($autocorrection == 1) {
            $this->autocorrectionoption = true;
        }
        
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

        $topiclist = cardbox_get_topics($cardboxid);

        foreach ($topiclist as $key => $value) {
            $this->topics[] = array('value' => $key, 'label' => $value);
        }

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
        $data['helpbuttoncorrectionmode'] = $OUTPUT->help_icon('choosecorrectionmode', 'cardbox');
        $data['helpbuttontopic'] = $OUTPUT->help_icon('weightopic', 'cardbox');

        return $data;

    }

}
