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
 * This file is used when adding/editing a flashcard to a cardbox.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die(); //  It must be included from a Moodle page.

require_once("$CFG->libdir/formslib.php"); // moodleform is defined in formslib.php
require_once('locallib.php');

class mod_cardbox_review_form extends moodleform {
    function definition($action = null, $preselected = null) {
        global $CFG, $DB, $USER, $COURSE;
        $mform = $this->_form;
        $customdata = $this->_customdata;

        // Pass contextual parameters to the form (via set_data() in controller.php).
        $mform->addElement('hidden', 'id'); // Course module id.
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $customdata['cmid']);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHANUM);
        $mform->setDefault('action', 'review');

        /*$mform->addElement('hidden', 'course'); // Course id.
        $mform->setType('course', PARAM_INT);
        $mform->setDefault('id', $customdata['cmid']);*/
        $mform->addElement('html', '<div id="cardbox-review">');
        $mform->addElement('html', '<div id="container-fluid cardbox-studyview">');
        $topicname = '';
        $cardid = '';
        
        foreach ($customdata['cardlist'] as $key => $value) {
            
            $cardcontents = $DB->get_records_sql(
                'SELECT mcc.id,mcc.card,mcc.cardside,mcc.contenttype,mcc.content,
                    (SELECT topicname from {cardbox_topics} where id =mcc2.topic) AS topicname
                    FROM {cardbox_cardcontents} mcc join {cardbox_cards} mcc2 on mcc.card=mcc2.id
                        where mcc.card = :cardid and area in (0,3) order by 6',
                        ['cardid' => $value]);
            
            $question = '';
            $answer = '';
            $qcontext = null;
            $acontext = null;
            $count = 0;
            
            foreach ($cardcontents as $cardcontent) {
                //Question Side
                if ($cardcontent->cardside == "0" ){
                    $qcontext = $DB->get_field('cardbox_cardcontents', 'content', array('card' => $value, 'cardside' => $cardcontent->cardside, 'contenttype' => 2, 'area' => 1), IGNORE_MISSING);
                    switch($cardcontent->contenttype){
                        case 1:
                            $downloadurl = cardbox_get_download_url($customdata['context'], $cardcontent->id, $cardcontent->content);
                            $question .= '<div class="cardbox-image"><img src="'.$downloadurl.'" alt="" class="img-fluid  d-block"></div>';
                        break;
                        case 2:
                            $question .= '<div class="cardbox-card-text text-center"><div class="text_to_html" style="text-align: center;">'.
                            $cardcontent->content.'</div></div>';
                        break;
                        case 3:
                            $downloadurl = cardbox_get_download_url($customdata['context'], $cardcontent->id, $cardcontent->content);
                            $question .= '<audio controls="">
                            <source src="'.$downloadurl.'" type="audio/mpeg">
                        </audio>';
                        break;
                        default:
                            print_r('ERROR!');
                    }
                } else {
                    $acontext = $DB->get_field('cardbox_cardcontents', 'content', array('card' => $value, 'cardside' => $cardcontent->cardside, 'contenttype' => 2, 'area' => 1), IGNORE_MISSING);
                    $multianswers = $DB->count_records('cardbox_cardcontents',
                        ['cardside' => $cardcontent->cardside, 'card' => $value, 'area' => 0]);
                    $multianswers += $DB->count_records('cardbox_cardcontents',
                        ['cardside' => $cardcontent->cardside, 'card' => $value, 'area' => 3]);


                    if ($multianswers > 1) {
                        $count++;
                        if($cardid != $value){
                            $answer .= '<div class="cardbox-card-right-side-multi"><div style="height: 100%">';
                            $cardid = $value;
                        }
                        $eachheight = (100-$multianswers)/$multianswers;

                        $suggestedanswers = $DB->get_records('cardbox_cardcontents', array('card' => $value, 'cardside' => $cardcontent->cardside, 'area' => 3), '', 'content');
                        $solutionsapproved = true;
                        foreach ($suggestedanswers as $suggestedanswer) {
                            if ($count == $multianswers){
                                if ($suggestedanswer->content === $cardcontent->content) {
                                    $answer .= '<div class="cardbox-cardside-multi" style ="height:'.($eachheight-1).'%; border-color: mediumvioletred; border-style: solid;">
                                    <div class="cardbox-card-text "><div class="text_to_html">'.$cardcontent->content.
                                    '</div></div></div></div>';
                                    $solutionsapproved = false;
                                }
                            } else {
                                if ($suggestedanswer->content === $cardcontent->content) {
                                    $answer .= '<div class="cardbox-cardside-multi" style ="height:'.$eachheight.'%; border-color: mediumvioletred; border-style: solid; margin-bottom: 1%">
                                    <div class="cardbox-card-text "><div class="text_to_html">'.$cardcontent->content.
                                    '</div></div></div>';
                                    $solutionsapproved = false;
                                }
                            }
                        }
                        if ($solutionsapproved){
                            if ($count == $multianswers){
                                $answer .= '<div class="cardbox-cardside-multi" style ="height:'.($eachheight-1).'%">
                                <div class="cardbox-card-text "><div class="text_to_html">'.$cardcontent->content.
                                '</div></div></div></div>';
                            } else {
                                $answer .= '<div class="cardbox-cardside-multi" style ="height:'.$eachheight.'%; margin-bottom: 1%">
                                <div class="cardbox-card-text "><div class="text_to_html">'.$cardcontent->content.
                                '</div></div></div>';
                            }
                        }

                    } else {
                        $answer .= '<div class="cardbox-cardside"><div class="cardbox-card-text "><div class="text_to_html"><div style="height:100%">'
                        .$cardcontent->content.'</div></div></div></div>';
                    }
                }
            }
            if (!is_null($cardcontents[$cardcontent->id]->topicname)) {
                if ($topicname != $cardcontents[$cardcontent->id]->topicname) {
                    $topicname = $cardcontents[$cardcontent->id]->topicname;
                    //$mform->addElement('html', '<div class="alert alert-dark" role="alert"><h5>Topic:<i> '.$topicname.'</i><h5></div>');
                }
            } else {
                if ($topicname != get_string('notopic', 'cardbox')) {
                    //$mform->addElement('html', '<div class="alert alert-dark" role="alert"><h5>Topic:<i> Not assigned </i><h5></div>');
                    $topicname = get_string('notopic', 'cardbox');
                }
            }
            $mform->addElement('html', '<div id="cardbox-card-in-review" data-cardid="'.$value.'" class="row reviewcontent">');

            $mform->addElement('html', '<div class="topic-review">'. strtoupper(get_string('choosetopic', 'cardbox').': '.
                                        $topicname).'</div><div class="col-xl-4" style="padding:0px;"><div class="cardbox-column" style="height: 100%;"><div class="cardbox-card-left-side">
                                        <div class="cardbox-cardside"><div style="height:100%">'.$question.'</div>
                                        </div></div><div style="margin:15px; margin-top: 0px"><p>'.$qcontext.
                                        '</p></div></div></div>');
            if ($multianswers > 1) {
                $mform->addElement('html', '<div class="col-xl-4" style="padding:0px;"><div class="cardbox-column" style="height: 100%"><div style="height: 100%">'
                .$answer.'</div></div><div style="margin:15px; margin-left: 0.2em; margin-top: 0px">'.$acontext.'</div></div></div>');
            } else {
                $mform->addElement('html', '<div class="col-xl-4" style="padding:0px;"><div class="cardbox-column" style="height: 100%;"><div class="cardbox-card-right-side"><div>'
                .$answer.'</div></div><div style="margin:15px; margin-left: 0.2em;">'.$acontext.'</div></div></div>');
            }
            $mform->addElement('html', '<div class="col-xs-2"><div id="review-button-wrapper">
            <div class="btn-group-vertical" role="group" aria-label="review-actions">
            <button id="cardbox-edit-'.$value.'" type="button" class="btn btn-primary cardbox-review-button" title="Edit"><i class="icon fa fa-pencil fa-fw"></i></button>
            </div></div></div>');
            $mform->addElement('html', '<div class="col-lg-1 checkbox-card">');
            $mform->addElement('checkbox', 'chck'.$value); // Checkbox for selection
            $mform->addElement('html', '</div>');
            $mform->addElement('html', '</div>'); // ending cardbox-card-in-review and row reviewcontent
        }
        $mform->addElement('html', '<div id= "review-div" class="cardbox-card-in-review sticky-review-arr">');
        $reviewbtngrp = array();
        $reviewbtngrp[] =& $mform->createElement('submit', 'approvebtn', get_string('approve', 'cardbox'));
        $reviewbtngrp[] =& $mform->createElement('submit', 'rejectbtn', get_string('reject', 'cardbox'));
        $mform->addGroup($reviewbtngrp, 'reviewbtnarr', '', array(''), false);
        $mform->setType('reviewbtnarr', PARAM_RAW);
        $mform->closeHeaderBefore('reviewbtnarr');
        $mform->addElement('html', '</div></div></div>');
    }
}