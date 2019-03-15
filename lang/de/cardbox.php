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
$string['cardbox'] = 'Karteikasten'; // superfluous?
$string['modulename'] = 'Karteikasten';
$string['pluginname'] = 'Karteikasten';
$string['modulenameplural'] = 'Karteikästen';
$string['cardboxname'] = 'Name des Karteikastens';
$string['pluginadministration'] = 'Karteikasten Administration';

// Tab navigation
$string['addflashcard'] = 'Karte anlegen';
$string['practice'] = 'Üben';
$string['review'] = 'Freigabe';

// Subpage titles
$string['titleforaddflashcard'] = 'Karte anlegen';
$string['titleforpractice'] = 'Üben';

// Form elements for creating a new card
$string['choosetopic'] = 'Thema';
$string['notopic'] = 'nicht zugeordnet';
$string['addnewtopic'] = 'Thema anlegen';
$string['entertopic'] = 'Thema anlegen';
$string['enterquestion'] = 'Frage/Begriff eingeben';
$string['image'] = 'Bild hinzufügen';
$string['enteranswer'] = 'Lösung eingeben';
$string['addanswer'] = 'weitere Lösung';
$string['savecard'] = 'Speichern';

// Success notifications
$string['success:addnewcard'] = 'Die Lernkarte wurde erstellt und wartet auf Freigabe.';
$string['success:approve'] = 'Die Karte wurde zum Lernen freigegeben.';
$string['success:edit'] = 'Die Karte wurde erfolgreich bearbeitet und zum Lernen freigegeben.';
$string['success:reject'] = '.';
$string['success:skip'] = '.';

// Title and form elements for choosing the settings for a new practice session
$string['titleforchoosesettings'] = 'Wie und was möchten Sie üben?';
$string['choosecorrectionmode'] = 'Korrekturmodus';
$string['choosecorrectionmode_help'] = 'Sie können zwischen Selbstkontrolle und automatischer Kontrolle wählen. Auch bei der automatischen Kontrolle können Sie entscheiden, ob eine Antwort als richtig bewertet werden soll.';
$string['selfcorrection'] = 'Selbstkonstrolle';
$string['autocorrection'] = 'Automatisierte Kontrolle';
$string['weightopic'] = 'Thema gewichten';
$string['weightopic_help'] = 'Sie können ein Thema auswählen, das verstärkt geübt werden soll. Dies kann in Vorbereitung auf einen Test sinnvoll sein.';
$string['notopicpreferred'] = 'keine Gewichtung';
$string['beginpractice'] = 'Start';

// Practice mode: Buttons.
$string['submitanswer'] = 'Überprüfen';
$string['markascorrect'] = 'Gewusst';
$string['markasincorrect'] = 'Nicht gewusst';
// Practice mode: Feedback
$string['sessioncompleted'] = 'Fertig! :-)';

$string['titleprogresschart'] = 'Ergebnis';
$string['right'] = 'richtig';
$string['wrong'] = 'falsch';

$string['titleoverviewchart'] = 'Karteikasten';
$string['new'] = 'neu';
$string['flashcards'] = 'Karten';
$string['box'] = 'Kästchen';

// Review.
$string['approve'] = 'Freigeben';
$string['reject'] = 'Ablehnen';
$string['edit'] = 'Bearbeiten';
$string['skip'] = 'Überspringen';