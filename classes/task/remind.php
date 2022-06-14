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

        global $DB;

        $sm = get_string_manager();

        $sql = "SELECT cm.id, cm.course AS courseid, cm.id AS coursemoduleid, ca.name AS cardboxname, co.fullname AS coursename "
                . "FROM {course_modules} cm "
                . "LEFT JOIN {modules} m ON cm.module = m.id "
                . "JOIN {cardbox} ca ON cm.instance = ca.id "
                . "LEFT JOIN {course} co ON cm.course = co.id "
                . "WHERE m.name = ?";

        $cardboxes = $DB->get_records_sql($sql, array('cardbox'));

        foreach ($cardboxes as $cardbox) {

            $info = new \stdClass();
            $info->cardboxname = $cardbox->cardboxname;
            $info->coursename = $cardbox->coursename;

            $cardbox->context = \context_module::instance($cardbox->coursemoduleid);

            $recipients = get_enrolled_users($cardbox->context, 'mod/cardbox:practice');

            foreach ($recipients as $recipient) {
                $message = new \core\message\message();
                $message->component = 'mod_cardbox';
                $message->name = 'memo';
                $message->userfrom = core_user::get_noreply_user();
                $message->userto = $recipient;
                $message->subject = $sm->get_string('remindersubject', 'cardbox', null, $recipient->lang);
                $message->fullmessage = $sm->get_string('remindergreeting', 'cardbox', $recipient->username, $recipient->lang).' '.
                                        $sm->get_string('remindermessagebody', 'cardbox', null, $recipient->lang) . ' ' .
                                        $sm->get_string('reminderfooting', 'cardbox', $info, $recipient->lang);
                $message->fullmessageformat = FORMAT_MARKDOWN;
                $message->fullmessagehtml = '<p>'.
                        $sm->get_string('remindergreeting', 'cardbox', $recipient->username, $recipient->lang).
                        '</p><p>'.$sm->get_string('remindermessagebody', 'cardbox', null, $recipient->lang).
                '</p><p><em>'.$sm->get_string('reminderfooting', 'cardbox', $info, $recipient->lang) . '</em></p>';
                $message->smallmessage = 'small message';
                $message->notification = 1;
                $message->courseid = $cardbox->courseid;

                message_send($message);

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
