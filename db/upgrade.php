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

    return true;

}