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
        $index_cardboxid = new xmldb_index('cardboxid_idx', XMLDB_INDEX_NOTUNIQUE, array('cardboxid'));

        // Conditionally launch add index course_idx.
        if (!$dbman->index_exists($table, $index_cardboxid)) {
            $dbman->add_index($table, $index_cardboxid);
        }
        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021032302, 'cardbox');
    }

    if ($oldversion < 2021032303) {
        
        $table_cards = new xmldb_table('cardbox_cards');
        $index_cardboxid = new xmldb_index('cardboxid_idx', XMLDB_INDEX_NOTUNIQUE, array('cardbox'));
        $index_topic = new xmldb_index('topic_idx', XMLDB_INDEX_NOTUNIQUE, array('topic'));
        $index_cardboxapproved = new xmldb_index('cardboxapproved_idx', XMLDB_INDEX_NOTUNIQUE, array('cardbox', 'approved'));
        // Adding indexes to table cardbox_cards
        if (!$dbman->index_exists($table_cards, $index_cardboxid)) {
            $dbman->add_index($table_cards, $index_cardboxid);
        }
        if (!$dbman->index_exists($table_cards, $index_topic)) {
            $dbman->add_index($table_cards, $index_topic);
        }
        if (!$dbman->index_exists($table_cards, $index_cardboxapproved)) {
            $dbman->add_index($table_cards, $index_cardboxapproved);
        }
        upgrade_mod_savepoint(true, 2021032303, 'cardbox');
    } 
    
    if ($oldversion < 2021032304) {

        $table_progress = new xmldb_table('cardbox_progress');
        $index_userid_card = new xmldb_index('cardboxid_idx', XMLDB_INDEX_NOTUNIQUE, array('userid', 'card'));
        $index_cardposition = new xmldb_index('cardboxapproved_idx', XMLDB_INDEX_NOTUNIQUE, array('cardposition'));
        // Adding indexes to table cardbox_progress
        if (!$dbman->index_exists($table_progress, $index_userid_card)) {
            $dbman->add_index($table_progress, $index_userid_card);
        }
        if (!$dbman->index_exists($table_progress, $index_cardposition)) {
            $dbman->add_index($table_progress, $index_cardposition);
        }
        upgrade_mod_savepoint(true, 2021032304, 'cardbox');
    }
    
    if ($oldversion < 2021032305) {
        $table_cardcontents = new xmldb_table('cardbox_cardcontents');
        $index_card_contenttype = new xmldb_index('card_contenttype_idx', XMLDB_INDEX_NOTUNIQUE, array('card', 'contenttype'));
        $index_card = new xmldb_index('card_idx', XMLDB_INDEX_NOTUNIQUE, array('card'));
        $index_cardside = new xmldb_index('cardside_idx', XMLDB_INDEX_NOTUNIQUE, array('cardside'));
        $index_contenttype = new xmldb_index('contenttype_idx', XMLDB_INDEX_NOTUNIQUE, array('contenttype'));
        // Adding indexes to table cardbox_cardcontents
        if (!$dbman->index_exists($table_cardcontents, $index_card_contenttype)) {
            $dbman->add_index($table_cardcontents, $index_card_contenttype);
        }
        if (!$dbman->index_exists($table_cardcontents, $index_card)) {
            $dbman->add_index($table_cardcontents, $index_card);
        }
        if (!$dbman->index_exists($table_cardcontents, $index_cardside)) {
            $dbman->add_index($table_cardcontents, $index_cardside);
        }
        if (!$dbman->index_exists($table_cardcontents, $index_contenttype)) {
            $dbman->add_index($table_cardcontents, $index_contenttype);
        }
        upgrade_mod_savepoint(true, 2021032305, 'cardbox');
    }

    if ($oldversion < 2021032306) {
        $table_contenttypes = new xmldb_table('cardbox_contenttypes');
        
        $index_contentname = new xmldb_index('contentname_idx', XMLDB_INDEX_NOTUNIQUE, array('name'));
        $index_types = new xmldb_index('types_idx', XMLDB_INDEX_NOTUNIQUE, array('type'));

        // Adding indexes to table cardbox_contenttypes;
        if (!$dbman->index_exists($table_contenttypes, $index_contentname)) {
            $dbman->add_index($table_contenttypes, $index_contentname);
        }
        if (!$dbman->index_exists($table_contenttypes, $index_types)) {
            $dbman->add_index($table_contenttypes, $index_types);
        }
        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021032306, 'cardbox');
    }
        
    if ($oldversion < 2021032307) {
        $table_statistics = new xmldb_table('cardbox_statistics');

        $index_userid_userid = new xmldb_index('userid_userid_idx', XMLDB_INDEX_NOTUNIQUE, array('id', 'userid'));
        // Adding indexes to table cardbox_statistics
        if (!$dbman->index_exists($table_statistics, $index_userid_userid)) {
            $dbman->add_index($table_statistics, $index_userid_userid);
        }
    
        upgrade_mod_savepoint(true, 2021032307, 'cardbox');
    } 

    if ($oldversion < 2021032308) {

        $table = new xmldb_table('cardbox_topics');
        $index_topicname = new xmldb_index('topicname_idx', XMLDB_INDEX_NOTUNIQUE, array('topicname'));

        if (!$dbman->index_exists($table, $index_topicname)) {
            $dbman->add_index($table, $index_topicname);
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

    if ($oldversion < 2021090101) {

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
        upgrade_mod_savepoint(true, 2021090101, 'cardbox');
    }

    if ($oldversion < 2021090102) {

        // Define default value to be changed.
        $table = new xmldb_table('cardbox');
        $field = new xmldb_field('necessaryanswers', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'autocorrection');

        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
        }

        // Cardbox savepoint reached.
        upgrade_mod_savepoint(true, 2021090102, 'cardbox');
    }

    return true;

}