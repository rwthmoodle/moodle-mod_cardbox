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

defined('MOODLE_INTERNAL') || die();


require_once("$CFG->libdir/formslib.php"); // moodleform is defined in formslib.php
require_once($CFG->dirroot.'/mod/cardbox/locallib.php');

class mod_cardbox_review_form extends moodleform {
    public function definition($action = null, $preselected = null) {
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

        foreach ($customdata['cardlist'] as $cardid) {

            $cardcontents = $DB->get_records_sql(
                'SELECT mcc.id, mcc.card, mcc.cardside, mcc.contenttype, mcc.content, mcc.area, mcc2.disableautocorrect,
                    (SELECT topicname from {cardbox_topics} where id = mcc2.topic) AS topicname
                    FROM {cardbox_cardcontents} mcc join {cardbox_cards} mcc2 on mcc.card = mcc2.id
                        where mcc.card = :cardid and area in (:areamain, :areasugg) order by topicname',
                [
                    'cardid' => $cardid,
                    'areamain' => CARD_MAIN_INFORMATION,
                    'areasugg' => CARD_ANSWERSUGGESTION_INFORMATION
                ]
            );

            $question = '';
            $answer = '';
            $count = 0;
            $countsuggestedanswers = 0;
            $divadded = false;
            $necessaryanswers = cardbox_get_necessaryanswers($cardid);
            if ($necessaryanswers === "1") {

                $howmanyanswersnecessary = '<span class="badge badge-dark" data-toggle = "tooltip" title = "'.
                                           get_string("oneanswersnecessary_help", "cardbox").'">'.
                                           get_string("oneanswersnecessary", "cardbox").'</span>';

            } else {
                $howmanyanswersnecessary = '<span class="badge badge-dark" data-toggle = "tooltip" title = "'.
                                           get_string("allanswersnecessary_help", "cardbox").'">'.
                                           get_string("allanswersnecessary", "cardbox").'</span>';
            }

            foreach ($cardcontents as $cardcontent) {
                $cardcontent->content = format_text($cardcontent->content);

                if ($cardcontent->topicname === null) {
                    $topicname = get_string('notopic', 'cardbox');;
                } else {
                    $topicname = $cardcontent->topicname;
                }

                // Question Side.
                if ($cardcontent->cardside == CARDBOX_CARDSIDE_QUESTION) {
                    switch($cardcontent->contenttype){
                        case CARDBOX_CONTENTTYPE_IMAGE:
                            $downloadurl = cardbox_get_download_url($customdata['context'], $cardcontent->id, $cardcontent->content);
                            $question .= '<div class="cardbox-image"><img src="'.$downloadurl.'" alt="" class="img-fluid  d-block"></div>';
                        break;
                        case CARDBOX_CONTENTTYPE_TEXT:
                            $question .= '<div class="cardbox-card-text text-center"><div class="text_to_html" style="text-align: center;">'.
                            $cardcontent->content.'</div></div>';
                        break;
                        case CARDBOX_CONTENTTYPE_AUDIO:
                            $downloadurl = cardbox_get_download_url($customdata['context'], $cardcontent->id, $cardcontent->content);
                            $question .= '<audio controls="">
                                              <source src="'.$downloadurl.'" type="audio/mpeg">
                                          </audio>';
                        break;
                        default:
                            echo "<span class='notification alert alert-danger alert-block fade in' role='alert' style='display:block'>Something went wrong </span>";
                    }
                } else {
                    $countapprovedanswers = $DB->count_records('cardbox_cardcontents',
                        ['cardside' => $cardcontent->cardside, 'card' => $cardid, 'area' => CARD_MAIN_INFORMATION]);
                    $countsuggestedanswers = $countapprovedanswers + $DB->count_records('cardbox_cardcontents',
                        ['cardside' => CARDBOX_CARDSIDE_ANSWER, 'card' => $cardid, 'area' => CARD_ANSWERSUGGESTION_INFORMATION]);

                    if ($countsuggestedanswers > 1) {
                        $count++;
                        if (!$divadded) {
                            $answer .= '<div class="cardbox-card-right-side-multi">';
                            $divadded = true;
                        }
                        $height = (100 - ($countsuggestedanswers - 1)) / $countsuggestedanswers;

                        $answerapproved = $cardcontent->area != CARD_ANSWERSUGGESTION_INFORMATION;
                        $suggestedanswers = $DB->get_records('cardbox_cardcontents', ['card' => $cardid,
                            'cardside' => CARDBOX_CARDSIDE_ANSWER, 'area' => CARD_ANSWERSUGGESTION_INFORMATION], '', 'id, content');
                        $class = 'cardbox-cardside-multi';
                        if (!$answerapproved) {
                            $class .= ' suggestion';
                        }
                        $answer .= '<div class="'.$class.'" >
                                    <div class="cardbox-card-text "><div class="text_to_html">'.$cardcontent->content.
                                    '</div></div></div>';
                        if ($count == $countsuggestedanswers) {
                            $answer .= '</div>';
                        }

                    } else {
                        $answer .= '<div class="cardbox-cardside"><div class="cardbox-card-text "><div class="text_to_html">'
                            .'<div style="height:100%">'.$cardcontent->content.'</div></div></div></div>';
                    }
                }
            }
            $mform->addElement('html', '<div id="cardbox-card-in-review" data-cardid="'.$cardid.'" class="row reviewcontent" style="margin-bottom: 0px;">');

            if ($cardcontent->disableautocorrect == DISABLE_AUTOCORRECT) {
                $acimgurl = '<span class="badge badge-secondary" data-toggle = "tooltip" title = "'.get_string("autocorrecticon_help", "cardbox").'">'.
                            get_string("autocorrecticon", "cardbox"). '</span>';

            } else {
                $acimgurl = '';
            }
            if ($countsuggestedanswers > 1) {
                $mform->addElement('html', '<div class="col-xl-4" style="margin-left: 3%; margin-bottom: 10px">'. strtoupper(get_string('choosetopic', 'cardbox').': '.
                                   $topicname).'</div><div class="col-xl-4" style="padding-left: 0.4%;"><div class="review-icon-grid-div">'.$howmanyanswersnecessary.
                                   $acimgurl. '</div></div><div class="col-xl-2"></div><div class="col-xl-4" style="padding:0px;"><div class="cardbox-column" style="height: 100%;">
                                   <div class="cardbox-card-left-side"><div class="cardbox-cardside"><div style="height:100%">'.$question.'</div></div></div></div></div>');
            } else {
                $mform->addElement('html', '<div class="col-xl-4" style="margin-left: 3%; margin-bottom: 10px">'. strtoupper(get_string('choosetopic', 'cardbox').': '.
                $topicname).'</div><div class="col-xl-4" style="padding-left: 0.4%;"><div class="review-icon-grid-div">'.$acimgurl.'
                </div></div><div class="col-xl-2"></div><div class="col-xl-4" style="padding:0px;"><div class="cardbox-column" style="height: 100%;">
                <div class="cardbox-card-left-side"><div class="cardbox-cardside"><div style="height:100%">'.$question.'</div></div></div></div></div>');
            }

            if ($countsuggestedanswers > 1) {
                $mform->addElement('html', '<div class="col-xl-4" style="padding:0px;"><div class="cardbox-column" style="height: 100%"><div style="height: 100%">'
                .$answer.'</div></div></div>');
            } else {
                $mform->addElement('html', '<div class="col-xl-4" style="padding:0px;"><div class="cardbox-column" style="height: 100%;"><div class="cardbox-card-right-side"><div>'
                .$answer.'</div></div></div></div>');
            }

            $mform->addElement('html', '<div class="col-xs-2"><div id="review-button-wrapper">
                <div class="btn-group-vertical" role="group" aria-label="review-actions">
                <button id="cardbox-edit-'.$cardid.'" type="button" class="btn btn-primary cardbox-review-button" title="Edit"><i class="icon fa fa-pencil fa-fw"></i></button>
                </div></div></div>');
            $cardapproved = cardbox_card_approved($cardid);
            if ($cardapproved) {
                $mform->addElement('html', '<div class="col-lg-1 checkbox-card">');
                while (true) {
                    if ($countapprovedanswers < 1) {
                        foreach ($suggestedanswers as $suggestedanswer) {
                                $mform->addElement('html', '<div style ="height:'.$height.'%">');
                                $mform->addElement('checkbox', 'chck'.$cardid.'-'.strip_tags(str_replace(" ", "" , $suggestedanswer->content))); // Checkbox for selection
                                $mform->addElement('html', '</div>');
                        }
                        break;
                    } else {
                        $mform->addElement('html', '<div style ="height:'.($height - 1).'%"></div>');
                        $countapprovedanswers--;
                    }
                }

            } else {
                $mform->addElement('html', '<div class="col-lg-1 checkbox-card">');
                $mform->addElement('checkbox', 'chck'.$cardid);
            }
            $mform->addElement('html', '</div>');
            $mform->addElement('html', '</div>'); // ending cardbox-card-in-review and row reviewcontent

            $qcontext = $DB->get_field('cardbox_cardcontents', 'content', ['card' => $cardid, 'cardside' => CARDBOX_CARDSIDE_QUESTION,
                'contenttype' => CARDBOX_CONTENTTYPE_TEXT, 'area' => CARD_CONTEXT_INFORMATION]);
            $qcontext = format_text($qcontext);
            $acontext = $DB->get_field('cardbox_cardcontents', 'content', ['card' => $cardid, 'cardside' => CARDBOX_CARDSIDE_ANSWER,
                'contenttype' => CARDBOX_CONTENTTYPE_TEXT, 'area' => CARD_CONTEXT_INFORMATION]);
            $acontext = format_text($acontext);
            $mform->addElement('html', '<div id="cardbox-card-in-review" class="row reviewcontent" style="display: -webkit-box; margin-top: 10px">
            <div class="col-xl-4" style="margin-left: 10%; padding-right: 0px; padding-left: 1%;"><div class="cardbox-column" >'.$qcontext.
            '</div></div><div class="col-xl-4" style="padding-left:0.5%;"><div class="cardbox-column" ><div>'.$acontext.'</div></div></div></div>');

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
