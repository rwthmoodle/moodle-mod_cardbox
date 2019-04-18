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
$string['modulename_help'] = '<p>Mit dieser Aktivität können Lernkarten erstellt und nach dem Karteikasten-Prinzip geübt werden. Besonders geeignet ist der Karteikasten für Vokabeln, Fachbegriffe und Formeln.</p><p>Alle Teilnehmer/innen können Lernkarten für den gesamten Kurs erstellen. Die Lernkarten werden jedoch erst übernommen, nachdem ein/e Dozent/in sie freigegeben hat.</p>';
$string['pluginname'] = 'Karteikasten';
$string['modulenameplural'] = 'Karteikästen';
$string['cardboxname'] = 'Name des Karteikastens';
$string['pluginadministration'] = 'Karteikasten Administration';
$string['setting_autocorrection'] = 'Autokorrektur aktivieren';
$string['setting_autocorrection_help'] = 'Die Autokorrektur unterstützt nur Texteingaben. Sie sollte deaktiviert werden, falls z.B. Formeln abgefragt werden.';
$string['setting_autocorrection_label'] = 'Vorsicht geboten!';

// Tab navigation
$string['addflashcard'] = 'Karte anlegen';
$string['practice'] = 'Üben';
$string['statistics'] = 'Fortschritt';
$string['review'] = 'Freigabe';

// Subpage titles
$string['titleforaddflashcard'] = 'Neue Karte';
$string['titleforpractice'] = 'Üben';
$string['titleforreview'] = 'Karte überprüfen';
$string['titleforcardedit'] = 'Karte bearbeiten';

// Form elements for creating a new card
$string['choosetopic'] = 'Thema';
$string['reviewtopic'] = 'Thema: ';
$string['notopic'] = 'nicht zugeordnet';
$string['addnewtopic'] = 'Thema anlegen';
$string['entertopic'] = 'Thema anlegen';
$string['enterquestion'] = 'Frage';
$string['image'] = 'Bild zur Frage';
$string['enteranswer'] = 'Lösung';
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
$string['help:nocardsavailable'] = 'Karteikasten leer';
$string['help:nocardsavailable_help'] = 'Mögliche Gründe:<ul><li>Es wurden noch keine Karten angelegt.</li><li>Die/Der Dozent/in hat die Karten noch nicht überprüft und freigegeben.</li></ul>';
$string['info:nocardsavailableforpractice'] = 'Derzeit liegen keine Karten zur Übung bereit.';
$string['help:nocardsavailableforpractice'] = 'Keine Karten';
$string['help:nocardsavailableforpractice_help'] = 'Mögliche Gründe:<ul><li>Eine Karte kann frühestens nach 24 Stunden erneut geübt werden.</li><li>Karten, die 5x richtig beantwortet wurden, gelten als gelernt und werden nicht mehr wiederholt.</li></ul>';

// Title and form elements for choosing the settings for a new practice session
$string['titleforchoosesettings'] = 'Übungseinstellungen';
$string['choosecorrectionmode'] = 'Übungsmodus';
$string['choosecorrectionmode_help'] = 'Sie können Ihre Antworten eingeben und korrigieren lassen. Möchten Sie lieber mündlich antworten oder Lösungen handschriftlich notieren, so wählen Sie den Selbstkontrollmodus.';
$string['selfcorrection'] = 'Selbstkontrolle';
$string['autocorrection'] = 'Automatische Kontrolle';
$string['weightopic'] = 'Thema gewichten';
$string['weightopic_help'] = 'Wenn Sie ein Thema gewichten, wird dieses verstärkt geübt. Dies bedeutet jedoch nicht, dass alle oder ausschließlich Karten zu diesem Thema geübt werden.';
$string['notopicpreferred'] = 'keine Gewichtung';
$string['beginpractice'] = 'Jetzt üben';
$string['applysettings'] = 'Anwenden';
$string['cancel'] = 'Abbrechen';

// Practice mode: Buttons.
$string['options'] = 'Einstellungen';
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
$string['feedback:incorrectandpossiblyincomplete'] = 'Falsche Antwort';
$string['feedback:notknown'] = 'Keine Antwort.';

$string['sessioncompleted'] = 'Fertig! :-)';
$string['titleprogresschart'] = 'Ergebnis';
$string['right'] = 'richtig';
$string['wrong'] = 'falsch';

$string['titleoverviewchart'] = 'Karteikasten';
$string['new'] = 'neu';
$string['known'] = 'gelernt';
$string['flashcards'] = 'Karten';
$string['box'] = 'Fach';

$string['titleperformancechart'] = 'Vergangene Übungen';
$string['performance'] = '% gewusst:';

// Review.
$string['approve'] = 'Freigeben';
$string['reject'] = 'Ablehnen';
$string['edit'] = 'Bearbeiten';
$string['skip'] = 'Überspringen';

$string['strftimedate'] = '%d. %B %Y';
$string['strftimedatetime'] = '%d. %b %Y, %H:%M';

$string['barchartxaxislabel'] = 'Fach';
$string['barchartyaxislabel'] = 'Kartenzahl';
$string['linegraphxaxislabel'] = 'Datum';
$string['linegraphyaxislabel'] = '% gewusst';