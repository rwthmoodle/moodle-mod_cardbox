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

function startOptions(Y, __cmid, __openmodal) {

    require(['jquery'], function ($) {

        var modal = document.getElementById('cardboxPracticeSettings');
        
        if (__openmodal) {
            modal.classList.add('show');
            modal.classList.add('modal-open');
            modal.style.display = 'block';
        }

        document.getElementById('cardbox-onlyonetopic').addEventListener('change', function(e) {
            if (document.getElementById('cardbox-onlyonetopic').value!=-1) {
                document.getElementById('cardbox-topic-select').style.display = 'none';
                document.getElementById('cardbox-topic-description').style.display = 'none';
            } else {
                document.getElementById('cardbox-topic-select').style.display = 'flex';
                document.getElementById('cardbox-topic-description').style.display = 'flex';
                document.getElementById('cardbox-onlyonetopic-select').style.marginBottom = '2em';
                document.getElementById('cardbox-onlyonetopic-choices').style.marginBottom = '2em';
            }
        });

        document.getElementById('cardbox-apply-settings').addEventListener('click', function(e) {
            e.preventDefault();
            applySettings();
        });

        document.getElementById('cardbox-cancel-settings').addEventListener('click', function(e) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        });
        
        document.getElementById('cardbox-close-settings').addEventListener('click', function(e) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        });

        document.getElementById('cardbox-see-options').addEventListener('click', function(e) {
            document.getElementById('cardbox-practiceall-select').style.display = 'none';
            document.getElementById('cardbox-practiceall-choices').style.display = 'none';
            document.getElementById('cardbox-practiceall-yes').checked = true;
        });

        // If the user clicks anywhere outside of the modal, close it.
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.classList.remove('show');
                modal.style.display = "none";
            }
        }

        function applySettings() {
            
            var topic = document.getElementById('cardbox-topic').value;
            var practiceall = document.getElementById('cardbox-practiceall-yes').checked;
            var onlyonetopic = document.getElementById('cardbox-onlyonetopic').value;
            var amountcards = document.getElementById('cardbox-amountcards').value;
            var correctionmode;

            var radios = document.getElementById('cardbox-form').elements['correctionmode'];

            for (var i=0, len=radios.length; i<len; i++) {
                if ( radios[i].checked ) {
                    correctionmode = radios[i].value;
                    break;
                }
            }


            var goTo = window.location.pathname + '?id=' + __cmid + '&action=practice&start=true&mode=' + correctionmode + '&topic=' + topic +'&practiceall=' + practiceall +'&onlyonetopic=' + onlyonetopic +'&amountcards=' + amountcards;
            window.location.href = goTo;

        }

    });

}