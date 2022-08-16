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
 * Moodle restores data from course backups by executing a so called restore plan.
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

/**
 * Define all the restore steps that will be used by the restore_cardbox_activity_task
 */

/**
 * Structure step to restore one cardbox activity
 */
class restore_cardbox_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();

        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('cardbox', '/activity/cardbox');
        $paths[] = new restore_path_element('cardbox_topics', '/activity/cardbox/topics/topic');
        $paths[] = new restore_path_element('cardbox_cards', '/activity/cardbox/cards/card');
        $paths[] = new restore_path_element('cardbox_cardcontents', '/activity/cardbox/cards/card/cardcontents/cardcontent');
        if ($userinfo != 0) {
            $paths[] = new restore_path_element('cardbox_statistics', '/activity/cardbox/statistics/statistic');
            $paths[] = new restore_path_element('cardbox_progress', '/activity/cardbox/cards/card/progress/singleprogress');
        }
        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    protected function process_cardbox($data) {

        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('cardbox', $data); // Insert the cardbox record.

        $this->apply_activity_instance($newitemid); // Immediately after inserting "activity" record, call this.
    }

    protected function process_cardbox_topics($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->cardboxid = $this->get_new_parentid('cardbox');

        $newitemid = $DB->insert_record('cardbox_topics', $data);
        $this->set_mapping('cardbox_topics', $oldid, $newitemid);
    }

    protected function process_cardbox_cards($data) {

        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->cardbox = $this->get_new_parentid('cardbox');

        $newitemid = $DB->insert_record('cardbox_cards', $data);
        $this->set_mapping('cardbox_cards', $oldid, $newitemid);

    }

    protected function process_cardbox_cardcontents($data) {

        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->card = $this->get_new_parentid('cardbox_cards');

        $newitemid = $DB->insert_record('cardbox_cardcontents', $data);
        $this->set_mapping('cardbox_cardcontents', $oldid, $newitemid, true);

    }

    protected function process_cardbox_statistics($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->cardboxid = $this->get_new_parentid('cardbox');
        $data->timeofpractice = $this->apply_date_offset($data->timeofpractice);

        $newitemid = $DB->insert_record('cardbox_statistics', $data);
        $this->set_mapping('cardbox_statistics', $oldid, $newitemid);
    }

    protected function process_cardbox_progress($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->card = $this->get_new_parentid('cardbox_cards');
        $data->lastpracticed = $this->apply_date_offset($data->lastpracticed);

        $newitemid = $DB->insert_record('cardbox_progress', $data);
        $this->set_mapping('cardbox_progress', $oldid, $newitemid);
    }

    protected function after_execute() {
        // Add cardbox related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_cardbox', 'intro', null);
        $this->add_related_files('mod_cardbox', 'content', 'cardbox_cardcontents'); // Cardimage or content?.
    }
}
