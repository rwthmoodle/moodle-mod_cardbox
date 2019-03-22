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
 * This script controlls the behaviour of the page during practice.
 *
 * @param {type} Y required by moodle
 * @param int __cmid course module id
 * @param array __selection ids of those cards selected for practice
 * @param int specifies whether the practice mode is auto- or selfcheck and whether a question or answer is shown.
 * @returns {undefined}
 */
function startPractice(Y, __cmid, __selection, __boxcount, __correction, __case, __data) { // Wrapper function that is called by controller.php

    require(['jquery', 'core/templates', 'core/notification', 'chartjs'], function ($, templates, notification, chart) {

        /*********** 1. Variables and Calls ***********/
        
        var cardcount = __selection.length; // to be used for statistics/progress bar.

        // Information about the current flashcard.
        var position = 0;
        var cardId = __selection[0];
        var isrepetition = 0;
        
        // Information about the user's answer(s) for the currect flashcard.
        var userinput;
        var answeriscorrect = 0;
        var answeriscomplete = 0;
        var numberofmistakes = 0; // for one card.
        var missinganswers = 0;

        // Statistical information that will be displayed to the user at the end of practice.
        var countright = 0; // for all cards of this session.
        var countwrong = 0;

        // Collection of cards that were answered wrongly. They will be repeated until answered correctly once.
        // Their status in the DB won't change, however, i.e. they go back to the first box.
        var toRepeat = [];

        addQuestionEvents();
        
        document.getElementById('cardbox-apply-settings').addEventListener('click', function(e) {
            e.preventDefault();
            applySettings();
        });

        /*********** 2. Definitions ***********/

        function addQuestionEvents() {

            if ( (__case % 2) == 0) { // automatic check.

                document.getElementById('cardbox-submit-answer').addEventListener('click', function(e) {

                    // 0. Prevent page reload.
                    e.preventDefault();

                    // 1. Check whether the answer is correct and complete and add it to the templatable data.
                    checkAnswer();

                    // 2. render solution
                    renderSolutionAutoCheck();

                    // 3. Insert feedback (depending on 1.) and the user's solution.
                    giveFeedback();

                });

                document.getElementById('cardbox-do-not-know').addEventListener('click', function(e) {
                    e.preventDefault();
                    // check answer
                    // render solution
                    renderSolutionAutoCheck();
                    // mark as incorrect --> eventlisteners for next step
                });

            } else { // self-check.

                document.getElementById('cardbox-check-answer').addEventListener('click', function(e) {
                    e.preventDefault();
                    renderSolutionForSelfCheck();
                });

            }

        }

        /**
         * This function adds event listeners to the buttons of the solution view.
         *
         * @returns {undefined}
         */
        function addAnswerEvents() {

            if ( (__case % 2) == 0) { // automatic check.
          
                // Button overrides the result of the automatic check, tells the server and requests a new flashcard to render.
                document.getElementById('cardbox-override').addEventListener('click', function(e) {
                    e.preventDefault();
                    if ( (answeriscorrect === 1) && (answeriscomplete === 1) ) {
                        proceed(0);
                    } else {
                        proceed(1);
                    }
                });
                
                // Button sends the result of the automatic check to the server and requests a new flashcard to render.
                document.getElementById('cardbox-proceed').addEventListener('click', function(e) {
                    e.preventDefault();
                    removeNotifications();
                    if ( (answeriscorrect === 1) && (answeriscomplete === 1) ) {
                        proceed(1);
                    } else {
                        proceed(0);
                    }
                    
                });

            } else { // self check.
                
                // Button tells the server that the current card was answered correctly and requests a new flashcard.
                document.getElementById('cardbox-mark-as-correct').addEventListener('click', function(e) {
                    e.preventDefault();
                    proceed(1);
                });
                // Button tells the server that the current card was answered incorrectly and requests a new flashcard.
                document.getElementById('cardbox-mark-as-incorrect').addEventListener('click', function(e) {
                    e.preventDefault();
                    proceed(0);
                });
            }
                
        }

        /**
         * Function removes any green/red feedback from the top of the page.
         *
         * @returns {undefined}
         */
        function removeNotifications() {
            let notificationpanel = document.getElementById("user-notifications");
                while (notificationpanel.hasChildNodes()) {  
                    notificationpanel.removeChild(notificationpanel.firstChild);
            } 
        }


        function applySettings() {

            var topic = document.getElementById('cardbox-topic').value;
            var correctionmode;

            var radios = document.getElementById('cardbox-form').elements['correctionmode'];

            for (var i=0, len=radios.length; i<len; i++) {
                if ( radios[i].checked ) {
                    correctionmode = radios[i].value;
                    break;
                }
            }

            var goTo = window.location.pathname + '?id=' + __cmid + '&action=practice&correction=' + correctionmode + '&topic=' + topic;
            window.location.href = goTo;

        }
        /**
         * 
         * @returns {undefined}
         */
        function checkAnswer() {

            // Reset everything.
            answeriscorrect = 1;
            answeriscomplete = 0;
            numberofmistakes = 0;
            missinganswers = 0;

            var solutions = __data.answer.texts;
            userinput = [];
            var matches = [];
            var answers = [];

            // 1. Collect the user's answers in an array.
            var i;
            for (i = 1; i <= solutions.length; i++) {
                (function (innerI){
                    var ui = document.getElementById('cardbox-userinput-' + innerI).value;
                    if (ui !== '') {
                        userinput.push(ui);
                    }

                })(i);
            }

            // 2. For each solution: Check whether it is among the user's answers and collect the matches.
            solutions.forEach(check);

            // 3. Collect matches and non-matches and transform them into a displayable form. Also determine whether there are incorrect answers.
            userinput.forEach(collect);
            __data['userinputitems'] = answers;

            // 4. Check whether there are as many answers as solutions.
            if (solutions.length > matches.length) {
                answeriscomplete = 0;
                missinganswers = solutions.length - matches.length;

            } else {
                answeriscomplete = 1;
            }

            /**
             * This function takes each solution and checks whether it contains
             * one of the user's answers or is contained in one of the user's
             * answers.
             * 
             * @param {type} solutionitem
             * @param {type} index
             * @returns {undefined}
             */
            function check(solutionitem, index) {

                solutionitem = solutionitem.text;
                
                var j;
                var userinputitem;
                for (j = 0; j < userinput.length; j++) {
                    (function (innerI){
                        
                        userinputitem = userinput[innerI];
                        if (compare(solutionitem, userinputitem)) {

                                if ( matches.indexOf(userinputitem) === -1 ) {
                                    
                                    matches.push(userinputitem);
                                }

                        }

                    })(j);
                }

            }
            /**
             * Function returns true if one of the strings is contained within the other
             * ('or' identical).
             *
             * @param string a
             * @param string b
             * @returns {Boolean}
             */
            function compare(a, b) {

                a = a.toLowerCase();
                b = b.toLowerCase();

                if ( (a.includes(b)) || (b.includes(a)) ) {
                    return true;
                }
                return false;
   
            }
            /**
             * 
             * @param {type} userinput
             * @param {type} index
             * @returns {undefined}
             */
            function collect(userinput, index) {

                if ( matches.indexOf(userinput) != -1 ) {

                    var answer = {
                        userinput: userinput,
                        colorclass: 'cardbox-input-color-correct'
                    };

                } else {
                    // Note that the user made at least one mistake.
                    answeriscorrect = 0;
                    numberofmistakes++;
                    var answer = {
                        userinput: userinput,
                        colorclass: 'cardbox-input-color-incorrect'
                    };
                }

                answers.push(answer);
            }

        }
        /**
         * Function places a green or red feedback notification at the top of the page.
         *
         * @returns {undefined}
         */
        function giveFeedback() {

            if ( (answeriscorrect === 1) && (answeriscomplete === 1) ) {
                
                notification.addNotification({
                    message: M.util.get_string('feedback:correctandcomplete', 'cardbox'),
                    type: "success"
                });

            } else if (answeriscorrect === 1) {
                notification.addNotification({
                    message: M.util.get_string('feedback:incomplete', 'cardbox'),
//                    message: M.util.get_string('feedback:correctbutincomplete', 'cardbox', missinganswers),
                    type: "warning"
                });

            } else {
                notification.addNotification({
                    message: M.util.get_string('feedback:incorrectandpossiblyincomplete', 'cardbox'),
                    type: "error"
                });
                
            }

        }

        /**
         * Function initiates update of the progress status of the current card
         * and then renders the next card or wraps up the practice session.
         *
         * @param {type} iscorrect
         * @returns {undefined}
         */
        function proceed(iscorrect) { // XXX: Error notifications for error cases.

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
                data: {id: __cmid, action: 'updateandnext', case: __case, cardid: __selection[position], iscorrect: iscorrect, next: next, isrepetition: isrepetition, sesskey: M.cfg.sesskey},
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
                        __data = result.newdata;
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
                        templates.render('mod_cardbox/practice', data)
                                .then(function (html, js) {
                                    templates.replaceNodeContents('#cardbox-practice-replacable', html, js); // XXX partial.

                                }).then(function () {
                                        // Reset parameters.
//                                        answeriscorrect = 0;
//                                        answeriscomplete = 0;
//                                        numberofmistakes = 0;
                                        // Add event listeners.
                                        addQuestionEvents();

                                }); // Add a catch.
            })(templates, newdata);

        }

        function renderSolutionForSelfCheck() {

            var newdata = __data;
            newdata['case1'] = false;
            newdata['case3'] = true;
            
            (function (templates, data) {
                        templates.render('mod_cardbox/practice', data)
                                .then(function (html, js) {
                                    templates.replaceNodeContents('#cardbox-practice-replacable', html, js); // XXX partial.

                                }).then(function () {
                                        addAnswerEvents();

                                }); // Add a catch.
            })(templates, newdata);

        }
        /**
         * This function 'flips' the card from question to solution in the self-check mode.
         *
         * @returns {undefined}
         */
        function renderSolutionAutoCheck() {

            // Tell the templatable to display the solution view instead of the question view.
            var newdata = __data;
            newdata['case2'] = false;
            newdata['case4'] = true;

            (function (templates, data) {
                        templates.render('mod_cardbox/practice', data)
                                .then(function (html, js) {
                                    templates.replaceNodeContents('#cardbox-practice-replacable', html, js); // XXX partial.
                                }).then(function () {
                                        addAnswerEvents();

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