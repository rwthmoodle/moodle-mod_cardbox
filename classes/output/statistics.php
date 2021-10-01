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
    private $weeks;
    private $numberofcards;
    private $durationofsession;

    public function __construct($cardboxid, $ismanager) {

        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/mod/cardbox/locallib.php');

        $this->dates = array(); //new stdClass();
        $this->performances = array(); //new stdClass();
        $this->weeks = array(); //new stdClass();
        $this->numberofcards = array(); //new stdClass();
        $this->durationofsession = array(); //new stdClass();

        $data = $DB->get_records('cardbox_statistics', array('userid' => $USER->id, 'cardboxid' => $cardboxid), '', 'timeofpractice, percentcorrect');

        foreach ($data as $record) {
            $this->dates[] = cardbox_get_user_date($record->timeofpractice);
            $this->performances[] = $record->percentcorrect;
        }
        $this->ismanager = $ismanager;

        $data = $DB->get_records('cardbox_statistics', array('cardboxid' => $cardboxid), '', 'timeofpractice, numberofcards, duration');
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
            foreach ($data as $record) {
                if ($record->timeofpractice > $i && $record->timeofpractice < $mondayweeklater) {
                    $numberofcards += $record->numberofcards;
                    $durationofsession += $record->duration;
                    $count++;
                }

            }
            $this->weeks[] = "" .cardbox_get_user_date_short($i). " - " .cardbox_get_user_date_short($mondayweeklater - 86400);
            if ($count != 0) {
                $this->durationofsession[] = round(($durationofsession) / 60 / $count);
                $this->numberofcards[] = round($numberofcards / $count);
            } else {
                $this->durationofsession[] = 0;
                $this->numberofcards[] = 0;
            }
        }

        /* foreach ($data as $record) {
            $this->numberofcards[] = $record->numberofcards;
            $this->durationofsession[] = round(($record->duration) / 60);
        } */

    }

    public function export_for_template(\renderer_base $output) {

        $data = array();
        $data['dates'] = $this->dates;
        $data['performances'] = $this->performances;
        $data['ismanager'] = $this->ismanager;

        $data['weeks'] = $this->weeks;
        $data['numberofcards'] = $this->numberofcards;
        $data['durationofsession'] = $this->durationofsession;
        return $data;

    }
}