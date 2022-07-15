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
 * This page is used by Moodle when listing all the instances of the cardbox module
 * that are in a particular course with the course id being passed to this script.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/cardbox/locallib.php');
require_once('model/cardcollection.class.php'); // model.

// For this type of page this is the course id.
$id = required_param('id', PARAM_INT); // Course ID.

$courseid = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$course = get_course($courseid->id);
require_login($course);
$PAGE->set_url('/mod/cardbox/index.php', array('id' => $id));
$PAGE->set_pagelayout('incourse');

// Print the header.
$strplural = get_string("modulenameplural", "cardbox");
$PAGE->navbar->add($strplural);
$PAGE->set_title($strplural);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($strplural));

$context = context_course::instance($course->id);

require_capability('mod/cardbox:view', $context);

$strplural = get_string('modulenameplural', 'cardbox');
$usesections = course_format_uses_sections($course->format);
$modinfo = get_fast_modinfo($course);
if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $sections = $modinfo->get_section_info_all();
}
$html = '<table class="generaltable" width="90%" cellspacing="1" cellpadding="5" text-align="center" ><thead>' . "\n";
$html .= '<tr><th class="header-c0  cbx-center-align" scope="col">'.get_string('choosetopic', 'cardbox').'</th>';
$html .= '<th class="header-c1  cbx-center-align" scope="col">' . get_string('modulename', 'cardbox') . '</th>';
$html .= '<th class="header-c2  cbx-center-align" scope="col"> '.ucfirst(get_string('barchartyaxislabel', 'cardbox')).' </th>';
if (!has_capability('mod/cardbox:practice', $context)) {
    $html .= '</tr></thead><tbody>';
} else {
    $html .= '<th class="header-c3  cbx-center-align" scope="col">'.ucfirst(get_string('lastpractise', 'cardbox')).'</th>';
    $html .= '<th class="header-c4  cbx-center-align" scope="col">'.ucfirst(get_string('newcard', 'cardbox')).'</th>';
    $html .= '<th class="header-c3  cbx-center-align" scope="col">'.ucfirst(get_string('knowncard', 'cardbox')).'</th>';
    $html .= '<th class="header-c5  cbx-center-align" scope="col">'.ucfirst(get_string('flashcards', 'cardbox').' '.
            get_string('flashcardsdue', 'cardbox')).'</th>';
    $html .= '<th class="header-c6  cbx-center-align" scope="col">'.ucfirst(get_string('flashcards', 'cardbox').' '.
            get_string('flashcardsnotdue', 'cardbox')).'</th></tr></thead><tbody>';
}
foreach ($modinfo->instances['cardbox'] as $cm) {
    if (!$cm->uservisible) {
        continue;
    }
    $sectionname = '';
    if ($usesections && $cm->sectionnum >= 0) {
        $sectionname = get_section_name($course, $sections[$cm->sectionnum]); // Gives the section name where the cardbox is.
        if ($DB->record_exists('cardbox_cards', ['cardbox' => $cm->instance, 'approved' => '1'])) {
            // If cardbox activity has cards.
            $html .= '<tr>';
            // Row begins with section and cardbox activity name.
            $html .= '<td class="cell-c0  cbx-center-align" >'.$sectionname.'</td>';
            $html .= '<td class="cell-c1  cbx-center-align" >
                        <a href="'.$CFG->wwwroot.'/mod/cardbox/view.php?id='.$cm->id.'">'.$cm->get_formatted_name().'</a></td>';
            // Number of cards in the cardbox.
            $cardcount = $DB->count_records('cardbox_cards', ['cardbox' => $cm->instance, 'approved' => '1']);
            $html .= '<td class="cell-c2  cbx-center-align" >'.$cardcount.'</td>';
            if (has_capability('mod/cardbox:practice', $context)) {
                // Last Practised column.
                $lastpractised = $DB->get_records_sql('SELECT max(lastpracticed) as lstprac
            FROM {cardbox_progress} cbp
            WHERE cbp.card in (SELECT id from {cardbox_cards} cc
                                WHERE cc.cardbox = :cardbox and approved = :approved)
                                AND cbp.userid = :userid',
                                ['cardbox' => $cm->instance, 'userid' => $USER->id, 'approved' => '1']);
                if (implode(',', array_keys($lastpractised)) == '') {
                    $html .= '<td class="cell-c3  cbx-center-align">'.get_string('nopractise', 'cardbox').'</td>';
                } else {
                    $html .= '<td class="cell-c3  cbx-center-align">'.userdate(implode(',', array_keys($lastpractised)),
                                                                        get_string('strftimerecent')).'</td>';
                }
                // Card Status columns.
                $due = 0;
                $notdue = 0;
                require_once('model/cardbox.class.php');
                require_once('model/card_selection_algorithm.php');
                $select = new cardbox_card_selection_algorithm(null, true);
                $cardboxmodel = new cardbox_cardboxmodel($cm->instance, $select);
                $boxcount = $cardboxmodel->cardbox_get_status();
                // New cards.
                $html .= '<td class="cell-c4  cbx-center-align">'.$boxcount[0].'</td>';
                // Mastered cards.
                $html .= '<td class="cell-c5  cbx-center-align">'.$boxcount[6].'</td>';
                // Due  and Not Due cards.
                for ($i = 1; $i <= 5; $i++) {
                    $due += $boxcount[$i]['due'];
                    $notdue += $boxcount[$i]['notdue'];
                }
                $html .= '<td class="cell-c6  cbx-center-align">'.$due.'</td>';
                $html .= '<td class="cell-c7  cbx-center-align">'.$notdue.'</td>';
            } else {
                echo "<span class='notification alert alert-danger alert-block fade in' role='alert' style='display:block'>Something went wrong </span>";
            }
        } else {
            $html .= '<tr>';
            $html .= '<td class="cell-c0  cbx-center-align">'.$sectionname.'</td>';
            $html .= '<td class="cell-c1  cbx-center-align"><a href="'.$CFG->wwwroot.'/mod/cardbox/view.php?id='.$cm->id.'">'.
            $cm->get_formatted_name().'</a></td>';
            if (has_capability('mod/cardbox:practice', $context)) {
                for ($i = 2; $i <= 7; $i++) {
                    $html .= '<td class="cell-c'.$i.'  cbx-center-align">--</td>';
                }
            } else {
                $html .= '<td class="cell-c2  cbx-center-align">--</td>';
            }
            $html .= '</tr>';
        }
    }
}


$html .= '</tbody></table>';
echo $html;
echo $OUTPUT->footer();
