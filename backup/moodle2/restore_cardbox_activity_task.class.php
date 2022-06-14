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
 * Moodle restores data from course backups by executing so called restore plan.
 * The restore plan consists of a set of restore tasks and finally each restore task consists of one or more restore steps.
 * You as the developer of a plugin will have to implement one restore task that deals with your plugin data.
 * Most plugins have their restore tasks consisting of a single restore step
 * - the one that parses the plugin XML file and puts the data into its tables.
 *
 * See https://docs.moodle.org/dev/Backup_API and https://docs.moodle.org/dev/Backup_2.0_for_developers for more information.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/cardbox/backup/moodle2/restore_cardbox_stepslib.php'); // Because it exists (must).

class restore_cardbox_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }
    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Cardbox only has one structure step.
        $this->add_step(new restore_cardbox_activity_structure_step('cardbox_structure', 'cardbox.xml'));
    }

    /*************************************** optional *******************************************/

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder.
     */
    public static function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('cardbox', array('intro'), 'cardbox');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('CARDBOXVIEWBYID', '/mod/cardbox/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('CARDBOXINDEX', '/mod/cardbox/index.php?id=$1', 'course');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * pdfannotator logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    public static function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('cardbox', 'add', 'view.php?id={course_module}', '{cardbox}');
        $rules[] = new restore_log_rule('cardbox', 'update', 'view.php?id={course_module}', '{cardbox}');
        $rules[] = new restore_log_rule('cardbox', 'view', 'view.php?id={course_module}', '{cardbox}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    public static function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('cardbox', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
