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
    private $numberofcardsmin;
    private $numberofcardsmax;
    private $durationmin;
    private $durationmax;

    public function __construct($cardboxid, $ismanager) {

        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');

        $this->dates = [];
        $this->performances = [];
        $this->weeks = [];
        $this->numberofcardsavg = [];
        $this->durationofsessionavg = [];
        $this->tooltips = new stdClass();
        $this->tooltips->durationofsession = new stdClass();
        $this->tooltips->numberofcards = new stdClass();
        $this->tooltips->durationofsession->min = [];
        $this->tooltips->durationofsession->average = [];
        $this->tooltips->durationofsession->max = [];
        $this->tooltips->numberofcards->min = [];
        $this->tooltips->numberofcards->average = [];
        $this->tooltips->numberofcards->max = [];

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
                if ($record->numberofcards === null || $record->duration === null) {
                    // Do not count unfinished practice sessions.
                    continue;
                }
                if ($record->timeofpractice > $i && $record->timeofpractice < $mondayweeklater) {
                    $numberofcards += $record->numberofcards;
                    $durationofsession += $record->duration;
                    $distinctusers[$record->userid] = true;
                    $count++;
                }
            }
            $this->weeks[] = "" .cardbox_get_user_date_short($i). " - " .cardbox_get_user_date_short($mondayweeklater - 86400);

            $sqlmin = "SELECT MIN(numberofcards) AS numberofcards, MIN(duration) AS duration"
                . " FROM {cardbox_statistics}"
                . " WHERE cardboxid = :cbid AND timeofpractice > :start AND timeofpractice < :end";
            $params = ['cbid' => $cardboxid, 'start' => $i, 'end' => $mondayweeklater];
            $sqlmax = "SELECT MAX(numberofcards) AS numberofcards, MAX(duration) AS duration"
                . " FROM {cardbox_statistics}"
                . " WHERE cardboxid = :cbid AND timeofpractice > :start AND timeofpractice < :end";
            $numberofcardsmin = $DB->get_record_sql($sqlmin, $params);
            $numberofcardsmax = $DB->get_record_sql($sqlmax, $params);

            $practicingusersthreshold = get_config('mod_cardbox', 'weekly_statistics_user_practice_threshold');
            if ($count == 0 || count($distinctusers) < $practicingusersthreshold) {
                $durationofsession = 0;
                $numberofcards = 0;
                $this->numberofcardsmin[] = 0;
                $durationofsessiontooltipmin = 0;
                $this->numberofcardsmax[] = 0;
                $durationofsessiontooltipmax = 0;
                $this->durationmin[] = 0;
                $numberofcardstooltipmin = 0;
                $this->durationmax[] = 0;
                $numberofcardstooltipmax = 0;
            } else {
                $durationofsession = round(($durationofsession) / 60 / $count);
                $numberofcards = round($numberofcards / $count);
                $numberofcardstooltipmin = get_string('numberofcardsmin', 'cardbox') . ": " . $numberofcardsmin->numberofcards;
                $this->numberofcardsmin[] = $numberofcardsmin->numberofcards;
                $numberofcardstooltipmax = get_string('numberofcardsmax', 'cardbox') . ": " . $numberofcardsmax->numberofcards;
                $this->numberofcardsmax[] = $numberofcardsmax->numberofcards;
                $durationofsessiontooltipmin = get_string('durationmin', 'cardbox') . ": " . round($numberofcardsmin->duration / 60);
                $this->durationmin[] = round($numberofcardsmin->duration / 60);
                $durationofsessiontooltipmax = get_string('durationmax', 'cardbox') . ": " . round($numberofcardsmax->duration / 60);
                $this->durationmax[] = round($numberofcardsmax->duration / 60);
            }
            $this->durationofsessionavg[] = $durationofsession;
            $this->numberofcardsavg[] = $numberofcards;

            $durationofsessiontooltipavg = get_string('durationavg', 'cardbox') . ": " . $durationofsession;
            $numberofcardstooltipavg = get_string('numberofcardsavg', 'cardbox') . ": " . $numberofcards;
            if (count($distinctusers) < $practicingusersthreshold) {
                $belowthreshold = get_string('linegraphtooltiplabel_below_threshold', 'cardbox', $practicingusersthreshold);
                $numberofcardstooltipmin = $belowthreshold;
                $numberofcardstooltipavg = $belowthreshold;
                $numberofcardstooltipmax = $belowthreshold;
                $durationofsessiontooltipmin = $belowthreshold;
                $durationofsessiontooltipavg = $belowthreshold;
                $durationofsessiontooltipmax = $belowthreshold;
            }
            $this->tooltips->durationofsession->min[] = $durationofsessiontooltipmin;
            $this->tooltips->durationofsession->average[] = $durationofsessiontooltipavg;
            $this->tooltips->durationofsession->max[] = $durationofsessiontooltipmax;
            $this->tooltips->numberofcards->min[] = $numberofcardstooltipmin;
            $this->tooltips->numberofcards->average[] = $numberofcardstooltipavg;
            $this->tooltips->numberofcards->max[] = $numberofcardstooltipmax;
        }
    }

    public function export_for_template(\renderer_base $output) {

        $data = array();
        $data['dates'] = $this->dates;
        $data['performances'] = $this->performances;
        $data['ismanager'] = $this->ismanager;

        $data['displayweeklystats'] = $this->displayweeklystats;
        $data['weeks'] = $this->weeks;
        $data['numberofcardsavg'] = $this->numberofcardsavg;
        $data['durationofsessionavg'] = $this->durationofsessionavg;
        $data['tooltips'] = $this->tooltips;
        $data['numberofcardsmin'] = $this->numberofcardsmin;
        $data['numberofcardsmax'] = $this->numberofcardsmax;
        $data['durationofsessionmin'] = $this->durationmin;
        $data['durationofsessionmax'] = $this->durationmax;
        return $data;

    }
}