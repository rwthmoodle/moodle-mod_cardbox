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
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_cardbox\task;

use core_user;

/**
 * This clas
 */
class remind extends \core\task\scheduled_task {

    public function execute() {
        global $DB, $SESSION;

        $sql = "SELECT cm.id, cm.course AS courseid, cm.id AS coursemoduleid, ca.name AS cardboxname, co.fullname AS coursename "
                . "FROM {course_modules} cm "
                . "LEFT JOIN {modules} m ON cm.module = m.id "
                . "JOIN {cardbox} ca ON cm.instance = ca.id "
                . "LEFT JOIN {course} co ON cm.course = co.id "
                . "WHERE m.name = ? AND ca.enablenotifications = 1";
        $cardboxes = $DB->get_records_sql($sql, ['cardbox']);

        foreach ($cardboxes as $cardbox) {
            $cardbox->context = \context_module::instance($cardbox->coursemoduleid);
            $recipients = get_enrolled_users($cardbox->context, 'mod/cardbox:practice');

            foreach ($recipients as $recipient) {
                $modinfo = get_fast_modinfo($cardbox->courseid, $recipient->id);
                $cm = $modinfo->get_cm($cardbox->coursemoduleid);
                $info = new \core_availability\info_module($cm);
                $information = '';
                if (!$info->is_available($information, false, $recipient->id)) {
                    continue;
                }

                // Change language temporarily.
                $course = $info->get_course();
                if (!empty($course->lang)) {
                    // Use course language if it's enforced.
                    $lang = $course->lang;
                } else {
                    // Use recipient's preferred language.
                    $lang = $recipient->lang;
                }
                $forcelangisset = isset($SESSION->forcelang);
                if ($forcelangisset) {
                    $forcelang = $SESSION->forcelang;
                }
                $SESSION->forcelang = $lang;

                $a = new \stdClass();
                $a->cardboxname = format_string($cardbox->cardboxname);
                $a->coursename = format_string($cardbox->coursename);

                $message = new \core\message\message();
                $message->component = 'mod_cardbox';
                $message->name = 'memo';
                $message->userfrom = core_user::get_noreply_user();
                $message->userto = $recipient;
                $message->subject = get_string('remindersubject', 'cardbox');
                $message->fullmessage = get_string('remindergreeting', 'cardbox', $recipient->firstname).' '.
                                        get_string('remindermessagebody', 'cardbox') . ' ' .
                                        get_string('reminderfooting', 'cardbox', $a);
                $message->fullmessageformat = FORMAT_MARKDOWN;
                $message->fullmessagehtml = '<p>'.
                        get_string('remindergreeting', 'cardbox', $recipient->firstname).
                        '</p><p>'.get_string('remindermessagebody', 'cardbox').
                '</p><p><em>'.get_string('reminderfooting', 'cardbox', $a) . '</em></p>';
                $message->smallmessage = 'small message';
                $message->notification = 1;
                $message->courseid = $cardbox->courseid;

                message_send($message);

                // Reset language.
                if ($forcelangisset) {
                    $SESSION->forcelang = $forcelang;
                } else {
                    unset($SESSION->forcelang);
                }
            }

        }

    }

    /**
     * Function returns the name of the task as shown in admin screens
     */
    public function get_name(): string {
        return get_string('send_practice_reminders', 'cardbox');
    }

}