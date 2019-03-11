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
 * @authors   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 * @param {type} Y required by moodle
 * @param int __cmid course module id
 * @param array __selection ids of those cards selected for practice
 * @returns {undefined}
 */
function startPractice(Y, __cmid, __selection, __selfchecking) { // Wrapper function that is called by controller.php

    require(['jquery', 'core/templates', 'core/notification', 'chartjs'], function ($, templates, notification, chart) {

        var position = 0;
        var cardcount = __selection.length; // to be used for statistics/progress bar.
        var countright = 0;
        var countwrong = 0;
//        var answeredCorrectly = 0;
//        var toRepeat = array();

        console.log('__selection: ', __selection);

        registerEventListeners();
        
        
        function registerEventListeners() {

            document.getElementById('cardbox-submit-answer').addEventListener('click', function(e) {
                e.preventDefault();
                flipCard();
            });

            document.getElementById('cardbox-mark-as-correct').addEventListener('click', function(e) {
                e.preventDefault();
                proceed(1);
            });

            document.getElementById('cardbox-mark-as-incorrect').addEventListener('click', function(e) {
                e.preventDefault();
                proceed(0);
            });
        }

        function flipCard() {    

            // 1. Hide the question and the send button and display the answer instead.
            $('.cardbox-image').toggleClass('hidden');
            $('.cardbox-text').toggleClass('hidden');
            $('#cardbox-userinput').toggleClass('hidden'); // TODO: In automatic check, the input should be displayed, albeit not in an input field.
            $('#cardbox-submit-answer').toggleClass('hidden');

            // 2. Check answer or let user check their answer
            if (__selfchecking) {
                selfCheck();

            } else {
                check();
            }

        }

        function selfCheck() {
            $('#cardbox-mark-as-correct').toggleClass('hidden');
            $('#cardbox-mark-as-incorrect').toggleClass('hidden');
            
        }

        function check() {
            
        }

        /**
         * Function initiates update of the progress status of the current card
         * and then renders the next card or wraps up the practice session.
         *
         * @param {type} iscorrect
         * @returns {undefined}
         */
        function proceed(iscorrect) { // XXX: Error notifications for error cases AND collect wrong cards for repetition.

            if (iscorrect == 1) {
                countright++;
            } else {
                countwrong++;
            }
            if (position == (cardcount-1)) {
                var next = 0; // This was the last card of this practice session.
            } else {
                var next = __selection[position+1];
            }

            $.ajax({
                type: 'POST',
                url: 'action.php',
                data: {id: __cmid, action: 'updateandnext', cardid: __selection[position], iscorrect: iscorrect, next: next, sesskey: M.cfg.sesskey},
                success: function(result){

                    if (next == 0) {
                        finishPractice();

                    } else {
                        result = JSON.parse(result);
                        renderNewCard(result.newdata);
                    }

                }
            });

        }

        /**
         * Function tells the user that the session is finished.
         *
         * @returns {undefined}
         */
        function finishPractice() {
            
            // 1. Give the user feedback.
//            notification.addNotification({
//                message: M.util.get_string('sessioncompleted', 'cardbox'),
//                type: "success"
//            });

            // 2. Hide the action buttons.
            $('.cardbox-back').toggleClass('hidden');
            $('#cardbox-mark-as-correct').toggleClass('hidden');
            $('#cardbox-mark-as-incorrect').toggleClass('hidden');
            //$('.btn btn-primary').toggleClass('hidden');
            
            // 3. Display progress as doughnut chart.
            var ctx = document.getElementById("cardbox-practice-feedback").getContext("2d");
            
            var chartdata = {
                datasets: [{
                    label: 'Progress',
                    data: [countright, countwrong],
                    backgroundColor: [
                        '#00b33c',
                        '#ff9900'
                    ]
                }],

                // These labels appear in the legend and in the tooltips when hovering different arcs
                labels: [
                    'correct',
                    'incorrect'
                ]
            };
            
            var myDoughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: chartdata,
                //options: options
                options: {
                    rotation: 1 * Math.PI,
                    circumference: 1 * Math.PI
                }
            });

            myDoughnutChart.classList.remove('chartjs-render-monitor');
            
        }

        /**
         * Function rerenders the template with the question data of a new flashcard.
         *
         * @param {type} newdata
         * @returns {undefined}
         */
        function renderNewCard(newdata) {

            position = position + 1;

            (function (templates, data) {
                        templates.render('mod_cardbox/studyview', data)
                                .then(function (html, js) {
                                    templates.replaceNodeContents('#cardbox-studyview', html, js);

                                }).then(function () {
                                        registerEventListeners();

                                }); // Add a catch.
            })(templates, newdata);

        }

    });

}