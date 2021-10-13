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
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Description of statistics
 *
 */
class cardbox_statistics implements \renderable, \templatable {

    private $dates;
    private $performances;
    private $ismanager;
    private $displayweeklystats;
    private $weeks;
    private $numberofcards;
    private $durationofsession;
    private $tooltips;

    public function __construct($cardboxid, $ismanager) {

        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');

        $this->dates = [];
        $this->performances = [];
        $this->weeks = [];
        $this->numberofcards = [];
        $this->durationofsession = [];
        $this->tooltips = new stdClass();
        $this->tooltips->durationofsession = [];
        $this->tooltips->numberofcards = [];

        $data = $DB->get_records('cardbox_statistics', array('userid' => $USER->id, 'cardboxid' => $cardboxid), '', 'timeofpractice, percentcorrect');

        foreach ($data as $record) {
            $this->dates[] = cardbox_get_user_date($record->timeofpractice);
            $this->performances[] = $record->percentcorrect;
        }
        $this->ismanager = $ismanager;

        $enrolledstudentsthreshold = get_config('mod_cardbox', 'weekly_statistics_enrolled_students_threshold');
        $cm = get_coursemodule_from_instance('cardbox', $cardboxid);
        $context = context_module::instance($cm->id);
        $enrolledstudents = get_enrolled_users($context, 'mod/cardbox:practice');
        $this->displayweeklystats = count($enrolledstudents) >= $enrolledstudentsthreshold;
        if (!$this->displayweeklystats) {
            return;
        }

        $data = $DB->get_records('cardbox_statistics', array('cardboxid' => $cardboxid), '', 'timeofpractice, numberofcards, duration, userid');
        $endoflastweek = new DateTime();
        $endoflastweek->modify('Monday this week');
        $endoflastweek = $endoflastweek->format('U');

        $startdate = $endoflastweek - (10 * (86400 * 7));
        $week = 1;

        for ($i = $startdate; $i < $endoflastweek; $i = $i + (86400 * 7), $week++) {
            $mondayweeklater = $i + (86400 * 7);
            $numberofcards = 0;
            $durationofsession = 0;
            $count = 0;
            $distinctusers = [];
            foreach ($data as $record) {
                if ($record->timeofpractice > $i && $record->timeofpractice < $mondayweeklater) {
                    $numberofcards += $record->numberofcards;
                    $durationofsession += $record->duration;
                    $distinctusers[$record->userid] = true;
                    $count++;
                }
            }
            $this->weeks[] = "" .cardbox_get_user_date_short($i). " - " .cardbox_get_user_date_short($mondayweeklater - 86400);

            $practicingusersthreshold = get_config('mod_cardbox', 'weekly_statistics_user_practice_threshold');
            if ($count == 0 || count($distinctusers) < $practicingusersthreshold) {
                $durationofsession = 0;
                $numberofcards = 0;
            } else {
                $durationofsession = round(($durationofsession) / 60 / $count);
                $numberofcards = round($numberofcards / $count);
            }
            $this->durationofsession[] = $durationofsession;
            $this->numberofcards[] = $numberofcards;

            $durationofsessiontooltip = $durationofsession;
            $numberofcardstooltip = $numberofcards;
            if (count($distinctusers) < $practicingusersthreshold) {
                $belowthreshold = get_string('linegraphtooltiplabel_below_threshold', 'cardbox', 5);
                $durationofsessiontooltip = $belowthreshold;
                $numberofcardstooltip = $belowthreshold;
            }
            $this->tooltips->durationofsession[] = $durationofsessiontooltip;
            $this->tooltips->numberofcards[] = $numberofcardstooltip;
        }
    }

    public function export_for_template(\renderer_base $output) {

        $data = array();
        $data['dates'] = $this->dates;
        $data['performances'] = $this->performances;
        $data['ismanager'] = $this->ismanager;

        $data['displayweeklystats'] = $this->displayweeklystats;
        $data['weeks'] = $this->weeks;
        $data['numberofcards'] = $this->numberofcards;
        $data['durationofsession'] = $this->durationofsession;
        $data['tooltips'] = $this->tooltips;
        return $data;

    }
}