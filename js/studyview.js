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
function startPractice(Y, __cmid, __selection, __boxcount, __selfchecking) { // Wrapper function that is called by controller.php

    require(['jquery', 'core/templates', 'core/notification', 'chartjs'], function ($, templates, notification, chart) {

        var cardcount = __selection.length; // to be used for statistics/progress bar.
        // Information about the current flashcard.
        var position = 0;
        var cardId = __selection[0];
        var isrepetition = 0;
        // 
        var countright = 0;
        var countwrong = 0;        
        var toRepeat = []; // Collects cards that were answered wrongly. They will be repeated but their status in the DB won't change.

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

            var willBeRepetition = 0;
            var next;
            
            // This was the last card of this practice session.
            if (position == (cardcount-1) && toRepeat.length === 0) {
                next = 0;

            // There are only regular cards left.
            } else if (position < (cardcount-1) && toRepeat.length === 0) {
                next = __selection[position+1];
            
            // There are only cards left that are to be repeated.
            } else if (position == (cardcount-1) && toRepeat.length !== 0) {
                next = toRepeat.shift();
                willBeRepetition = 1;
                
            // There are both regular cards and cards to be repeated left.
            } else {
                if (getRandomInt(3) < 2) {
                    next = __selection[position+1];
                } else {
                    next = toRepeat.shift();
                    willBeRepetition = 1;
                }
            }

            $.ajax({
                type: 'POST',
                url: 'action.php',
                data: {id: __cmid, action: 'updateandnext', cardid: __selection[position], iscorrect: iscorrect, next: next, isrepetition: isrepetition, sesskey: M.cfg.sesskey},
                success: function(result){
                    result = JSON.parse(result);

                    /********* Deal with the current/old card. *********/
                    
                    // Regular cards:
                    if (isrepetition == 0) {
                        
                        // Adjust the card counts of the boxes.
                        var boxslot = result.lastposition;
                    
                        __boxcount[boxslot]--;

                        if (iscorrect === 1) {
                            countright++;
                            boxslot++;
                            __boxcount[boxslot]++;

                        } else {
                            countwrong++;
                            __boxcount[1]++;
                            // If a wrong answer was given, mark this card for repetition.
                            //toRepeat.push(__selection[position]);
                            toRepeat.push(cardId);
                        }
                    
                    // Cards that are repeated because they were answered wrongly before:
                    // If it was answered wrongly again:
                    } else if (iscorrect == 0) {
                        // Mark the card for repetition once more.
                        toRepeat.push(cardId);
                        
                    }
                    
                    /********* Deal with the new card. *********/
                    if (next == 0) {
                        finishPractice();

                    } else {

                        isrepetition = willBeRepetition;
                        renderNewCard(result.newdata, next);
                        
                    }

                }
            });

        }

        function getRandomInt(max) {
            return Math.floor(Math.random() * (max));
        }

        /**
         * Function rerenders the template with the question data of a new flashcard.
         *
         * @param {type} newdata
         * @returns {undefined}
         */
        function renderNewCard(newdata, next) {

            if (isrepetition === 0) {
                position = position + 1;
                cardId = __selection[position];
            } else {
                cardId = next;
            }

            (function (templates, data) {
                        templates.render('mod_cardbox/studyview', data)
                                .then(function (html, js) {
                                    templates.replaceNodeContents('#cardbox-studyview', html, js);

                                }).then(function () {
                                        registerEventListeners();

                                }); // Add a catch.
            })(templates, newdata);

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

                // These labels appear in the legend and in the tooltips when hovering different arcs.
                labels: [
                    M.util.get_string('right', 'cardbox'),
                    M.util.get_string('wrong', 'cardbox')
                ]
            };
            
            var myDoughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: chartdata,
                options: {
                    title: {
                        display: true,
                        text: M.util.get_string('titleprogresschart', 'cardbox'),
                        fontSize: 16,
                        position: 'top'
                    },
                    legend: {
                        position: 'bottom'
                    },
                    rotation: 1 * Math.PI,
                    circumference: 1 * Math.PI,
                    cutoutPercentage: 60
                }
            });

//            myDoughnutChart.classList.remove('chartjs-render-monitor');
            
            
            var ctx2 = document.getElementById("cardbox-overall-status").getContext("2d");
            
            var boxlabel = M.util.get_string('box', 'cardbox');
            
            var cardboxdata = {
                
                // These labels appear in the legend and in the tooltips when hovering different arcs.
                labels: [
                    M.util.get_string('new', 'cardbox'),
                    boxlabel + ' 1',
                    boxlabel + ' 2',
                    boxlabel + ' 3',
                    boxlabel + ' 4',
                    boxlabel + ' 5'
                ],

                datasets: [{
                    label: M.util.get_string('flashcards', 'cardbox'),
//                    data: [countnew, countboxone, countboxtwo, countboxthree, countboxfour, countboxfive],
                    data: [__boxcount[0], __boxcount[1], __boxcount[2], __boxcount[3], __boxcount[4], __boxcount[5]],
                    backgroundColor: '#0066ff'
                }]

            };

            var myBarChart = new Chart(ctx2, {
                type: 'bar',
                data: cardboxdata,
                options: {
                    title: {
                        display: true,
                        text: M.util.get_string('titleoverviewchart', 'cardbox'),
                        fontSize: 16,
                        position: 'top'
                    },
                    legend: {
                        position: 'bottom'
                    }//,
//                    barPercentage: 1,
//                    categoryPercentage: 1
                }
            });
            
            
        }

        

    });

}