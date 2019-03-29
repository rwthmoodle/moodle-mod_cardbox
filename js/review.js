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
function startReview(Y, __cmid, __cardlist, __cardid = 0) { // Wrapper function that is called by controller.php

    require(['jquery', 'core/templates', 'core/notification'], function ($, templates, notification) {

        console.log('__cardlist: ', __cardlist);

        var position;
        var cardinreview;
        var next;

        // Start with a particular card.
        if (__cardid != 0) {
            position = __cardlist.indexOf(''+__cardid);
            cardinreview = __cardid;
            next = __cardlist[position+1];

        } else {
            position = 0;
            cardinreview = __cardlist[0];
            next = __cardlist[1];
        }

        console.log('position: ', position);
        console.log('cardinreview: ', cardinreview);
        console.log('next: ', next);

        // var cardId = document.getElementById('cardbox-card-in-review').dataset.cardid; // XXX über die Liste abfragen?

        registerEventListeners();

        function registerEventListeners() {

            document.getElementById('cardbox-approve').addEventListener('click', function(e) {
                approve();
            });

            document.getElementById('cardbox-edit').addEventListener('click', function(e) {
                edit();
            });

//            document.getElementById('cardbox-reject').addEventListener('click', function(e) {
//                reject();
//            });
            
            document.getElementById('cardbox-skip').addEventListener('click', function(e) {
                skip();
            });
        }
        /**
         * 
         * @param {type} status
         * @returns {undefined}
         */
        function controlUpdate(action) {
            
            $.ajax({
                type: 'POST',
                url: 'action.php',
                data: {id: __cmid, action: 'review', cardid: cardinreview, status: action, nextcard: next, sesskey: M.cfg.sesskey},
                success: function(result){

                    result = JSON.parse(result);
                    
                    if (result.status === 'error') {
                        
                        notification.addNotification({
                                message: result.reason,
                                type: "error"
                        });
                        return;

                    }

                    // Notify the user that the card was successfully approved.
                    if (action != 'skip' ) {
                        
                        notification.addNotification({
                            message: M.util.get_string('success:'+action, 'cardbox'),
                            type: "success"
                        });
                    } 
                    // If there are more cards to review, proceed to the next one.
                    if (result.finished == 0) {
                        
                        updateStatus();
                        renderNewCard(result.newdata);
                    
                    // Else clean up.
                    } else {

                        var view = document.getElementById('cardbox-review');
                        view.remove();

                        var info = "<span class='notification'><div class='alert alert-info alert-block fade in' role='alert'>" + M.util.get_string('info:waslastcardforreview', 'cardbox') + "</div></span>";
                        var headline = document.getElementById('cardbox-review-headline');
                        headline.parentNode.insertAdjacentHTML('afterend', info);

                    }

                    // Remove the notification box after 3 seconds.
                    setTimeout(function(){
                        let notificationpanel = document.getElementById("user-notifications");
                        while (notificationpanel.hasChildNodes()) {  
                            notificationpanel.removeChild(notificationpanel.firstChild);
                        } 
                    }, 3000);

                }
            });
            
        }
        /**
         * Function tracks the review process and selects the next card to be reviewed.
         *
         * @returns {undefined}
         */
        function updateStatus() { // maybe use pop? or shift?
            position++;
            cardinreview = __cardlist[position];
            next = __cardlist[position+1];
        }
        
        /**
         * Function initiates an update of the status of the current card to approved
         * and renders the next card to be reviewed.
         *
         * @returns {undefined}
         */
        function approve() {
            controlUpdate('approve');

        }
        
        function edit() {
            openCardFormForEditing();
        }
        
//        function reject() {
//            controlUpdate('reject');
//        }
        
        function skip() {
            controlUpdate('skip');
        }
        
        function renderNewCard(newdata) {
            (function (templates, data) {
                            templates.render('mod_cardbox/review', data)
                                    .then(function (html, js) {
                                        templates.replaceNodeContents('#cardbox-review', html, js);

                                    }).then(function () {
                                            registerEventListeners(); // XXX unnecessary because buttons didn't disappear?

                                    }); // Add a catch.
            })(templates, newdata);
        
        }
        /**
         * 
         * @returns {undefined}
         */
        function openCardFormForEditing() {
            var goTo = window.location.pathname + '?id=' + __cmid + '&action=editcard&cardid=' + cardinreview;
            window.location.href = goTo;
        }
        
    });

}