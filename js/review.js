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
 * This script controlls the behaviour of the page during the review process.
 * In this process, the teacher checks whether the student provided content is
 * correct and should be included in the collection of flashcards.
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * 
 * @param {type} Y
 * @param int __cmid
 * @param int[] __cardlist
 * @param int __cardid
 * @returns {undefined}
 */
function startReview(Y, __cmid) {

    require(['jquery', 'core/templates', 'core/notification'], function ($, templates, notification) {
        
        registerEventListeners();

        function registerEventListeners() {
            
            const editbtns = document.querySelectorAll('#cardbox-review .cardbox-review-button');
            editbtns.forEach(btn => {
                const card = btn.closest('#cardbox-card-in-review');
                const cardid = card.getAttribute('data-cardid');
                btn.addEventListener('click', e => {
                    edit(cardid);
                });
            });
            const checkboxes = document.querySelectorAll('#cardbox-review input[type="checkbox"]');
            checkboxes.forEach(c => {
                c.addEventListener('click', e => {
                    var checked = document.querySelectorAll('#cardbox-review input:checked');
                    if (checked.length === 0) {
                        // there are no checked checkboxes
                        document.getElementById('review-div').style.display = 'none';
                    } else {
                        // there are some checked checkboxes
                        document.getElementById('review-div').style.display = 'block';
                    }
                });
              });
        }
        
        function edit(card) {
            openCardFormForEditing(card);
        }
        
        function openCardFormForEditing(cardinreview) {
            var goTo = window.location.pathname + '?id=' + __cmid + '&action=editcard&cardid=' + cardinreview;
            window.location.href = goTo;
        }
        
        
    });
}
function rejectcard(Y, __cmid, cardlist, countcard) {
    require(['jquery', 'core/notification'], function ($, notification) {
    notification.confirm(M.util.get_string('rejectcard','cardbox'),M.util.get_string('rejectcardinfo','cardbox', countcard),M.util.get_string('yes', 'cardbox'), M.util.get_string('cancel', 'cardbox'),function () {
        /* return */ $.ajax({
            type: "POST",
            url: "controller.php",
            data: { "action": 'rejectcard', sesskey: M.cfg.sesskey}
        }).then(function(){
            console.log(cardlist.join());
            var goTo = window.location.pathname + '?id=' + __cmid + '&action=rejectcard&cardid=' + cardlist.join() ;
            window.location.href = goTo;
        }); 
    });
});

}