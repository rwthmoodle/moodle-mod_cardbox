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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('cardbox_statistics_heading', get_string('statistics_heading', 'cardbox'), ''));

    $settings->add(new admin_setting_configtext('mod_cardbox/weekly_statistics_user_practice_threshold',
                    get_string('weekly_users_practice_threshold', 'cardbox'),
                    get_string('weekly_users_practice_threshold_desc', 'cardbox'), 5, PARAM_INT));

    $settings->add(new admin_setting_configtext('mod_cardbox/weekly_statistics_enrolled_students_threshold',
                    get_string('weekly_enrolled_students_threshold', 'cardbox'),
                    get_string('weekly_enrolled_students_threshold_desc', 'cardbox'), 10, PARAM_INT));
}
