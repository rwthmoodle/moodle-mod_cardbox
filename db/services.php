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
 * Web service local plugin cardbox external functions and service definitions.
 *
 * @package    mod_cardbox
 * @copyright  2015 Caio Bressan Doneda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(

    'mod_cardbox_deletetopic' => array(
        'classname'    => 'mod_cardbox_external',
        'methodname'   => 'deletetopic',
        'classpath'    => 'mod/cardbox/externallib.php',
        'description'  => 'Delete topic in a cardbox instance.',
        'type'         => 'write',
        'ajax'         => true,
        'capabilities' => 'mod/cardbox:edittopics'
    ),
    'mod_cardbox_renametopic' => array(
        'classname'    => 'mod_cardbox_external',
        'methodname'   => 'renametopic',
        'classpath'    => 'mod/cardbox/externallib.php',
        'description'  => 'Rename topic in a cardbox instance.',
        'type'         => 'write',
        'ajax'         => true,
        'capabilities' => 'mod/cardbox:edittopics'
    ),
);
