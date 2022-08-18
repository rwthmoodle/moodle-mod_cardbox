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

/**
 * This script controlls the behaviour of the overview page
 *
 * @param {type} Y
 * @param {type} __cmid
 * @param {type} __topic
 * @returns {undefined}
 */
function startOverview(Y, __cmid, __topic) { // Wrapper function that is called by controller.php.

    require(['jquery', 'core/notification'], function ($, notification) {
        var topicfilter = document.getElementById('cardbox-overview-topicfilter');
        topicfilter.onchange = function() {

            var select = this.options[this.selectedIndex];        
            var topicid = select['value'];
            window.location.href = window.location.pathname + '?id=' + __cmid + '&action=overview&topic=' + topicid;

        }
        const editbtns = document.querySelectorAll('#cardbox-overview .cardbox-overview-button-edit');
        editbtns.forEach(btn => {
            const card = btn.closest('#cardbox-card-in-overview');
            const cardid = card.getAttribute('data-cardid');
            btn.addEventListener('click', e => {
                edit(cardid);
            });
        });

        $('.cardbox-delete-button').each(function (i, button) {
            let id = button.id.split('-');
            let cardid = id[2];
            $('#' + button.id).click(function () {
                deleteCard(cardid);
            });
        });

        function edit(card) {
            openCardFormForEditing(card);
        }
        
        function openCardFormForEditing(cardinoverview) {
            var goTo = window.location.pathname + '?id=' + __cmid + '&action=editcard&cardid=' + cardinoverview + '&from=overview';
            window.location.href = goTo;
        }
         

        function deleteCard(cardid) {
            notification.confirm(M.util.get_string('deletecard','cardbox'),M.util.get_string('deletecardinfo','cardbox'),M.util.get_string('yes', 'cardbox'), M.util.get_string('cancel', 'cardbox'),function () {
                window.location.href = window.location.pathname + '?id=' + __cmid + '&action=deletecard&cardid=' + cardid + '&sesskey=' + M.cfg.sesskey;
            }); 

        }
    });

    
}