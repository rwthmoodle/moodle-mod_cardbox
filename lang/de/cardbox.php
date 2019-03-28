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
$string['cardbox'] = 'Karteikasten'; // superfluous?
$string['modulename'] = 'Karteikasten';
$string['pluginname'] = 'Karteikasten';
$string['modulenameplural'] = 'Karteikästen';
$string['cardboxname'] = 'Name des Karteikastens';
$string['pluginadministration'] = 'Karteikasten Administration';
$string['setting_autocorrection'] = 'Autokorrektur aktivieren';
$string['setting_autocorrection_help'] = 'Die Autokorrektur unterstützt kein Latex. Ihre Aktivierung wird nicht empfohlen, wenn Formeln abfragt werden.';
$string['setting_autocorrection_label'] = 'Vorsicht bei Formeln!';

// Tab navigation
$string['addflashcard'] = 'Karte anlegen';
$string['practice'] = 'Üben';
$string['review'] = 'Freigabe';

// Subpage titles
$string['titleforaddflashcard'] = 'Karte anlegen';
$string['titleforpractice'] = 'Üben';
$string['titleforreview'] = 'Karte überprüfen';
$string['titleforcardedit'] = 'Karte bearbeiten';

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
$string['success:edit'] = 'Die Karte wurde erfolgreich bearbeitet.';
$string['success:reject'] = '.';
//$string['success:skip'] = '.';

// Error notifications
$string['error:updateafterreview'] = 'Die Aktion konnte nicht gespeichert werden.';

// Info notifications
$string['info:nocardsavailableforreview'] = 'Zurzeit liegen keine neuen Karten zur Überprüfung vor.';
$string['info:waslastcardforreview'] = 'Dies war die letzte zu überprüfende Karte.';
$string['info:nocardsavailable'] = 'Ihre Lernkartei enthält zurzeit keine Karten.';

// Title and form elements for choosing the settings for a new practice session
$string['titleforchoosesettings'] = 'Wie und was möchten Sie üben?';
$string['choosecorrectionmode'] = 'Übungsmodus';
$string['choosecorrectionmode_help'] = 'Sie können zwischen Selbstkontrolle und automatischer Kontrolle wählen. In beiden Fällen können Sie entscheiden, ob eine Antwort als richtig oder falsch gewertet wird.';
$string['selfcorrection'] = 'Selbstkontrolle';
$string['autocorrection'] = 'Automatische Kontrolle';
$string['weightopic'] = 'Thema gewichten';
$string['weightopic_help'] = 'Sie können ein Thema auswählen, das verstärkt geübt werden soll. Dies kann in Vorbereitung auf einen Test sinnvoll sein.';
$string['notopicpreferred'] = 'keine Gewichtung';
$string['beginpractice'] = 'Start';
$string['applysettings'] = 'Anwenden';
$string['cancel'] = 'Abbrechen';

// Practice mode: Buttons.
$string['options'] = 'Optionen';
//$string['options'] = 'Korrekturmodus';

$string['checkanswer'] = 'Überprüfen';
$string['submitanswer'] = 'Antworten';
$string['dontknow'] = 'Weiß ich nicht';

$string['markascorrect'] = 'Gewusst';
$string['markasincorrect'] = 'Nicht gewusst';
$string['override'] = 'Überstimmen';
$string['override_iscorrect'] = 'Doch, ich hatte Recht!';
$string['override_isincorrect'] = 'Als falsch werten';
$string['proceed'] = 'Weiter';

$string['solution'] = 'Lösung';
$string['yoursolution'] = 'Ihre Lösung';

// Practice mode: Feedback

$string['feedback:correctandcomplete'] = 'Richtig!';
$string['feedback:incomplete'] = 'Unvollständig.';
$string['feedback:correctbutincomplete'] = 'Es fehlen {$a} Antworten.';
$string['feedback:incorrectandpossiblyincomplete'] = 'Das war leider nichts.'; // TODO
$string['feedback:notknown'] = 'Keine Antwort.';

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
