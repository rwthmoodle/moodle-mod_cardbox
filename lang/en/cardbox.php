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
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Meta information
$string['cardbox'] = 'Card Box';
$string['modulename'] = 'Card Box';
$string['modulename_help'] = '<p>This activity allows you to create flashcards for vocabulary, technical terms, formulae, etc. that you want to remember. You can study with the cards as you would do with a card box.</p><p>Cards can be created by every participant, but are only used for practice if a teacher has accepted them.</p>';
$string['pluginname'] = 'Card Box';
$string['modulenameplural'] = 'Card Boxes';
$string['cardboxname'] = 'Name of this Card Box';
$string['pluginadministration'] = 'Flashcards Administration';
$string['setting_autocorrection'] = 'Activate auto correction';
$string['setting_autocorrection_help'] = 'Auto correction does not work for latex content. If you plan to have formulae on some cards, you should deavtivate it.';
$string['setting_autocorrection_label'] = 'Handle with care.';

// Tab navigation
$string['addflashcard'] = 'Add a card';
$string['practice'] = 'Practice';
$string['statistics'] = 'Progress';
$string['review'] = 'Review';

// Subpage titles
$string['titleforaddflashcard'] = 'New card';
$string['titleforpractice'] = 'Practice';
$string['titleforreview'] = 'Check card';
$string['titleforcardedit'] = 'Edit card';

// Form elements for creating a new card
$string['choosetopic'] = 'Topic';
$string['reviewtopic'] = 'Topic: ';
$string['notopic'] = 'not assigned';
$string['addnewtopic'] = 'create a topic';
$string['entertopic'] = 'create a topic';
$string['enterquestion'] = 'Question or prompt';
$string['image'] = 'Question image';
$string['enteranswer'] = 'Solution';
$string['addanswer'] = 'Add another solution';
$string['savecard'] = 'Save';

// Success notifications
$string['success:addnewcard'] = 'The card was created and awaits approval.';
$string['success:approve'] = 'The card was approved and is now free to use.';
$string['success:edit'] = 'Die Karte wurde erfolgreich bearbeitet und zum Lernen freigegeben.';
$string['success:reject'] = '.';
//$string['success:skip'] = '.';

// Error notifications
$string['error:updateafterreview'] = 'Update failed.';

// Info notifications
$string['info:nocardsavailableforreview'] = 'There are no new cards to review at present.';
$string['info:waslastcardforreview'] = 'This was the last card to be reviewed.';
$string['info:nocardsavailable'] = 'There are no cards in your cardbox at present.';
$string['info:nocardsavailableforpractice'] = 'There are no cards ready for practice.';
$string['help:nocardsavailableforpractice'] = 'No cards';
$string['help:nocardsavailableforpractice_help'] = 'Possible reasons:<ul><li>Individual cards cannot be practiced more often than once every 24 hours.</li><li>Once a card has been answered correctly for the 5th time, it is considered "mastered" and no longer repeated.</li></ul>';

// Title and form elements for choosing the settings for a new practice session
$string['titleforchoosesettings'] = 'Practice options';
$string['choosecorrectionmode'] = 'Practice mode';
$string['choosecorrectionmode_help'] = 'You can type in your answer and have it checked. If you prefer oral answers or handwriting, please select "Check yourself".';
$string['selfcorrection'] = 'Check yourself';
$string['autocorrection'] = 'Automatic check';
$string['weightopic'] = 'Priority topic';
$string['weightopic_help'] = 'Cards belonging to the priority topic will be favoured in the selection of cards for practice. This does not mean, however, that only these cards or all of these cards will be selected.';
$string['notopicpreferred'] = 'no preference';
$string['beginpractice'] = 'Start practice';
$string['applysettings'] = 'Applay';
$string['cancel'] = 'Cancel';

// Practice mode: Buttons.
$string['options'] = 'Options';
$string['dontknow'] = "I don't know";
$string['checkanswer'] = 'Check';
$string['submitanswer'] = 'Answer';
$string['markascorrect'] = 'Correct';
$string['markasincorrect'] = 'Incorrect';
$string['override'] = 'Override';
$string['override_iscorrect'] = 'No, I was right!';
$string['override_isincorrect'] = 'No, I was wrong.';
$string['proceed'] = 'Next';

$string['solution'] = 'Solution';
$string['yoursolution'] = 'Your solution';

// Practice mode: Feedback
$string['feedback:correctandcomplete'] = 'Well done.';
$string['feedback:incomplete'] = 'Answers missing.';
$string['feedback:correctbutincomplete'] = 'There are {$a} answers missing.';
$string['feedback:incorrectandpossiblyincomplete'] = 'Incorrect.';
$string['feedback:notknown'] = 'No answer given';

$string['sessioncompleted'] = 'Finished! :-)';
$string['titleprogresschart'] = 'Results';
$string['right'] = 'right';
$string['wrong'] = 'wrong';
$string['titleoverviewchart'] = 'Cardbox';
$string['new'] = 'new';
$string['known'] = 'mastered';
$string['flashcards'] = 'cards';
$string['box'] = 'box';

$string['titleperformancechart'] = 'Past practice sessions';
$string['performance'] = '% correct';

// Review.
$string['approve'] = 'Approve';
$string['reject'] = 'Reject';
$string['edit'] = 'Edit';
$string['skip'] = 'Skip';

$string['strftimedate'] = '%d. %B %Y';
$string['strftimedatetime'] = '%d. %b %Y, %H:%M';
$string['barchartxaxislabel'] = 'Deck';
$string['barchartyaxislabel'] = 'Card count';
$string['linegraphxaxislabel'] = 'Date';
$string['linegraphyaxislabel'] = '% known';