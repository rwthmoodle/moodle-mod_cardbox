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
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/mod/cardbox/locallib.php');

function xmldb_cardbox_upgrade($oldversion) {

    global $CFG, $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2019022601) {

        // Define table cardbox_cards to be created.
        $table = new xmldb_table('cardbox_cards');

        // Adding fields to table.

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('cardbox', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('topic', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'cardbox');
        $table->add_field('author', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'topic');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'author');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timecreated');
        $table->add_field('approvedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timemodified');

        // Adding keys to table cardbox_cards.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for cardbox_cards.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019022601, 'cardbox');
    }

    if ($oldversion < 2019022602) {

        // Define table cardbox_cards to be created.
        $table = new xmldb_table('cardbox_progress');

        // Adding fields to table.

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('card', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');
        $table->add_field('cardposition', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'card');
        $table->add_field('lastpracticed', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'cardposition');
        $table->add_field('repetitions', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'lastpracticed');

        // Adding keys to table cardbox_progress.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for cardbox_progress.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019022602, 'cardbox');
    }

    if ($oldversion < 2019022603) {

        // Define table cardbox_cardcontents to be created.
        $table = new xmldb_table('cardbox_cardcontents');

        // Adding fields to table.

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('card', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('contenttype', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'card');
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'contenttype');

        // Adding keys to table cardbox_cardcontents.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for cardbox_cardcontents.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019022603, 'cardbox');
    }

    if ($oldversion < 2019022604) {

        // Define table cardbox_contenttypes to be created.
        $table = new xmldb_table('cardbox_contenttypes');

        // Adding fields to table.

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'type');

        // Adding keys to table cardbox_contenttypes.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for cardbox_contenttypes.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019022604, 'cardbox');
    }

    if ($oldversion < 2019022605) {

        // Define table cardbox_topics to be created.
        $table = new xmldb_table('cardbox_topics');

        // Adding fields to table.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('topicname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');

        // Adding keys to table cardbox_topics.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for cardbox_topics.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019022605, 'cardbox');
    }

    if ($oldversion < 2019022700) {

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019022700, 'cardbox');
    }

    if ($oldversion < 2019022702) {

        // Define field cardside to be added to cardbox_cardcontents.
        $table = new xmldb_table('cardbox_cardcontents');
        $field = new xmldb_field('cardside', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null, 'card');

        // Conditionally launch add field cardside.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019022702, 'cardbox');
    }

    if ($oldversion < 2019032700) {

        // Define field autocorrection to be added to cardbox.
        $table = new xmldb_table('cardbox');
        $field = new xmldb_field('autocorrection', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1', 'introformat');

        // Conditionally launch add field autocorrection.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019032700, 'cardbox');
    }

    if ($oldversion < 2019032800) {

        // Define field cardboxid to be added to changeme.
        $table = new xmldb_table('cardbox_topics');
        $field = new xmldb_field('cardboxid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'topicname');

        // Conditionally launch add field cardboxid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019032800, 'cardbox');
    }

    if ($oldversion < 2019040200) {

        // Define table cardbox_statistics to be created.
        $table = new xmldb_table('cardbox_statistics');

        // Adding fields to table cardbox_statistics.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timeofpractice', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('percentcorrect', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table cardbox_statistics.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for cardbox_statistics.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019040200, 'cardbox');
    }

    if ($oldversion < 2019040201) {

        // Define field cardboxid to be added to cardbox_statistics.
        $table = new xmldb_table('cardbox_statistics');
        $field = new xmldb_field('cardboxid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Conditionally launch add field cardboxid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019040201, 'cardbox');
    }

    if ($oldversion < 2019062700) {

        // Define field approved to be added to cardbox_cards.
        $table = new xmldb_table('cardbox_cards');
        $field = new xmldb_field('approved', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'timemodified');

        // Conditionally launch add field approved.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019062700, 'cardbox');
    }

    if ($oldversion < 2019070101) {

        global $DB;

        $sql = "UPDATE {cardbox_cards} SET approved = 1 WHERE approvedby IS NOT NULL";
        $DB->execute($sql);

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019070101, 'cardbox');
    }

    if ($oldversion < 2019081300) {

        global $DB;
        $table = 'cardbox_contenttypes';
        $condition = [];
        $types = $DB->record_exists($table, $condition);
        if (!$types) {
            $DB->insert_record($table, array('type' => 'file', 'name' => 'image'), false, false);
            $DB->insert_record($table, array('type' => 'text', 'name' => 'text'), false, false);
            $DB->insert_record($table, array('type' => 'file', 'name' => 'audio'), false, false);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2019081300, 'cardbox');
    }

    if ($oldversion < 2021032301) {

        $table = new xmldb_table('cardbox');
        $index = new xmldb_index('course_idx', XMLDB_INDEX_NOTUNIQUE, array('course'));

        // Conditionally launch add index course_idx.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021032301, 'cardbox');
    }

    if ($oldversion < 2021032302) {

        $table = new xmldb_table('cardbox_topics');
        $indexcardboxid = new xmldb_index('cardboxid_idx', XMLDB_INDEX_NOTUNIQUE, array('cardboxid'));

        // Conditionally launch add index course_idx.
        if (!$dbman->index_exists($table, $indexcardboxid)) {
            $dbman->add_index($table, $indexcardboxid);
        }
        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021032302, 'cardbox');
    }

    if ($oldversion < 2021032303) {

        $tablecards = new xmldb_table('cardbox_cards');
        $indexcardboxid = new xmldb_index('cardboxid_idx', XMLDB_INDEX_NOTUNIQUE, array('cardbox'));
        $indextopic = new xmldb_index('topic_idx', XMLDB_INDEX_NOTUNIQUE, array('topic'));
        $indexcardboxapproved = new xmldb_index('cardboxapproved_idx', XMLDB_INDEX_NOTUNIQUE, array('cardbox', 'approved'));
        // Adding indexes to table cardbox_cards
        if (!$dbman->index_exists($tablecards, $indexcardboxid)) {
            $dbman->add_index($tablecards, $indexcardboxid);
        }
        if (!$dbman->index_exists($tablecards, $indextopic)) {
            $dbman->add_index($tablecards, $indextopic);
        }
        if (!$dbman->index_exists($tablecards, $indexcardboxapproved)) {
            $dbman->add_index($tablecards, $indexcardboxapproved);
        }
        upgrade_mod_savepoint(true, 2021032303, 'cardbox');
    }

    if ($oldversion < 2021032304) {

        $tableprogress = new xmldb_table('cardbox_progress');
        $indexuseridcard = new xmldb_index('cardboxid_idx', XMLDB_INDEX_NOTUNIQUE, array('userid', 'card'));
        $indexcardposition = new xmldb_index('cardboxapproved_idx', XMLDB_INDEX_NOTUNIQUE, array('cardposition'));
        // Adding indexes to table cardbox_progress
        if (!$dbman->index_exists($tableprogress, $indexuseridcard)) {
            $dbman->add_index($tableprogress, $indexuseridcard);
        }
        if (!$dbman->index_exists($tableprogress, $indexcardposition)) {
            $dbman->add_index($tableprogress, $indexcardposition);
        }
        upgrade_mod_savepoint(true, 2021032304, 'cardbox');
    }

    if ($oldversion < 2021032305) {
        $tablecardcontents = new xmldb_table('cardbox_cardcontents');
        $indexcardcontenttype = new xmldb_index('card_contenttype_idx', XMLDB_INDEX_NOTUNIQUE, array('card', 'contenttype'));
        $indexcard = new xmldb_index('card_idx', XMLDB_INDEX_NOTUNIQUE, array('card'));
        $indexcardside = new xmldb_index('cardside_idx', XMLDB_INDEX_NOTUNIQUE, array('cardside'));
        $indexcontenttype = new xmldb_index('contenttype_idx', XMLDB_INDEX_NOTUNIQUE, array('contenttype'));
        // Adding indexes to table cardbox_cardcontents
        if (!$dbman->index_exists($tablecardcontents, $indexcardcontenttype)) {
            $dbman->add_index($tablecardcontents, $indexcardcontenttype);
        }
        if (!$dbman->index_exists($tablecardcontents, $indexcard)) {
            $dbman->add_index($tablecardcontents, $indexcard);
        }
        if (!$dbman->index_exists($tablecardcontents, $indexcardside)) {
            $dbman->add_index($tablecardcontents, $indexcardside);
        }
        if (!$dbman->index_exists($tablecardcontents, $indexcontenttype)) {
            $dbman->add_index($tablecardcontents, $indexcontenttype);
        }
        upgrade_mod_savepoint(true, 2021032305, 'cardbox');
    }

    if ($oldversion < 2021032306) {
        $tablecontenttypes = new xmldb_table('cardbox_contenttypes');

        $indexcontentname = new xmldb_index('contentname_idx', XMLDB_INDEX_NOTUNIQUE, array('name'));
        $indextypes = new xmldb_index('types_idx', XMLDB_INDEX_NOTUNIQUE, array('type'));

        // Adding indexes to table cardbox_contenttypes;
        if (!$dbman->index_exists($tablecontenttypes, $indexcontentname)) {
            $dbman->add_index($tablecontenttypes, $indexcontentname);
        }
        if (!$dbman->index_exists($tablecontenttypes, $indextypes)) {
            $dbman->add_index($tablecontenttypes, $indextypes);
        }
        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021032306, 'cardbox');
    }

    if ($oldversion < 2021032307) {
        $tablestatistics = new xmldb_table('cardbox_statistics');

        $indexuseriduserid = new xmldb_index('userid_userid_idx', XMLDB_INDEX_NOTUNIQUE, array('id', 'userid'));
        // Adding indexes to table cardbox_statistics
        if (!$dbman->index_exists($tablestatistics, $indexuseriduserid)) {
            $dbman->add_index($tablestatistics, $indexuseriduserid);
        }

        upgrade_mod_savepoint(true, 2021032307, 'cardbox');
    }

    if ($oldversion < 2021032308) {

        $table = new xmldb_table('cardbox_topics');
        $indextopicname = new xmldb_index('topicname_idx', XMLDB_INDEX_NOTUNIQUE, array('topicname'));

        if (!$dbman->index_exists($table, $indextopicname)) {
            $dbman->add_index($table, $indextopicname);
        }
        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021032308, 'cardbox');
    }

    if ($oldversion < 2021072102) {

        // Define field context to be added to cardbox_cardcontents.
        $table = new xmldb_table('cardbox_cardcontents');
        $field = new xmldb_field('context', XMLDB_TYPE_TEXT, null, null, null, null, null, 'content');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021072102, 'cardbox');
    }

    if ($oldversion < 2021072600) {

        // Define field context to be added to cardbox_cardcontents.
        $table = new xmldb_table('cardbox_cardcontents');
        $field = new xmldb_field('necessaryanswers', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'context');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021072600, 'cardbox');
    }

    if ($oldversion < 2021072800) {

        // Define field context to be added to cardbox_cardcontents.
        $table = new xmldb_table('cardbox');
        $field = new xmldb_field('necessaryanswers', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '-1', 'autocorrection');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021072800, 'cardbox');
    }

    if ($oldversion < 2021072902) {

        // Define field context to be added to cardbox_cardcontents.
        $table = new xmldb_table('cardbox_cardcontents');
        $field = new xmldb_field('area', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'contenttype');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021072902, 'cardbox');
    }

    if ($oldversion < 2021073003) {

        // Define field context to be dropped from cardbox_cardcontents.
        $table = new xmldb_table('cardbox_cardcontents');
        $field = new xmldb_field('necessaryanswers', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define default value to be changed.
        $table = new xmldb_table('cardbox');
        $field = new xmldb_field('necessaryanswers', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'autocorrection');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
        }

        // Define field necessaryanswers to be added to cardbox_cardcontents.
        $table = new xmldb_table('cardbox_cards');
        $field = new xmldb_field('necessaryanswers', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'approvedby');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021073003, 'cardbox');
    }

    if ($oldversion < 2021080201) {

        // Define field necessaryanswerseditable to be added to cardbox.
        $table = new xmldb_table('cardbox');
        $field = new xmldb_field('necessaryanswerslocked', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'necessaryanswers');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021080201, 'cardbox');
    }

    if ($oldversion < 2021080400) {

        // Define field necessaryanswerseditable to be added to cardbox.
        $table = new xmldb_table('cardbox');
        $field = new xmldb_field('casesensitive', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'necessaryanswerslocked');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021080400, 'cardbox');
    }

    if ($oldversion < 2021090102) {

        // Define field context to be dropped from cardbox_cardcontents.
        $table = new xmldb_table('cardbox_cardcontents');
        $field = new xmldb_field('necessaryanswers');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field context to be dropped from cardbox_cardcontents.
        $table = new xmldb_table('cardbox_cardcontents');
        $field = new xmldb_field('context');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021090102, 'cardbox');
    }

    if ($oldversion < 2021090600) {

        // Replace old contenttypes table with new DEFINEs.
        $contenttypeimage = $DB->get_field('cardbox_contenttypes', 'id', ['name' => 'image'], MUST_EXIST);
        $contenttypetext = $DB->get_field('cardbox_contenttypes', 'id', ['name' => 'text'], MUST_EXIST);
        $contenttypeaudio = $DB->get_field('cardbox_contenttypes', 'id', ['name' => 'audio'], MUST_EXIST);
        $DB->execute("UPDATE {cardbox_cardcontents} SET contenttype = :newvalue WHERE contenttype = :oldvalue",
            ['oldvalue' => $contenttypeimage, 'newvalue' => CARDBOX_CONTENTTYPE_IMAGE]);
        $DB->execute("UPDATE {cardbox_cardcontents} SET contenttype = :newvalue WHERE contenttype = :oldvalue",
            ['oldvalue' => $contenttypetext, 'newvalue' => CARDBOX_CONTENTTYPE_TEXT]);
        $DB->execute("UPDATE {cardbox_cardcontents} SET contenttype = :newvalue WHERE contenttype = :oldvalue",
            ['oldvalue' => $contenttypeaudio, 'newvalue' => CARDBOX_CONTENTTYPE_AUDIO]);

        // Define table cardbox_contenttypes to be created.
        $table = new xmldb_table('cardbox_contenttypes');

        // Conditionally launch drop table for cardbox_contenttypes.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021090600, 'cardbox');
    }

    if ($oldversion < 2021100101) {

        // Define field numberofcards to be added to cardbox_statistics.
        $table = new xmldb_table('cardbox_statistics');
        $field = new xmldb_field('numberofcards', XMLDB_TYPE_INTEGER, '4', null, null, null, null, 'timeofpractice');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field duration to be added to cardbox_statistics.
        $table = new xmldb_table('cardbox_statistics');
        $field = new xmldb_field('duration', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'numberofcards');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021100101, 'cardbox');
    }

    if ($oldversion < 2021101400) {
        $table = new xmldb_table('cardbox_progress');
        $index = new xmldb_index('card_idx', XMLDB_INDEX_NOTUNIQUE, ['card']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_mod_savepoint(true, 2021101400, 'cardbox');
    }

    // Remove unused index and add new indices to cardbox_statistics.
    if ($oldversion < 2021102701) {
        $table = new xmldb_table('cardbox_statistics');

        // Remove (id, userid) index.
        $index = new xmldb_index('userid_userid_idx', XMLDB_INDEX_NOTUNIQUE, ['id', 'userid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Add (userid, cardboxid) index.
        $index = new xmldb_index('userid_cardboxid_idx', XMLDB_INDEX_NOTUNIQUE, ['userid', 'cardboxid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Add (cardboxid) index.
        $index = new xmldb_index('userid_cardboxid_idx', XMLDB_INDEX_NOTUNIQUE, ['cardboxid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_mod_savepoint(true, 2021102701, 'cardbox');
    }

    if ($oldversion < 2021111900) {
        global $DB;
        $sql = "UPDATE {cardbox_cardcontents} SET contenttype = 1 WHERE contenttype = 2 and cardside = 1";
        $DB->execute($sql);
        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021111900, 'cardbox');
    }

    if ($oldversion < 2022060100) {
        global $DB;

        $table = new xmldb_table('cardbox_cards');
        $field = new xmldb_field('disableautocorrect', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'necessaryanswers');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $sql = "UPDATE {cardbox_cards} SET disableautocorrect = 0 WHERE disableautocorrect IS NULL";
        $DB->execute($sql);

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2022060100, 'cardbox');
    }

    if ($oldversion < 2023052401) {

        // Define field autocorrection to be added to cardbox.
        $table = new xmldb_table('cardbox');
        $field = new xmldb_field('enablenotifications', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'introformat');

        // Conditionally launch add field autocorrection.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2023052401, 'cardbox');
    }

    // Enable notifications for current cardboxes
    if ($oldversion < 2023052402) {
        global $DB;

        $sql = "UPDATE {cardbox} SET enablenotifications = 1";
        $DB->execute($sql);

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2023052402, 'cardbox');
    }

    return true;

}