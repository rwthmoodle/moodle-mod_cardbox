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
 * @package   mod_flashcards
 * @copyright 2019 RWTH Aachen (see README.md)
 * @authors   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Meta information
$string['cardbox'] = 'Card Box';
$string['modulename'] = 'Card Box';
$string['pluginname'] = 'Card Box';
$string['modulenameplural'] = 'Card Boxes';
$string['cardboxname'] = 'Name of this Card Box';
$string['pluginadministration'] = 'Card Box Administration';

// Tab navigation
$string['addflashcard'] = 'Add flashcard';
$string['practice'] = 'Practice';
$string['review'] = 'Review';

// Subpage titles
$string['titleforaddflashcard'] = 'Add flashcard';
$string['titleforpractice'] = 'Practice';
$string['titleforreview'] = 'Check flashcard';
$string['titleforcardedit'] = 'Edit flashcard';

// Form elements for creating a new card
$string['choosetopic'] = 'Topic';
$string['notopic'] = 'not assigned';
$string['addnewtopic'] = 'create a topic';
$string['entertopic'] = 'create a topic';
$string['enterquestion'] = 'Enter a prompt or question';
$string['image'] = 'Add an image';
$string['enteranswer'] = 'Enter the solution';
$string['addanswer'] = 'Add another solution';
$string['savecard'] = 'Save';

// Success notifications
$string['success:addnewcard'] = 'The flashcard was created and awaits approval.';
$string['success:approve'] = 'The flashcard was approved and is now free to use.';
$string['success:edit'] = 'Die Karte wurde erfolgreich bearbeitet und zum Lernen freigegeben.';
$string['success:reject'] = '.';
$string['success:skip'] = '.';

// Error notifications
$string['error:updateafterreview'] = 'Update failed.';

// Info notifications
$string['info:nocardsavailableforreview'] = 'There are no new cards to review at present.';
$string['info:nocardsavailable'] = 'There are no flashcards in your cardbox at present.';


// Title and form elements for choosing the settings for a new practice session
$string['titleforchoosesettings'] = 'What and how would you like to practice?';
$string['choosecorrectionmode'] = 'Correction mode';
$string['choosecorrectionmode_help'] = 'Would you like to ...?';
$string['selfcorrection'] = 'Self-check';
$string['autocorrection'] = 'Automatic check';
$string['weightopic'] = 'Priority topic';
$string['weightopic_help'] = 'Sie können die Kartenauswahl für diesen Übungsdurchlauf beeinflussen, indem Sie ein Thema auswählen, das verstärkt geübt werden soll. Dies kann in Vorbereitung auf einen Test sinnvoll sein.';
//$string['weightopic_help'] = 'Angeklickte Themen werden bei der Kartenauswahl für diesen Übungsdurchlauf bevorzugt behandelt.';
$string['notopicpreferred'] = 'no preference';
$string['beginpractice'] = 'Start'; // XXX can perhaps be removed.
$string['applysettings'] = 'Applay';
$string['cancel'] = 'Cancel';

// Practice mode: Buttons.
$string['options'] = 'Options';
$string['dontknow'] = "I don't know";
$string['submitanswer'] = 'Check';
$string['markascorrect'] = 'Correct';
$string['markasincorrect'] = 'Incorrect';
$string['override'] = 'Override';
$string['proceed'] = 'Next';

$string['solution'] = 'Solution';
$string['yoursolution'] = 'Your solution';

// Practice mode: Feedback
$string['feedback:correctandcomplete'] = 'Well done.';
$string['feedback:correctbutincomplete'] = 'Your answer is incomplete.';
$string['feedback:incorrectandpossiblyincomplete'] = 'Incorrect.';
$string['sessioncompleted'] = 'Finished! :-)';
$string['titleprogresschart'] = 'Results';
$string['right'] = 'right';
$string['wrong'] = 'wrong';
$string['titleoverviewchart'] = 'Cardbox';
$string['new'] = 'new';
$string['flashcards'] = 'flashcards';
$string['box'] = 'box';

// Review.
$string['approve'] = 'Approve';
$string['reject'] = 'Reject';
$string['edit'] = 'Edit';
$string['skip'] = 'Skip';