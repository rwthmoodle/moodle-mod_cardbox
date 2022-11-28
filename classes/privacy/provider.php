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
 * Privacy class for requesting user data.
 *
 * @package   mod_cardbox
 * @category  privacy
 * @copyright 2022 RWTH Aachen
 * @author    Amrita Deb Dutta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cardbox\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\writer;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;

class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {
    /**
     * This function implements the \core_privacy\local\metadata\provider interface.
     *
     * It describes what kind of data is stored by the cardbox, including:
     *
     * 1. Items stored in a Moodle subsystem - for example files, and ratings
     * 2. Items stored in the Moodle database
     * 3. User preferences stored site-wide within Moodle for the pdfannotator
     * 4. Data being exported to an external location
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        // 2. Describing data stored in database tables.
        // 2.1 A user's annotations in the pdf are stored.
        $collection->add_database_table(
            'cardbox_cards', [
        'author' => 'privacy:metadata:cardbox_cards:author',
        'id' => 'privacy:metadata:cardbox_cards:id',
        'timecreated' => 'privacy:metadata:cardbox_cards:timecreated',
        'timemodified' => 'privacy:metadata:cardbox_cards:timemodified',
        'approvedby' => 'privacy:metadata:cardbox_cards:approvedby',
            ], 'privacy:metadata:cardbox_cards'
        );
        // 2.2 A user's progress for the cardbox are stored.
        $collection->add_database_table(
            'cardbox_progress', [
        'userid' => 'privacy:metadata:cardbox_progress:userid',
        'lastpracticed' => 'privacy:metadata:cardbox_progress:lastpracticed',
        'repetitions' => 'privacy:metadata:cardbox_progress:repetitions',
        'card' => 'privacy:metadata:cardbox_progress:card',
            ], 'privacy:metadata:cardbox_progress'
        );
        // 2.2 A user's practice progress for the cardbox are stored to derive statistics.
        $collection->add_database_table(
            'cardbox_statistics', [
        'userid' => 'privacy:metadata:cardbox_statistics:userid',
        'cardboxid' => 'privacy:metadata:cardbox_statistics:cardboxid',
        'timeofpractice' => 'privacy:metadata:cardbox_statistics:timeofpractice',
        'numberofcards' => 'privacy:metadata:cardbox_statistics:numberofcards',
        'duration' => 'privacy:metadata:cardbox_statistics:duration',
        'percentcorrect' => 'privacy:metadata:cardbox_statistics:percentcorrect',
            ], 'privacy:metadata:cardbox_statistics'
        );
        return $collection;
    }

    /**
     * This function implements the core_privacy\local\request\plugin\provider interface.
     * It retursn a list of contexts that contain user information for the specified user.
     *
     * @param   int           $userid       The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        $params = [
            'modname' => 'cardbox',
            'contextlevel' => CONTEXT_MODULE,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
            'userid4' => $userid,
            'userid5' => $userid,
            'userid6' => $userid,
            'userid7' => $userid,
            'userid8' => $userid,
        ];

        $sql = "SELECT DISTINCT c.id
                FROM {context} c
                JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                JOIN {cardbox} cbx ON cbx.id = cm.instance
                LEFT JOIN {cardbox_statistics} cbxs ON cbx.id = cbxs.cardboxid AND cbxs.userid = :userid1
                LEFT JOIN {cardbox_cards} cbxc ON cbx.id = cbxc.cardbox
                LEFT JOIN {cardbox_progress} cbxp ON cbxc.id = cbxp.card AND cbxp.userid = :userid4
                WHERE (
                    cbxs.userid = :userid5 OR
                    cbxc.author = :userid6 OR
                    cbxc.approvedby = :userid7 OR
                    cbxp.userid = :userid8
                )";
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;

    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');
        $userid = $contextlist->get_user()->id;

        if (empty($contextlist)) {
            return;
        }

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $sql = "SELECT
                    c.id AS contextid,
                    cm.id AS cmid,
                    cbx.id AS id,
                    cbx.name AS cardboxname
                FROM {context} c
                JOIN {course_modules} cm ON cm.id = c.instanceid
                JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                JOIN {cardbox} cbx ON cbx.id = cm.instance
                WHERE c.id {$contextsql}";
        $params = ['modname' => 'cardbox'];
        $params = array_merge($params, $contextparams);

        $cardboxes = $DB->get_recordset_sql($sql, $params);
        foreach ($cardboxes as $cardbox) {
            $context = \context::instance_by_id($cardbox->contextid);

            // Get all cards with contents created by the user.
            $sql = "SELECT cc.id, cc.card,
                        (SELECT topicname FROM {cardbox_topics} WHERE id = c.topic) AS topic,
                        timecreated,
                        timemodified,
                        CASE
                            WHEN cc.cardside = 0 THEN 'Question'
                            WHEN cc.cardside = 1 THEN 'Answer'
                        END AS cardside,
                        CASE
                            WHEN cc.contenttype = 0 THEN 'Image'
                            WHEN cc.contenttype = 1 THEN 'Text'
                            WHEN cc.contenttype = 2 THEN 'Audio'
                        END AS contenttype,
                        CASE
                            WHEN cc.area = 0 THEN 'Main Info'
                            WHEN cc.area = 1 THEN 'Context Info'
                            WHEN cc.area = 2 THEN 'Image Description'
                            WHEN cc.area = 3 THEN 'Answer Suggestion'
                        END AS infotype,
                        cc.content
                    FROM {cardbox_cards} c
                    JOIN {cardbox_cardcontents} cc ON c.id = cc.card
                    WHERE c.author = :authorid
                        AND c.cardbox = :cardboxid";
            $cards = $DB->get_records_sql($sql, ['authorid' => $userid, 'cardboxid' => $cardbox->id]);
            $usercreatedcards = [];
            foreach ($cards as $c) {
                $usercreatedcards[] = (object) [
                    'cardid' => $c->card,
                    'topic' => $c->topic,
                    'timecreated' => transform::datetime($c->timecreated),
                    'timemodified' => $c->timemodified ? transform::datetime($c->timemodified) : 'Never',
                    'cardside' => $c->cardside,
                    'contenttype' => $c->contenttype,
                    'infotype' => $c->infotype,
                    'content' => $c->content
                ];
            }

            // Get all cards with contents approved by the user.
            $sql = "SELECT cc.id, cc.card,
                        (SELECT topicname FROM {cardbox_topics} WHERE id = c.topic) AS topic,
                        timecreated,
                        timemodified,
                        CASE
                            WHEN cc.cardside = 0 THEN 'Question'
                            WHEN cc.cardside = 1 THEN 'Answer'
                        END AS cardside,
                        CASE
                            WHEN cc.contenttype = 0 THEN 'Image'
                            WHEN cc.contenttype = 1 THEN 'Text'
                            WHEN cc.contenttype = 2 THEN 'Audio'
                        END AS Contentype,
                        CASE
                            WHEN cc.area = 0 THEN 'Main Info'
                            WHEN cc.area = 1 THEN 'Context Info'
                            WHEN cc.area = 2 THEN 'Image Description'
                            WHEN cc.area = 3 THEN 'Answer Suggestion'
                        END AS InfoType,
                        cc.content
                    FROM {cardbox_cards} c
                    JOIN {cardbox_cardcontents} cc ON c.id = cc.card
                    WHERE c.approvedby = :approver
                        AND c.cardbox = :cardboxid";
            $cards = $DB->get_records_sql($sql, ['approver' => $userid, 'cardboxid' => $cardbox->id]);
            $userapprovedcards = [];
            foreach ($cards as $c) {
                $userapprovedcards[] = (object) [
                    'cardid' => $c->card,
                    'topic' => $c->topic,
                    'timecreated' => transform::datetime($c->timecreated),
                    'timemodified' => $c->timemodified ? transform::datetime($c->timemodified) : 'Never',
                    'cardside' => $c->cardside,
                    'contenttype' => $c->contenttype,
                    'infotype' => $c->infotype,
                    'content' => $c->content
                ];
            }

            // Get user progress for the cardbox.
            $sql = "SELECT card, cardposition, lastpracticed, repetitions
                     FROM {cardbox_progress}
                     WHERE card IN (SELECT id FROM {cardbox_cards} WHERE cardbox = :cardboxid)
                         AND userid = :userid";
            $progresses = $DB->get_records_sql($sql, ['userid' => $userid, 'cardboxid' => $cardbox->id]);
            $userprogress = [];
            foreach ($progresses as $p) {
                $key = 'Card '.$p->card;
                $userprogress[$key] = (object) [
                    'deck' => $p->cardposition,
                    'lastpracticed' => $p->lastpracticed ? transform::datetime($p->lastpracticed) : 'Never',
                    'repetitions' => $p->repetitions
                ];
            }

            // Get user stats for entire cardbox.
            $sql = "SELECT cardboxid, timeofpractice, numberofcards, duration, percentcorrect
                     FROM {cardbox_statistics}
                     WHERE userid = :userid
                         AND cardboxid = :cardboxid";
            $statistics = $DB->get_records_sql($sql, ['userid' => $userid, 'cardboxid' => $cardbox->id]);
            $userstats = [];
            foreach ($statistics as $s) {
                $key = 'Cardbox '.$s->cardboxid;
                $cbxname = $DB->get_field('cardbox', 'name', ['id' => $s->cardboxid]);
                $userstats[$key] = (object) [
                    'cardboxname' => $cbxname,
                    'timeofpractice' => transform::datetime($s->timeofpractice),
                    'numberofcards' => $s->numberofcards,
                    'duration' => $s->duration,
                    'percentcorrect' => $s->percentcorrect
                ];
            }

            $cardbox->usercreatedcards = $usercreatedcards;
            $cardbox->userapprovedcards = $userapprovedcards;
            $cardbox->userprogress = $userprogress;
            $cardbox->userstats = $userstats;

            writer::with_context($context)->export_data([], $cardbox);
        }
        $cardboxes->close();
    }

    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }
        $cardboxid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid]);
        if ($cardboxid === false) {
            return;
        }

        // Delete all statistics for this cardbox instance.
        $DB->delete_records('cardbox_statistics', ['cardboxid' => $cardboxid]);

        // Delete user progress for this cardbox instance.
        $listofcards = $DB->get_records('cardbox_cards', ['cardbox' => $cardboxid]);
        foreach ($listofcards as $cardid) {
            $DB->delete_records('cardbox_progress', ['card' => $cardid->id]);
        }

        // Remove author and approver details from cards. The card on a whole doesnt get deleted.
        $DB->set_field('cardbox_cards', 'author', 0, ['cardbox' => $cardboxid]);
        $DB->set_field('cardbox_cards', 'approvedby', 0, ['cardbox' => $cardboxid]);
    }

    /**
     *
     * Delete personal data for the user in a list of contexts.
     *
     * @param \mod_cardbox\privacy\approved_contextlist $contextlist List of contexts to delete data from.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            $cardboxid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid]);
            if ($cardboxid === false) {
                continue;
            }

            // Delete all statistics for the user in this cardbox instance.
            $DB->delete_records('cardbox_statistics', ['cardboxid' => $cardboxid, 'userid' => $userid]);

            // Delete user progress for this cardbox instance.
            $DB->delete_records('cardbox_progress', ['userid' => $userid]);

            // Remove author and approver details from cards. The card on a whole doesnt get deleted.
            $usercreatedcards = $DB->get_records('cardbox_cards', ['cardbox' => $cardboxid, 'author' => $userid]);
            foreach ($usercreatedcards as $card) {
                $DB->set_field('cardbox_cards', 'author', 0, ['cardbox' => $cardboxid, 'id' => $card->id]);
            }
            $userapprovedcards = $DB->get_records('cardbox_cards', ['cardbox' => $cardboxid, 'approvedby' => $userid]);
            foreach ($userapprovedcards as $card) {
                $DB->set_field('cardbox_cards', 'approvedby', 0, ['cardbox' => $cardboxid, 'id' => $card->id]);
            }
        }
    }
}
