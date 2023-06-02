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

defined('MOODLE_INTERNAL') || die;

// Meta information
$string['cardbox'] = 'Card Box';
$string['activityname'] = 'Cardbox activity';
$string['modulename'] = 'Card Box';
$string['modulename_help'] = '<p>This activity allows you to create flashcards for vocabulary, technical terms, formulae, etc. that you want to remember. You can study with the cards as you would do with a card box.</p><p>Cards can be created by every participant, but are only used for practice if a teacher has accepted them.</p>';
$string['pluginname'] = 'Card Box';
$string['modulenameplural'] = 'Card Boxes';
$string['cardboxname'] = 'Name of this Card Box';
$string['pluginadministration'] = 'Flashcards Administration';
$string['setting_autocorrection'] = 'Allow autocorrection';
$string['setting_autocorrection_help'] = 'Autocorrection only works for normal text. If students may be expected to give formulae answers, you should deactivate autocorrection.';
$string['setting_autocorrection_label'] = '<font color="red">only suitable for text</font>'; // 'Activate with care.';
$string['setting_enablenotifications'] = 'Allow notifications';
$string['setting_enablenotifications_help'] = 'Students receive notifications when cards have been edited or it is time to practice again.';
$string['setting_enablenotifications_label'] = 'Enable sending notifications to students';
$string['necessaryanswers_activity'] = 'Default settings for "How many answers are necessary?"';
$string['necessaryanswers_activity_help'] = 'Set the default value for "How many answers are necessary?" in the card creation form.';
$string['necessaryanswers_activity_locked'] = 'Allow to change the number of necessary answers afterwards?';
$string['necessaryanswers_activity_locked_help'] = 'If "Yes" is selected, then it is possible to change the number of required responses when creating or editing a card.';
$string['casesensitive'] = 'Case sensitivity';
$string['casesensitive_help'] = 'Specifies whether, when practising with automatic control, entries that only differ from the correct answer in terms of upper/lower case are also counted as correct.';
$string['numberofcardssetting'] = 'Number of cards to practice';
$string['numberofcardssetting_help'] = 'Specifies how many cards students should learn per practice session. If "Students decide" is selected, they have free choice.';
$string['studentschoose'] = 'Students choose';
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
$string['massimport'] = 'Import cards';
$string['edittopic'] = 'Manage topics';

// Subpage titles
$string['titleforaddflashcard'] = 'New card';
$string['titleforpractice'] = 'Practice';
$string['titleforreview'] = 'Check card';
$string['titleforcardedit'] = 'Edit card';
$string['intro:overview'] = 'This overview displays all cards that have been approved.';

// Form elements for creating a new card
$string['choosetopic'] = 'Topic';
$string['reviewtopic'] = 'TOPIC: ';
$string['notopic'] = 'not assigned';
$string['addnewtopic'] = 'create a topic';
$string['entertopic'] = 'create a topic';
$string['enterquestion'] = 'Question or prompt';
$string['entercontextquestion'] = 'Additional information for this question';
$string['addcontext'] = 'Show context';
$string['removecontext'] = 'Hide context';
$string['entercontextanswer'] = 'Additional information for the answer';
$string['necessaryanswers_card'] = 'How many answers are necessary?';
$string['necessaryanswers_all'] = 'all';
$string['necessaryanswers_one'] = 'one';
$string['addimage'] = 'Show image options';
$string['removeimage'] = 'Hide image options';
$string['image'] = 'Question image';
$string['imagedescription'] = 'Describe this image for someone who cannot see it (recommended)';
$string['imgdescriptionnecessary_label'] = 'This image is decorative only';
$string['addsound'] = 'Show sound options';
$string['removesound'] = 'Hide sound options';
$string['sound'] = 'Question sound';
$string['answerimage'] = 'Answer image';
$string['answersound'] = 'Answer sound';
$string['enteranswer'] = 'Solution';
$string['answer_repeat'] = 'Add another solution';
$string['autocorrectlocked'] = 'Disable Automatic Check';
$string['autocorrecticon'] = 'Self Check only';
$string['autocorrecticon_help'] = 'Answer cannot be typed in while practising in Automatic Check mode. In the Automatic check mode, the learning card will then still be displayed, but only as a self-check.';
$string['autocorrectlocked_help'] = 'Activate this checkbox if the answer of the learning card is not to be typed in while practicing in "Automcatic check" mode. In the "Automatic check" mode, the learning card will then still be displayed, but only as a self-check. This option is especially useful for learning cards whose answers are not suitable for manual input (e.g. definitions), but should still be practiced together with other learning cards whose answers are entered manually.';
$string['answer_repeat_help'] = 'If you have multiple solutions, please use a separate solution field for each answer.<br>
                                Another solution field can be added by the button "Add another solution".<br>
                                To set whether students need to know all answers or only one (in case of alternative answers) please use the dropdown below.';

$string['addanswer'] = 'Add another solution';
$string['autocorrectlocked'] = 'Disable Automatic Check';
$string['savecard'] = 'Save';
$string['saveandaccept'] = 'Save and accept';

// Success notifications
$string['success:addnewcard'] = 'The card was created and awaits approval.';
$string['success:addandapprovenewcard'] = 'The card was created and approved for practice.';
$string['success:approve'] = 'The card was approved and is now free to use.';
$string['success:edit'] = 'The card was edited.';
$string['success:reject'] = 'The card was deleted.';

// Error notifications
$string['error:updateafterreview'] = 'Update failed.';
$string['error:createcard'] = 'The card was not created, because it is either missing a question and/or answer or if you uploaded a picture the imagedescription might be missing.';


// Import cards
$string['examplesinglecsv'] = 'Example text file for cards having single answers';
$string['examplesinglecsv_help'] = 'Example text file for cards having single answers';
$string['examplemulticsv'] = 'Example text file for cards having multiple answers';
$string['examplemulticsv_help'] = 'Example text file for cards having multiple answers';
$string['cancelimport'] = 'Import was cancelled';
$string['importpreview'] = 'Import cards preview';
$string['importsuccess'] = '{$a} cards imported successfully';
$string['allowedcolumns'] = '<br><p>Allowed column names are:</p>';
$string['ques'] = 'Column name for question';
$string['ans'] = 'Column name for answer';
$string['qcontext'] = 'Column name for question context';
$string['acontext'] = 'Column name for answer context';
$string['topic'] = 'Column name for topic';
$string['acdisable'] = 'Column name to disable Automatic Check for a card. Yes = 1; No = 0.';

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
$string['help:nocardsavailableforpractice_help'] = 'You have correctly answered every card that is currently available 5 times over a period of at least two months. These cards are regarded as mastered and no longer repeated.';
$string['info:nocardsdueforpractice'] = 'None of your cards are due for repetition yet.';
$string['info:enrolledstudentsthreshold_manager'] = 'There must be at least {$a} students enrolled in this course for weekly practice statistics to be displayed.';
$string['info:enrolledstudentsthreshold_student'] = 'Average progress among students is only displayed if there are at least {$a} students enrolled in the course.';
$string['help:nocardsdueforpractice'] = 'No cards due';
$string['help:nocardsdueforpractice_help'] = 'New cards are due immediately. For any other card the deck decides:<ol><li>deck: daily</li><li>deck: after 3 days</li><li>deck: after 7 days</li><li>deck: after 16 days</li><li>deck: after 34 days</li></ol>';
$string['help:whenarecardsdue'] = 'When are cards due';
$string['help:whenarecardsdue_help'] = 'New cards are immediately due for practice. For any other card the deck decides:<ol><li>deck: daily</li><li>deck: after 3 days</li><li>deck: after 7 days</li><li>deck: after 16 days</li><li>deck: after 34 days</li></ol>';
$string['help:practiceanyway'] = 'If you practice anyway, correctly answered cards do not move on, but remain in their current tray.';

// Title and form elements for choosing the settings for a new practice session
$string['titleforchoosesettings'] = 'Practice options';
$string['choosecorrectionmode'] = 'Practice mode';
$string['selfcorrection'] = 'Check yourself';
$string['autocorrection'] = 'Automatic check';
$string['weightopic'] = 'Focus';
$string['notopicpreferred'] = 'no preference';
$string['practiceall'] = 'Practice all cards';
$string['practiceall_help'] = 'These cards do not proceed to the next deck if answered correctly. Thus, you can practice as often as you wish without risking that cards leave the cardbox forever after only a few days.';
$string['onlyonetopic'] = 'Topic';
$string['maxnumbercardspractice'] = 'Max. number of cards';
$string['undefined'] = 'No limit';

$string['beginpractice'] = 'Start practice';
$string['applysettings'] = 'Apply';
$string['cancel'] = 'Cancel';

// Practice mode: Buttons.
$string['options'] = 'Practice anyway';
$string['endpractice'] = 'End practice';

$string['dontknow'] = "I don't know";
$string['checkanswer'] = 'Check';
$string['submitanswer'] = 'Answer';
$string['markascorrect'] = 'Correct';
$string['markasincorrect'] = 'Incorrect';
$string['override'] = 'Override';
$string['override_iscorrect'] = 'No, I was right!';
$string['override_isincorrect'] = 'No, I was wrong.';
$string['proceed'] = 'Next';
$string['suggestanswer_label'] = 'Please suggest a new solution';
$string['suggestanswer'] = 'Suggest answer';
$string['suggestanswer_send'] = 'Send answer';
$string['cardsleft'] = 'Remaining cards:';

$string['solution'] = 'Solution';
$string['yoursolution'] = 'Your answer';

// Practice mode: Feedback
$string['feedback:correctandcomplete'] = 'Well done!';
$string['feedback:incomplete'] = 'Answers missing!';
$string['feedback:correctbutincomplete'] = 'There are {$a} answers missing.';
$string['feedback:incorrectandpossiblyincomplete'] = 'Incorrect!';
$string['feedback:notknown'] = 'No answer given!';

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

$string['titlenumberofcards'] = 'Number of cards per session';
$string['numberofcards'] = 'Number';
$string['numberofcardsavg'] = 'Average';
$string['numberofcardsmin'] = 'Minimum';
$string['numberofcardsmax'] = 'Maximum';

$string['titledurationofasession'] = 'Duration of a session';
$string['duration'] = 'Duration (min)';
$string['durationavg'] = 'Average';
$string['durationmin'] = 'Minimum';
$string['durationmax'] = 'Maximun';


// Review.
$string['approve'] = 'Approve';
$string['reject'] = 'Reject';
$string['edit'] = 'Edit';
$string['skip'] = 'Skip';
$string['countcardapprove'] = '{&a} cards have been approved and ready for practise';
$string['countcardreject'] = '{&a} cards have been rejected';
$string['rejectcard'] = 'Reject Card';
$string['rejectcardinfo'] = 'Do you want to reject the selected {$a} cards? These cards will be deleted and cannot be recovered.';

$string['allanswersnecessary'] = "All";
$string['oneanswersnecessary'] = "One";
$string['allanswersnecessary_help'] = "all answers necessary";
$string['oneanswersnecessary_help'] = "one answer necessary";

// Statistics
$string['strftimedate'] = '%d. %B %Y';
$string['strftimedatetime'] = '%d. %b %Y, %H:%M';
$string['strftimedateshortmonthabbr'] = '%d %b';


$string['barchartxaxislabel'] = 'Deck';
$string['barchartyaxislabel'] = 'Card count';
$string['barchartstatistic1'] = 'Number of cards per deck for all students';
$string['linegraphxaxislabel'] = 'Date';
$string['linegraphyaxislabel_performance'] = '% known';
$string['linegraphyaxislabel_numbercards'] = 'Number of cards';
$string['linegraphyaxislabel_duration'] = 'Duration (min)';
$string['linegraphtooltiplabel_below_threshold'] = 'no statistics because <{$a} users practiced that week';
$string['lastpractise'] = 'last practised';
$string['nopractise'] = 'not practised yet';
$string['newcard'] = 'new cards';
$string['knowncard'] = 'mastered cards';
$string['averagestudentscompare'] = 'average of all students';
$string['absolutenumberofcards'] = 'Absolute number of cards';

$string['yes'] = 'Yes';
$string['no'] = 'No';
$string['cancel'] = 'Cancel';
$string['deletecard'] = 'Delete card?';
$string['deletecardinfo'] = 'The card and the progress of this card will be deleted for all users.';
$string['delete'] = 'Delete';


$string['topicfilter'] = 'Topic ';
$string['deckfilter'] = 'Deck';
$string['noselection'] = 'all';
$string['createddate'] = 'Created date';
$string['alphabetical'] = 'Alphabetical';
$string['sorting'] = 'Sort';
$string['descending'] = 'descending';
$string['ascending'] = 'ascending';

$string['card'] = 'Question/Answer:';
$string['cardposition'] = 'Deck:';
$string['cardposition_help'] = 'Shows which deck this card is in. The higher the number the better the card has already been learned. New cards are not yet in a box. After box 5 cards are considered "mastered" and are no longer practiced.';

// Overview Tab.
$string['student:deckdescription'] = 'This card lies in deck {$a}';
$string['manager:deckdescription'] = 'On average, this card lies in deck {$a} among all students';
$string['manager:repeatdesc'] = 'This card was mastered by students, on average, after {$a} repetitions';
$string['student:repeatdesc'] = 'This card was mastered after {$a} repetitions';

// Edit topics Tab.
$string['deletetopic'] = 'Delete topic';
$string['deletetopicinfo'] = 'Do you want to delete the selected topic {$a}? For cards assigned to this topic, this will set the topic to "not assigned".';
$string['createtopic'] = 'Add';
$string['existingtopics'] = 'already existing topics';
$string['notopics'] = 'there are no topics yet';

// Settings.
$string['statistics_heading'] = 'Statistics';
$string['weekly_users_practice_threshold'] = 'Threshold practicers per week';
$string['weekly_users_practice_threshold_desc'] = 'How many users need to practice per week in order for managers to see statistics for that week.';
$string['weekly_enrolled_students_threshold'] = 'Threshold enrolled students';
$string['weekly_enrolled_students_threshold_desc'] = 'How many students need to be enrolled into the course for weekly statistics to be shown for managers.';
$string['qmissing'] = 'Question missing.';
$string['qfieldmissing'] = 'Question field missing.';
$string['amissing'] = 'Answer missing.';
$string['afieldmissing'] = 'Answer field missing.';
$string['successmsg'] = ' card(s) have been imported successfully.';
$string['errormsg'] = 'The below lines could not be imported into cards';
$string['status'] = 'status';
$string['continue'] = 'Continue';
$string['unmatchedanswers'] = 'CSV file requires {$a->csvschema} answers; only {$a->actual} given.';
$string['emptyimportfile'] = 'Nothing to import - CSV file has no rows.';
