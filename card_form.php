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

class mod_cardbox_card_form extends moodleform {

    /**
     * This function is called by the constructor.
     *
     * @global type $CFG
     * @global type $DB
     * @global type $USER
     * @global type $COURSE
     * @param type $action
     * @param array $preselected This param is saved by the constructor in $this->_customdata.
     */
    function definition($action = null, $preselected = null) {

        global $CFG, $DB, $USER, $COURSE;

        $mform = $this->_form;

        $customdata = $this->_customdata;

        // Pass contextual parameters to the form (via set_data() in controller.php).
        $mform->addElement('hidden', 'id'); // Course module id.
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'course'); // Course id.
        $mform->setType('course', PARAM_INT);
        
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHANUM);
        
        $mform->addElement('hidden', 'from');
        $mform->setType('from', PARAM_ALPHA);

        $mform->addElement('hidden', 'cardid');
        $mform->setType('cardid', PARAM_INT);
        $mform->setDefault('cardid', 0);
        
        $mform->addElement('hidden', 'next');
        $mform->setType('next', PARAM_INT);
        $mform->setDefault('next', 0);

        // Get topics to choose from when creating a new card.
        $topiclist = cardbox_get_topics($customdata['cardboxid'], true);
        $select = $mform->addElement('select', 'topic', get_string('choosetopic', 'cardbox'), $topiclist);
        if (!empty($customdata['topic'])) {
            $select->setSelected($customdata['topic']);
        }

        // Text input field for creating a new topic.
        $mform->addElement('text', 'newtopic', ''); // $mform->addElement('text', 'newtopic', get_string('entertopic', 'cardbox'));
        $mform->setType('newtopic', PARAM_CLEANHTML); // supports ä, ö, ü, ...
        $mform->disabledIf('newtopic', 'topic', 'neq', 0); // You can only enter a new topic name if you choose to.

        // Enter a prompt or question. // XXX Make width / number of columns dynamic
//        $mform->addElement('textarea', 'question', get_string('enterquestion', 'cardbox'), 'wrap="virtual" rows="2" cols="105"');

        /****************** question experiment **********************/
        
//        $questiongroup = array();
//        $questiongroup[] =& $mform->createElement('editor', 'question', get_string('enterquestion', 'cardbox'), 'wrap="virtual" rows="5" cols="150"');
//        
//        $options = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
//                          'accepted_types' => array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'svg'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
//        $questiongroup[] =& $mform->createElement('filemanager', 'cardimage', get_string('image', 'cardbox'), null, $options);
//        
//        
//        $audiooptions = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
//                          'accepted_types' => array('mp3'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
//        $questiongroup[] =& $mform->createElement('filemanager', 'cardsound', get_string('sound', 'cardbox'), null, $audiooptions);
//        
//        
////        $questiongroup[] =& $mform->createElement('filemanager', 'picture', 'Flamingo');
//        $mform->addGroup($questiongroup, 'questiongroup', get_string('enterquestion', 'cardbox'), array(' '), true);
//
//        //$mform->setType('questiongroup', PARAM_CLEANHTML);
//
//        //$mform->addGroupRule('questiongroup', array('value' => array(array(list, of, rule, params, but, fieldname))));
////        $mform->addRule('questiongroup', null, 'required', null, 'client');
//

        /****************** end of question experiment **********************/

        /****************** question **********************/

        $mform->addElement('editor', 'question', get_string('enterquestion', 'cardbox'), 'wrap="virtual" rows="5" cols="150"');
        $mform->setType('question', PARAM_RAW);

        // Enter an image instead or as a supplement.
//        $options = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
//                          'accepted_types' => array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'svg'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
        $options = array();
        $options['accepted_types'] = array('.bmp', '.gif', '.jpeg', '.jpg', '.png', '.svg');
        $options['maxbytes'] = 0;
        $options['maxfiles'] = 1;
        $options['mainfile'] = true;
        $mform->addElement('filemanager', 'cardimage', get_string('image', 'cardbox'), null, $options);

        // Enter an audio file instead or as a supplement.
//        $audiooptions = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
//                          'accepted_types' => array('mp3'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
        $audiooptions = array();
        $audiooptions['accepted_types'] = array('.mp3');
        $audiooptions['maxbytes'] = 0;
        $audiooptions['maxfiles'] = 1;
        $audiooptions['mainfile'] = true;
        $mform->addElement('filemanager', 'cardsound', get_string('sound', 'cardbox'), null, $audiooptions);

        /****************** end of question **********************/

        // Enter 1...n correct answers. // XXX Make width / number of columns dynamic
//        $torepeat = array($mform->createElement('textarea', 'answer', get_string('enteranswer', 'cardbox'), 'wrap="virtual" rows="2" cols="105"'));
        //$torepeat = array($mform->createElement('editor', 'answer', get_string('enteranswer', 'cardbox'), 'wrap="virtual" rows="5" cols="150"'));
        $torepeat = array();
        $solution = $mform->createElement('editor', 'answer', get_string('enteranswer', 'cardbox'), 'wrap="virtual" rows="5" cols="150"');
        //$mform->addRule('answer', null, 'required', null, 'client');
        $torepeat[] = $solution;
        
        $mform->setType('answer', PARAM_RAW);

        if (!empty($customdata['answercount'])) {
            $initialrepeats = $customdata['answercount'];
        } else {
            $initialrepeats = 1;
        }
        $roptions = array();
        $roptions['answer']['helpbutton'] = array('answer_repeat', 'cardbox'); //array('answer_repeat', 'helpbutton', array('answer_repeat', 'answer_repeat', 'cardbox'));
        $repeathiddenname = 'answer_repeat';
        $addfieldsname = 'answer_add_fields';
        $addfieldsno = 1; // How many fields to add at a time / at button click.
        $addstring = get_string('addanswer', 'cardbox');
        $test = $this->repeat_elements($torepeat, $initialrepeats, $roptions, $repeathiddenname, $addfieldsname, $addfieldsno, $addstring);
        
        // Enter an image instead or as a supplement
//        $options = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
//                          'accepted_types' => array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'svg'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
//        $mform->addElement('filemanager', 'answerimage', get_string('answerimage', 'cardbox'), null, $options);
//
//        // Enter an audio file instead or as a supplement
//        $audiooptions = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
//                          'accepted_types' => array('mp3'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL);
//        $mform->addElement('filemanager', 'answersound', get_string('answersound', 'cardbox'), null, $audiooptions);

        
        $context = context_module::instance($customdata['cmid']);
        
        if (has_capability('mod/cardbox:approvecard', $context)) {
            $this->add_action_buttons_for_managers(true);

        } else {
            $this->add_action_buttons(true, get_string('savecard', 'cardbox'));
        }

    }

    /**
     * This function allows managers to save and accept a card in one action.
     *
     * @param type $cancel
     * @param type $submitlabel
     * @param type $submit2label
     */
    function add_action_buttons_for_managers($cancel=true, $submitlabel=null, $submit2label=null) {
        if (is_null($submitlabel)) {
            $submitlabel = get_string('saveandaccept', 'cardbox');
        }

        if (is_null($submit2label)) {
            $submit2label = get_string('savecard', 'cardbox');
        }

        $mform = $this->_form;

        // elements in a row need a group
        $buttonarray = array();

        if ($submit2label !== false) {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton2', $submit2label);
        }

        if ($submitlabel !== false) {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        }

        if ($cancel) {
            $buttonarray[] = &$mform->createElement('cancel');
        }

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->setType('buttonar', PARAM_RAW);
        $mform->closeHeaderBefore('buttonar');
    }

    
    
    /**
     * This function checks whether the user entered text, an image and/or an audio file
     * for a question.
     *
     * @global type $USER
     * @param type $data
     * @param type $files
     * @return type
     */
    function validation($data, $files) {

        global $USER;

        $errors = parent::validation($data, $files);

        $question = $data['question'];
        $questiontext = $question['text'];
        
        $fs = get_file_storage();
        $usercontext = context_user::instance($USER->id);

        $draftitemid = $data['cardimage'];
        $imagefiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'sortorder, id', false);

        $draftitemid2 = $data['cardsound'];
        $audiofiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid2, 'sortorder, id', false);
        
        $answers = $data['answer'];
        $answer = $answers[0];
        $answertext = $answer['text'];

        if ( (empty($questiontext) && empty($imagefiles) && empty($audiofiles)) || empty($answertext) ) {
            $errors['files'] = get_string('required');
        }
        return $errors;
        
        
//        global $USER;
//
//        $errors = parent::validation($data, $files);
//
//        $questiongroup = $data['questiongroup'];
//        
//        $question = $questiongroup['question'];
//        $questiontext = $question['text'];
//        
//        $fs = get_file_storage();
//        $usercontext = context_user::instance($USER->id);
//
//        $draftitemid = $questiongroup['cardimage'];
//        $imagefiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'sortorder, id', false);
//
//        $draftitemid2 = $questiongroup['cardsound'];
//        $audiofiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid2, 'sortorder, id', false);
//
//        if (empty($questiontext) && empty($imagefiles) && empty($audiofiles)) {
//            $errors['files'] = get_string('required');
//        }
//        return $errors;

    }

    // Loads the old file in the filemanager.
//    public function data_preprocessing(&$defaultvalues) {
//        if ($this->current->instance) {
//            $contextid = $this->context->id;
//            $draftitemid = file_get_submitted_draft_itemid('cardimage');
//            file_prepare_draft_area($draftitemid, $contextid, 'mod_cardbox', 'content', 0, array('subdirs' => true));
//            $defaultvalues['cardimage'] = $draftitemid;
////            $this->_form->disabledIf('files', 'update', 'notchecked', 2);
//        }
//    }

}
