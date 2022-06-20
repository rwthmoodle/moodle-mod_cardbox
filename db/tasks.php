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
 * This file sets the default schedule for system notifications (practice reminders).
 * Managers can change the timing via site administration -> server -> scheduled tasks.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$tasks = [
    [
        'classname' => 'mod_cardbox\task\remind',
        'blocking' => 0, // does not prevent other scheduled tasks from running at the same time.
        'minute' => '00',
        'hour' => '15',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '0', // 0 and 7 are Sunday.
    ],
];
