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
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function startOptions(Y, __cmid) {
    
    require(['jquery'], function ($) {
        
        var optionsbutton = document.getElementById('cardbox-see-options');
        optionsbutton.click(); // XXX not working.
        
        document.getElementById('cardbox-apply-settings').addEventListener('click', function(e) {
            e.preventDefault();
            applySettings();
        });
        
        function applySettings() {// XXX maybe just add an action param to the form in the template.

            var topic = document.getElementById('cardbox-topic').value;
            var correctionmode;

            var radios = document.getElementById('cardbox-form').elements['correctionmode'];

            for (var i=0, len=radios.length; i<len; i++) {
                if ( radios[i].checked ) {
                    correctionmode = radios[i].value;
                    break;
                }
            }

            var goTo = window.location.pathname + '?id=' + __cmid + '&action=practice&start=true&mode=' + correctionmode + '&topic=' + topic;
            window.location.href = goTo;

        }
        
        
    });
    
    
    
}