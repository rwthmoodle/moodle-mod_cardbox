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
 * Define all the backup steps that will be used by the backup_cardbox_activity_task
 *
 * Moodle creates backups of courses or their parts by executing a so called backup plan.
 * The backup plan consists of a set of backup tasks and finally each backup task consists of one or more backup steps.
 * This file provides all the backup steps classes.
 *
 * See https://docs.moodle.org/dev/Backup_API and https://docs.moodle.org/dev/Backup_2.0_for_developers for more information.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function initialises the cardbox_contenttypes table with its 2 standard types.
 * XXX Sound support would be nice in the future.
 *
 * @global type $DB
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_cardbox_install() {

    global $DB;
    $table = 'cardbox_contenttypes';
    $condition = [];
    $types = $DB->record_exists($table, $condition);
    if (!$types) {
        $DB->insert_record($table, array('type' => 'image', 'name' => 'image'), false, false);
        $DB->insert_record($table, array('type' => 'text', 'name' => 'question'), false, false);
        $DB->insert_record($table, array('type' => 'text', 'name' => 'answer'), false, false);
    }

}