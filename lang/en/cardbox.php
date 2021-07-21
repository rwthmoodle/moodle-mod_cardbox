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
$string['setting_autocorrection'] = 'Allow autocorrection';
$string['setting_autocorrection_help'] = 'Autocorrection only works for normal text. If students may be expected to give formulae answers, you should deactivate autocorrection.';
$string['setting_autocorrection_label'] = '<font color="red">only suitable for text</font>'; // 'Activate with care.';
$string['messageprovider:changenotification'] = 'Notify when a flashcard was edited';
$string['changenotification:subject'] = 'Change notification';
$string['changenotification:message'] = 'A flashcard was edited in your cardbox. Here is the card in its current form.';

// Reminders
$string['send_practice_reminders'] = 'Send e-mail reminders to the course participants';
$string['messageprovider:memo'] = 'Reminders to practice with cardbox';
$string['remindersubject'] = 'Practice reminder';
$string['remindergreeting'] = 'Hello {$a}, ';
$string['remindermessagebody'] = 'please remember to study with your cardbox on a regular basis.';
$string['reminderfooting'] = 'This reminder was sent automatically by your cardbox "{$a->cardboxname}" in the course "{$a->coursename}".';

// Tab navigation
$string['addflashcard'] = 'Add a card';
$string['practice'] = 'Practice';
$string['statistics'] = 'Progress';
$string['overview'] = 'Overview';
$string['review'] = 'Review';

// Subpage titles
$string['titleforaddflashcard'] = 'New card';
$string['titleforpractice'] = 'Practice';
$string['titleforreview'] = 'Check card';
$string['titleforcardedit'] = 'Edit card';
$string['intro:overview'] = 'This overview displays all cards that have been approved.';

// Form elements for creating a new card
$string['choosetopic'] = 'Topic';
$string['reviewtopic'] = 'Topic: ';
$string['notopic'] = 'not assigned';
$string['addnewtopic'] = 'create a topic';
$string['entertopic'] = 'create a topic';
$string['enterquestion'] = 'Question or prompt';
$string['image'] = 'Question image';
$string['sound'] = 'Question sound';
$string['answerimage'] = 'Answer image';
$string['answersound'] = 'Answer sound';
$string['enteranswer'] = 'Solution';
$string['answer_repeat'] = 'Add another solution';
$string['answer_repeat_help'] = "If the solution consists of several parts, please enter them individually by clicking 'Add another solution'. This enables the program to check answers for completeness. If there are valid alternatives for the solution, please enter all of them in the first input field.";
$string['addanswer'] = 'Add another solution';
$string['savecard'] = 'Save';
$string['saveandaccept'] = 'Save and accept without review';

// Success notifications
$string['success:addnewcard'] = 'The card was created and awaits approval.';
$string['success:addandapprovenewcard'] = 'The card was created and approved for practice.';
$string['success:approve'] = 'The card was approved and is now free to use.';
$string['success:edit'] = 'The card was edited.';
$string['success:reject'] = 'The card was deleted.';
//$string['success:skip'] = '.';

// Error notifications
$string['error:updateafterreview'] = 'Update failed.';
$string['error:createcard'] = 'The card was not created, because it is missing a question and/or answer.';

// Info notifications
$string['info:statisticspage'] = 'This page tells you how many cards there are in your cardbox (due and not-due) and how well you did in your previous practice sessions.';
$string['info:nocardsavailableforreview'] = 'There are no new cards to review at present.';
$string['info:waslastcardforreview'] = 'This was the last card to be reviewed.';
$string['info:nocardsavailableforoverview'] = 'There are no cards in this cardbox.';
$string['info:nocardsavailable'] = 'There are no cards in your cardbox at present.';
$string['help:nocardsavailable'] = 'Empty Cardbox';
$string['help:nocardsavailable_help'] = 'Possible reasons:<ul><li>No cards have been created.</li><li>The teacher has yet to check and accept a card.</li></ul>';
$string['info:nocardsavailableforpractice'] = 'There are no cards ready for practice.';
$string['help:nocardsavailableforpractice'] = 'No cards';
$string['help:nocardsavailableforpractice_help'] = 'You have correctly answered every card that is currently available 5 times over a period of at least two months. These cards are regarded as mastered and no longer repeated.</ul>';
$string['info:nocardsdueforpractice'] = 'None of your cards are due for repetition yet.';
$string['help:nocardsdueforpractice'] = 'No cards due';
$string['help:nocardsdueforpractice_help'] = 'New cards are due immediately. For any other card the deck decides:<ol><li>deck: daily</li><li>deck: after 3 days</li><li>deck: after 7 days</li><li>deck: after 16 days</li><li>deck: after 34 days</li></ol>';
$string['help:whenarecardsdue'] = 'When are cards due';
$string['help:whenarecardsdue_help'] = 'New cards are immediately due for practice. For any other card the deck decides:<ol><li>deck: daily</li><li>deck: after 3 days</li><li>deck: after 7 days</li><li>deck: after 16 days</li><li>deck: after 34 days</li></ol>';
$string['help:practiceanyway'] = 'If you would like to practice, nevertheless, please click on <em>Options</em> and select <em>Practice cards before they are due</em>.';

// Title and form elements for choosing the settings for a new practice session
$string['titleforchoosesettings'] = 'Practice options';
$string['choosecorrectionmode'] = 'Practice mode';
$string['choosecorrectionmode_help'] = 'You can type in your answer and have it checked. If you prefer oral answers or handwriting, please select "Check yourself".';
$string['selfcorrection'] = 'Check yourself';
$string['autocorrection'] = 'Automatic check';
$string['weightopic'] = 'Priority topic';
$string['weightopic_help'] = 'Cards belonging to the priority topic will be favoured in the selection of cards for practice.';
$string['notopicpreferred'] = 'no preference';
$string['practiceall'] = 'Practice cards before they are due';
$string['practiceall_help'] = 'These cards do not proceed to the next deck if answered correctly. Thus, you can practice as often as you wish without risking that cards leave the cardbox forever after only a few days.';
$string['onlyonetopic'] = 'Topic';
$string['onlyonetopic_help'] = 'If you select a topic you will practice only cards from that topic. You will not be asked about cards from other topics.';
$string['beginpractice'] = 'Start practice';
$string['applysettings'] = 'Apply';
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
$string['yoursolution'] = 'Your answer';

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
$string['flashcardsdue'] = 'due';
$string['flashcardsnotdue'] = 'not due yet';
$string['box'] = 'box';

$string['titleperformancechart'] = 'Past practice sessions';
$string['performance'] = '% correct';

// Review.
$string['approve'] = 'Approve';
$string['reject'] = 'Reject';
$string['edit'] = 'Edit';
$string['skip'] = 'Skip';

// Statistics
$string['strftimedate'] = '%d. %B %Y';
$string['strftimedatetime'] = '%d. %b %Y, %H:%M';
$string['barchartxaxislabel'] = 'Deck';
$string['barchartyaxislabel'] = 'Card count';
$string['linegraphxaxislabel'] = 'Date';
$string['linegraphyaxislabel'] = '% known';
$string['lastpractise'] = 'last practised';
$string['nopractise'] = 'not practised yet';
$string['newcard'] = 'new cards';
$string['knowncard'] = 'mastered cards';

$string['yes'] = 'Yes';
$string['cancel'] = 'Cancel';
$string['deletecard'] = 'Delete card?';
$string['deletecardinfo'] = 'The card and the progress of this card will be deleted for all users.';