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
 * When a course renders its page layout and activities it generates the links to
 * view them using the view.php script, so the links will look like
 * <wwwrootofyoursite>/mod/cardbox/view.php?id=4, where 4 is the course module id.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('lib.php');
require_once('locallib.php');

$cmid = required_param('id', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($cmid, 'cardbox');
$cardbox = $DB->get_record('cardbox', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

$PAGE->set_title(get_string('activityname', 'cardbox'));
$PAGE->set_heading($course->fullname); // Set course name for display.

// Go to (default) page.
if (has_capability('mod/cardbox:practice', $context)) { // for students and other participants.
    $action = optional_param('action', 'practice', PARAM_ALPHA);

} else if (has_capability('mod/cardbox:approvecard', $context)) {
    $action = optional_param('action', 'review', PARAM_ALPHA);

} else { // For guests.
    $action = optional_param('action', 'addflashcard', PARAM_ALPHA);
}

$taburl = new moodle_url('/mod/cardbox/view.php', array('id' => $cmid));

$myrenderer = $PAGE->get_renderer('mod_cardbox');

$cardbox->revision = 1;

require_once($CFG->dirroot . '/mod/cardbox/controller.php');

echo $OUTPUT->footer();
