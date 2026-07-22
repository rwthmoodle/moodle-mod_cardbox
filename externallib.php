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
 * Externallib.php file for cardbox plugin.
 *
 * @package    mod_cardbox
 * @copyright  2015 Caio Bressan Doneda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/* require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/filelib.php');
require_once(dirname(__FILE__).'/classes/cardbox_webservices_handler.php'); */

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/user/externallib.php");
require_once("$CFG->dirroot/mod/cardbox/locallib.php");

/**
 * Class mod_cardbox_external
 * @copyright  2015 Caio Bressan Doneda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_cardbox_external extends external_api {

    public static function deletetopic_parameters() {
        return new external_function_parameters(
            array(
                "topicid" => new external_value(PARAM_INT, "topicid"),
                "deletecards" => new external_value(PARAM_BOOL),
            )
        );
    }

    public static function deletetopic($topicid, $deletecards) {
        global $DB;

        $params = self::validate_parameters(
            self::deletetopic_parameters(),
            array('topicid' => $topicid, 'deletecards' => $deletecards)
        );

        $cmid = self::get_cmid($params['topicid']);
        $context = context_module::instance($cmid);
        require_capability('mod/cardbox:edittopics', $context);
        if ($deletecards) {
            // Delete topic and cards.
            $success = false;
            $cardids = $DB->get_fieldset_select(
                'cardbox_cards',
                'id',
                'topic = :topic',
                ['topic' => $topicid]
            );
            if (!empty($cardids)) {
                [$insql, $params] = $DB->get_in_or_equal($cardids, SQL_PARAMS_NAMED);
                $countbefore = $DB->count_records_select(
                    'cardbox_progress',
                    "card {$insql}",
                    $params
                );
                if ($countbefore > 0) {
                    $DB->delete_records_select(
                        'cardbox_progress',
                        "card {$insql}",
                        $params
                    );
                }
                $countbefore = $DB->count_records_select(
                    'cardbox_cardcontents',
                    "card {$insql}",
                    $params
                );
                if ($countbefore > 0) {
                    $DB->delete_records_select(
                        'cardbox_cardcontents',
                        "card {$insql}",
                        $params
                    );
                }
                $countbefore = $DB->count_records_select(
                    'cardbox_cards',
                    "card {$insql}",
                    $params
                );
                if ($countbefore > 0) {
                    $DB->delete_records_select(
                        'cardbox_cards',
                        "card {$insql}",
                        $params
                    );
                }
                $countaftercards = $DB->count_records_select(
                    'cardbox_cards',
                    "card {$insql}",
                    $params
                );
                $countaftercardcontents = $DB->count_records_select(
                    'cardbox_cardcontents',
                    "card {$insql}",
                    $params
                );
                $countafterprogress = $DB->count_records_select(
                    'cardbox_progress',
                    "card {$insql}",
                    $params
                );
                
                if ($countafterprogress == 0 and $countaftercardcontents == 0 and $countaftercards == 0 ) {
                    $success = true;
                }
            }
        } else {
            // Delete topic only. Associated cards will have topic set to 
            $success = $DB->set_field_select('cardbox_cards', 'topic', null, 'topic = :id', ['id' => $params['topicid']]);
        }
        $DB->delete_records('cardbox_topics', ['id' => $params['topicid']]);
        return $success;
    }

    public static function deletetopic_returns() {
        return null;
    }

    public static function renametopic_parameters() {
        return new external_function_parameters(
            array(
                "topicid" => new external_value(PARAM_INT, "topicid"),
                "newtopicname" => new external_value(PARAM_TEXT, "newtopicname")
            )
        );
    }

    public static function renametopic($topicid, $newtopicname) {
        global $DB;

        $params = self::validate_parameters(
            self::renametopic_parameters(),
            array('topicid' => $topicid,
                  'newtopicname' => $newtopicname)
        );

        $cmid = self::get_cmid($params['topicid']);
        $context = context_module::instance($cmid);
        require_capability('mod/cardbox:edittopics', $context);

        $success = $DB->set_field_select('cardbox_topics', 'topicname', $params['newtopicname'], 'id = :id', ['id' => $params['topicid']]);

        return $success;
    }

    public static function renametopic_returns() {
        return null;
    }

    public static function get_cmid($topicid) {
        global $DB;
        $sql = 'SELECT cardboxid FROM {cardbox_topics} WHERE id = :id';
        $cardboxid = $DB->get_field_sql($sql, ['id' => $topicid]);
        $sql = 'SELECT id FROM {modules} WHERE name = "cardbox"';
        $module = $DB->get_field_sql($sql);
        $sql = 'SELECT id FROM {course_modules} WHERE module = :module AND instance= :cardboxid';
        return $DB->get_field_sql($sql, ['cardboxid' => $cardboxid, 'module' => $module]);
    }
}
