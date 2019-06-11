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
namespace mod_cardbox\taks;

class cardbox_remind extends \core\task\scheduled_task {
    
    public function execute() {
        
        global $DB, $USER;
        
        $sql = "SELECT co.id AS courseid, cm.id AS coursemoduleid, ca.name AS cardboxname, co.fullname AS coursename "
                . "FROM {course_modules} cm "
                . "JOIN {modules} m ON cm.module = m.id "
                . "JOIN {cardbox} ca ON cm.instance = ca.id "
                . "JOIN {course} co ON cm.course = co.id "
                . "WHERE m.name = ?";

        $cardboxes =  $DB->get_records_sql($sql, array('cardbox'));

        foreach ($cardboxes as $cardbox) {

            $cardbox->context = context_module::instance($cardbox->coursemoduleid);

            $recipients = get_enrolled_users($cardbox->context, 'mod/cardbox:practice');

            foreach ($recipients as $recipient) {
                $message = new \core\message\message();
                $message->component = 'mod_cardbox';
                $message->name = 'memo';
                $message->userfrom = $USER; // TODO replace
                $message->userto = $recipient;
                $message->subject = 'practice memo'; // XXX Durch get_string() ersetzen
                $message->fullmessage = 'message body';
                $message->fullmessageformat = FORMAT_MARKDOWN;
                $message->fullmessagehtml = '<p>message body</p>';
                $message->smallmessage = 'small message';
                $message->notification = 1; // For personal messages '0'. Important: the 1 without '' and 0 with ''.
                $message->contexturl = 'http://GalaxyFarFarAway.com';
                $message->contexturlname = 'Context name';
    //            $message->replyto = "random@example.com";
                $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
    //            $message->set_additional_content('email', $content);
                $message->courseid = $cardbox->courseid;


                $messageid = message_send($message);

                return $messageid;

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
