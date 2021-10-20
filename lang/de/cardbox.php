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
$string['setting_autocorrection'] = 'Autokorrektur erlauben';
$string['setting_autocorrection_help'] = 'Die Studierenden wählen vor jeder Übung, ob sie ihre Antworten selbst überprüfen oder eintippen und durch das Programm überprüfen lassen möchten. Die Autokorrektur unterstützt jedoch nur Texteingaben. Sie sollte deaktiviert werden, falls z.B. Formeln abgefragt werden.';
$string['setting_autocorrection_label'] = '<font color="red">nur für Textinhalte geeignet</font>';
$string['necessaryanswers_activity'] = 'Standardeinstellung für "Wie viele Antworten werden benötigt?"';
$string['necessaryanswers_activity_help'] = 'Den Standardwert für Auswahlfeld "Wie viele Antworten werden benötigt?" im Kartenerstellungsformular festlegen.';
$string['necessaryanswers_activity_locked'] = 'Nachträgliches Verändern der Anzahl an notwendigen Antworten erlauben?';
$string['necessaryanswers_activity_locked_help'] = 'Wenn "Ja" ausgewählt ist, dann ist es möglich beim Erstellen oder Bearbeiten einer Karte die Anzahl an erforderlichen Antworten zu verändern.';
$string['casesensitive'] = 'Groß- und Kleinschreibung beachten';
$string['casesensitive_help'] = 'Gibt an, ob beim Üben mit automatischer Kontrolle Eingaben, die sich nur in der Groß-/Kleinschreibung von der richtig Antwort unterscheiden, auch als richtig gewertet werden.';
$string['numberofcardssetting'] = 'Anzahl an zu übenden Karten';
$string['numberofcardssetting_help'] = 'Gibt an, wie viele Karten die Studenten pro Übeeinheit lernen sollen. Ist "Studenten entscheiden" ausgewählt, so haben sie die freie Wahl.';
$string['studentschoose'] = 'Studenten entscheiden';
$string['messageprovider:memo'] = 'Übungserinnerungen des Karteikastens';
$string['messageprovider:changenotification'] = 'Benachrichtigung über geänderte Lernkarte';
$string['changenotification:subject'] = 'Änderungsmitteilung';
$string['changenotification:message'] = 'Die folgende Lernkarte wurde bearbeitet. Sie sehen die bearbeitete Version.';

// Reminders
$string['send_practice_reminders'] = 'E-Mail-Erinnerungen an die Kursteilnehmer/innen versenden';
$string['remindersubject'] = 'Übungserinnerung';
$string['remindergreeting'] = 'Hallo {$a}, ';
$string['remindermessagebody'] = "bitte denken Sie daran, regelmäßig mit Ihrem Karteikasten zu lernen.";
$string['reminderfooting'] = 'Diese Erinnerung wurde automatisch von Ihrem Karteikasten "{$a->cardboxname}" im Kurs "{$a->coursename}" versendet.';

// Tab navigation
$string['addflashcard'] = 'Karte anlegen';
$string['practice'] = 'Üben';
$string['statistics'] = 'Fortschritt';
$string['overview'] = 'Übersicht';
$string['review'] = 'Freigabe';
$string['massimport'] = 'Karten importieren';

// Subpage titles
$string['titleforaddflashcard'] = 'Legen Sie mithilfe des Formulars eine neue Lernkarte an.';//'Neue Karte';
$string['titleforpractice'] = 'Üben';
$string['titleforreview'] = 'Hier können Sie die von den Kursteilnehmer/innen angelegten Karten zum Lernen freigeben, sie bearbeiten oder löschen.'; //'Karte überprüfen';
$string['titleforcardedit'] = 'Karte bearbeiten';
$string['intro:overview'] = 'Die Übersicht umfasst alle bereits freigegebenen Karten.';

// Form elements for creating a new card
$string['choosetopic'] = 'Thema';
$string['reviewtopic'] = 'THEMA: ';
$string['notopic'] = 'nicht zugeordnet';
$string['addnewtopic'] = 'Thema anlegen';
$string['entertopic'] = 'Thema anlegen';
$string['enterquestion'] = 'Frage';
$string['entercontextquestion'] = 'Zusatzinformationen zur Frage';
$string['addcontext'] = 'Kontextzeile einblenden';
$string['removecontext'] = 'Kontextzeile ausblenden';
$string['entercontextanswer'] = 'Zusatzinformationen zur Antwort';
$string['necessaryanswers_card'] = 'Wie viele Antworten werden benötigt?';
$string['necessaryanswers_all'] = 'Alle';
$string['necessaryanswers_one'] = 'Eine';
$string['addimage'] = 'Bildoptionen einblenden';
$string['removeimage'] = 'Bildoptionen ausblenden';
$string['image'] = 'Bild zur Frage';
$string['imagedescription'] = 'Beschreibung des Bildes für jemanden, der das Bild nicht sehen kann (empfohlen)';
$string['imgdescriptionnecessary_label'] = 'Beschreibung nicht notwendig';
$string['answerimage'] = 'Bild zur Lösung';
$string['addsound'] = 'Audiooptionen einblenden';
$string['removesound'] = 'Bildoptionen ausblenden';
$string['sound'] = 'Tonaufnahme zur Frage';
$string['answersound'] = 'Tonaufnahme zur Lösung';
$string['enteranswer'] = 'Lösungstext';
$string['answer_repeat'] = 'weitere Lösung';
//$string['answer_repeat_help'] = 'Besteht die Lösung aus mehreren Teilen, so klicken Sie bitte auf "weitere Lösung", um diese einzeln einzugeben. Nur so kann das Programm prüfen, ob ein Nutzer die Antwort vollständig kennt. Handelt es sich dagegen um alternative Lösungsvorschläge, so benutzen sie bitte nur das erste Eingabefeld.';

/* $string['answer_repeat_help'] = '<b>Bei mehreren Lösungen</b><ul><li><b>Alternative Lösungen</b><br>Müssen die Studierenden nur <em>eine</em> Lösung kennen, nutzen Sie bitte das Lösungsfeld und klicken Sie nicht auf "weitere Lösung".</li><li><b>Mehrteilige Lösungen</b><br>Müssen alle Teillösungen gekannt werden, so geben Sie diese bitte einzeln per Klick auf "weitere Lösung" ein.</li></ul>'; */
$string['answer_repeat_help'] = 'Bei mehreren Lösungen nutzen Sie bitte für jede Antwort ein separates Lösungsfeld.<br> Ein weiteres Lösungsfeld kann durch den Button "weitere Lösung" hinzugefügt werden.<br> Um einzustellen, ob Studierende nun alle Antworten wissen müssen oder nur eine (falls es um Alternativantworten geht) nutzen Sie bitte den Dropdown darunter.';

$string['addanswer'] = 'weitere Lösung';
$string['savecard'] = 'Speichern';
$string['saveandaccept'] = 'Speichern und freigeben';

// Success notifications
$string['success:addnewcard'] = 'Die Lernkarte wurde erstellt und wartet auf Freigabe.';
$string['success:addandapprovenewcard'] = 'Die Lernkarte wurde erstellt und für die Übung freigegeben.';
$string['success:approve'] = 'Die Karte wurde zum Lernen freigegeben.';
$string['success:edit'] = 'Die Karte wurde erfolgreich bearbeitet.';
$string['success:reject'] = 'Die Karte wurde gelöscht.';
//$string['success:skip'] = '.';

// Error notifications
$string['error:updateafterreview'] = 'Die Aktion konnte nicht gespeichert werden.';
$string['error:createcard'] = 'Die Karte wurde noch nicht gespeichert, da sie entweder keine Frage und/oder keine Lösung enthält oder falls ein Bild hochgeladen wurde die Bildbeschreibung fehlt.';

// Import cards
$string['examplesinglecsv'] = 'Beispieltextdatei für Karten mit nur einer Antwort.';
$string['examplesinglecsv_help'] = 'Beispieltextdatei für Karten mit nur einer Antwort.';
$string['examplemulticsv'] = 'Beispieltextdatei für Karten mit mehreren Antworten';
$string['examplemulticsv_help'] = 'Beispieltextdatei für Karten mit mehreren Antworten';
$string['cancelimport'] = 'Import wurde storniert';
$string['importpreview'] = 'Preview der Importkarten';
$string['importsuccess'] = '{$a} Karten erfolgreich importiert';

// Info notifications
$string['info:statisticspage'] = 'Hier sehen Sie, wie viele fällige und nicht-fällige Karten sich in Ihrem Karteikasten befinden und wie erfolgreich Ihre Übungen waren.';
$string['info:nocardsavailableforreview'] = 'Es liegen keine (weiteren) Karten zur Überprüfung vor.';
$string['info:waslastcardforreview'] = 'Dies war die letzte zu überprüfende Karte.';
$string['info:nocardsavailableforoverview'] = 'In dieser Kartenbox befinden sich keine Karten.';
$string['info:nocardsavailable'] = 'Ihre Lernkartei enthält zurzeit keine Karten.';
$string['help:nocardsavailable'] = 'Karteikasten leer';
$string['help:nocardsavailable_help'] = 'Mögliche Gründe:<ul><li>Es wurden noch keine Karten angelegt.</li><li>Die/Der Dozent/in hat die Karten noch nicht überprüft und freigegeben.</li></ul>';
$string['info:nocardsavailableforpractice'] = 'Derzeit liegen keine Karten zur Übung bereit.';
$string['help:nocardsavailableforpractice'] = 'Keine Karten';
$string['help:nocardsavailableforpractice_help'] = 'Sie haben alle zurzeit verfügbaren Karten 5x richtig beantwortet. Damit gelten sie als gelernt und werden nicht mehr wiederholt.</ul>';
$string['info:nocardsdueforpractice'] = "Derzeit sind keine Karten zur Wiederholung fällig.";
$string['help:nocardsdueforpractice'] = 'Keine Karten fällig';
$string['help:nocardsdueforpractice_help'] = 'Neue Karten sind sofort fällig. Ansonsten entscheidet das Fach:<ol><li>Fach: täglich</li><li>Fach: nach 3 Tagen</li><li>Fach: nach 7 Tagen</li><li>Fach: nach 16 Tagen</li><li>Fach: nach 34 Tagen</li></ol>';
$string['help:whenarecardsdue'] = 'Wann sind Karten fällig';
$string['help:whenarecardsdue_help'] = 'Neue Karten sind sofort zur Wiederholung fällig. Ansonsten entscheidet das Fach:<ol><li>Fach: täglich</li><li>Fach: nach 3 Tagen</li><li>Fach: nach 7 Tagen</li><li>Fach: nach 16 Tagen</li><li>Fach: nach 34 Tagen</li></ol>';
$string['help:practiceanyway'] = 'Wenn Sie trotzdem üben, wandern richtig beantwortete Karten nicht weiter, sondern verbleiben in ihrem aktuellen Fach.';

// Title and form elements for choosing the settings for a new practice session
$string['titleforchoosesettings'] = 'Übungseinstellungen';
$string['choosecorrectionmode'] = 'Übungsmodus';
$string['selfcorrection'] = 'Selbstkontrolle';
$string['autocorrection'] = 'Automatische Kontrolle';
$string['weightopic'] = 'Fokus';
$string['notopicpreferred'] = 'keine Gewichtung';
$string['onlyonetopic'] = 'Thema';
$string['practiceall'] = 'Alle Karten üben';
$string['practiceall_help'] = 'Zu früh wiederholte Karten wandern bei richtiger Antwort kein Fach weiter. So können Sie in Prüfungsphasen beliebig oft üben, ohne dass die Karten den Karteikasten nach 1 Tag als dauerhaft gelernt verlassen.';
$string['maxnumbercardspractice'] = 'Max. Anzahl an Karten';
$string['undefined'] = 'Unbegrenzt';

$string['beginpractice'] = 'Jetzt üben';
$string['applysettings'] = 'Anwenden';
$string['cancel'] = 'Abbrechen';

// Practice mode: Buttons.
$string['options'] = 'Trotzdem üben';
$string['endpractice'] = 'Üben beenden';

$string['checkanswer'] = 'Überprüfen';
$string['submitanswer'] = 'Antworten';
$string['dontknow'] = 'Weiß ich nicht';
$string['markascorrect'] = 'Gewusst';
$string['markasincorrect'] = 'Nicht gewusst';
$string['override'] = 'Überstimmen';
$string['override_iscorrect'] = 'Als richtig werten';
$string['override_isincorrect'] = 'Als falsch werten';
$string['proceed'] = 'Weiter';
$string['suggestanswer_label'] = 'Bitte schlagen Sie eine neue Lösung vor';
$string['suggestanswer'] = 'Antwort vorschlagen';
$string['suggestanswer_send'] = 'Antwort absenden';
$string['cardsleft'] = 'Verbleibende Karten:';

$string['solution'] = 'Lösung';
$string['yoursolution'] = 'Ihre Antwort';

// Practice mode: Feedback
$string['feedback:correctandcomplete'] = 'Richtig!';
$string['feedback:incomplete'] = 'Unvollständig!';
$string['feedback:correctbutincomplete'] = 'Es fehlen {$a} Antworten.';
$string['feedback:incorrectandpossiblyincomplete'] = 'Falsche Antwort!';
$string['feedback:notknown'] = 'Keine Antwort!';

$string['sessioncompleted'] = 'Fertig! :-)';
$string['titleprogresschart'] = 'Ergebnis';
$string['right'] = 'richtig';
$string['wrong'] = 'falsch';
$string['titleoverviewchart'] = 'Karteikasten';
$string['new'] = 'neu';
$string['known'] = 'gelernt';
$string['flashcards'] = 'Karten';
$string['flashcardsdue'] = 'fällig';
$string['flashcardsnotdue'] = 'noch nicht fällig';
$string['box'] = 'Fach';

$string['titleperformancechart'] = 'Vergangene Übungen';
$string['performance'] = '% gewusst:';

$string['titlenumberofcards'] = 'Anzahl an Karten pro Übung';
$string['numberofcards'] = 'Anzahl';
$string['numberofcardsavg'] = 'Durschnitt';
$string['numberofcardsmin'] = 'Minimum';
$string['numberofcardsmax'] = 'Maximum';

$string['titledurationofasession'] = 'Dauer einer Übung';
$string['duration'] = 'Dauer (min)';
$string['durationavg'] = 'Durschnitt in min';
$string['durationmin'] = 'Minimum in min';
$string['durationmax'] = 'Maximun in min';


// Review.
$string['approve'] = 'Freigeben';
$string['reject'] = 'Ablehnen';
$string['edit'] = 'Bearbeiten';
$string['skip'] = 'Überspringen';
$string['countcardapprove'] = '{&a} Karten wurden genehmigt und stehen für die Übung bereit';
$string['countcardreject'] = '{&a} Karten wurden abgelehnt';
$string['rejectcard'] = 'Karte ablehnen';
$string['rejectcardinfo'] = 'Möchten Sie die ausgewählten {$a} Karten ablehnen? Diese Karten werden gelöscht und können nicht wiederhergestellt werden.';
/* $string['allanswersnecessary'] = "<b style='float: right; padding-top: 5px'>alle Antworten notwendig</b>";
$string['oneanswersnecessary'] = "<b style='float: right; padding-top: 5px'>eine Antwort notwendig</b>"; */
$string['allanswersnecessary'] = "<b>alle Antworten notwendig</b>";
$string['oneanswersnecessary'] = "<b>eine Antwort notwendig</b>";

// Statistics
$string['strftimedate'] = '%d. %B %Y';
$string['strftimedatetime'] = '%d. %b %Y, %H:%M';
$string['strftimedateshortmonthabbr'] = '%d %b';


$string['barchartxaxislabel'] = 'Fach';
$string['barchartyaxislabel'] = 'Kartenzahl';
$string['barchartstatistic1'] = 'Anzahl der Karten pro Deck für alle Studierenden';
$string['linegraphxaxislabel'] = 'Datum';
$string['linegraphyaxislabel_performance'] = '% gewusst';
$string['linegraphyaxislabel_numbercards'] = 'Anzahl an Karten';
$string['linegraphyaxislabel_duration'] = 'Dauer (min)';
$string['linegraphtooltiplabel_below_threshold'] = 'keine Statistiken, weil <{$a} Teilnehmer/innen in der Woche geübt haben';
$string['lastpractise'] = 'zuletzt geübt';
$string['nopractise'] = 'noch nicht geübt';
$string['newcard'] = 'karten neu';
$string['knowncard'] = 'karten gelernt';
$string['averagestudentscompare'] = 'Durschnitt aller Studenten';
$string['absolutenumberofcards'] = 'Absolute Anzahl von Karten';

$string['yes'] = 'Ja';
$string['no'] = 'Nein';
$string['cancel'] = 'Abbrechen';
$string['deletecard'] = 'Karte löschen?';
$string['deletecardinfo'] = 'Die Karte sowie der Lernfortschritt dieser Karte aller User wird gelöscht.';
$string['delete'] = 'Löschen';

$string['topicfilter'] = 'Thema ';
$string['noselection'] = 'alle';

$string['card'] = 'Frage/Antwort:';
$string['cardposition'] = 'Fach:';
$string['cardposition_help'] = 'Hier wird angezeigt, in welchem Fach sich diese Karte befindet. Je höher die Nummer, desto besser ist die Karte bereits gelernt. Neue Karten sind in keinem Fach. Nach Fach 5 werden Karten als "gelernt" angesehen und nicht mehr geübt.';
//Overview Tab
$string['student:deckdescription'] = 'Diese Karte liegt in Fach {$a}';
$string['manager:deckdescription'] = 'Im Durchschnitt liegt diese Karte für alle Studierenden in Fach {$a}';
$string['manager:repeatdesc'] = 'Diese Karte wurde von den Studierenden, im Durchschnitt, nach {$a} Wiederholungen gelernt';
$string['student:repeatdesc'] = 'Diese Karte wurde nach {$a} Wiederholungen gelernt';

// Settings.
$string['statistics_heading'] = 'Statistiken';
$string['weekly_users_practice_threshold'] = 'Untergrenze Übende pro Woche';
$string['weekly_users_practice_threshold_desc'] = 'Wie viele Teilnehmende pro Woche mindestens üben müssen, damit Manager für die Woche Statistiken sehen.';
$string['weekly_enrolled_students_threshold'] = 'Untegrenze eingeschriebene Studierende';
$string['weekly_enrolled_students_threshold_desc'] = 'Wie viele Studierende in den Kurs eingeschrieben sein müssen, damit Manager wöchentliche Statistiken sehen.';
