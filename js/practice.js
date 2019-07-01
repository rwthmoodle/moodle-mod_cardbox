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
 * This script controlls the behaviour of the page during practice.
 *
 * @param {type} Y required by moodle
 * @param int __cmid course module id
 * @param array __selection ids of those cards selected for practice
 * @param {type} __boxcount
 * @param int __case specifies whether the practice mode is auto- or selfcheck and whether a question or answer is shown.
 * @param {type} __data
 * @returns {undefined}
 */
function startPractice(Y, __cmid, __selection, __boxcount, __case, __data) { // Wrapper function that is called by controller.php

    require(['jquery', 'core/templates', 'core/notification', 'chartjs'], function ($, templates, notification, chart) {

        /*********** 1. Variables and Calls ***********/

        console.log('__selection: ', __selection);
        console.log('__boxcount: ', __boxcount);
        console.log('__case: ', __case);
        console.log('__data: ', __data);


        var vc = new Viewcontroller(__case, templates, __data);


        var cardcount = __selection.length; // to be used for statistics/progress bar.

        // Information about the current flashcard.
        var position = 0;
        var cardId = __selection[0];
        var isrepetition = 0;
        var considercardcorrect = false;

        // Information about the user's answer(s) to the currect flashcard.
        var userinput;
        var answeriscorrect = 0;
        var answeriscomplete = 0;
        var answergiven = 1;

        // Statistical information that will be displayed to the user at the end of practice.
        var countright = 0; // for all cards of this session.
        var countwrong = 0;

        // Collection of cards that were answered wrongly. They will be repeated until answered correctly once.
        // Their status in the database won't change, however, i.e. they go back to the first box.
        var toRepeat = [];

        addQuestionEvents();
        
        var bluebox = document.getElementById('nocardsduenotification');
        if (bluebox !== null) {
            bluebox.parentNode.removeChild(bluebox);
        }

//        document.getElementById('cardbox-apply-settings').addEventListener('click', function(e) {
//            e.preventDefault();
//            applySettings();
//        });

        /*********** 2. Function definitions ***********/

        function addQuestionEvents() {

            if ( (__case % 2) == 0) { // automatic check.

                document.getElementById('cardbox-submit-answer').addEventListener('click', function(e) {

                    // 0. Prevent page reload.
                    e.preventDefault();

                    // 1. Check whether the answer is correct and complete and add it to the templatable data.
                    checkAnswer();

                    // 2. Render solution.
                    renderSolutionAutoCheck();

                    // 3. Insert feedback (depending on 1.) and the user's solution.
//                    giveFeedback();

                });

                document.getElementById('cardbox-do-not-know').addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Render solution
                    considercardcorrect = false;
                    answergiven = 0;
                    answeriscorrect = 0;
                    answeriscomplete = 0;

                    renderSolutionAutoCheck();

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
                    removeNotifications();
                    if ( considercardcorrect ) {
                        proceed(0);
                    } else {
                        proceed(1);
                    }
                });
                
                // Button sends the result of the automatic check to the server and requests a new flashcard to render.
                document.getElementById('cardbox-proceed').addEventListener('click', function(e) {

                    e.preventDefault();
                    removeNotifications();
                    if ( considercardcorrect ) {
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

//        function applySettings() {
//
//            var topic = document.getElementById('cardbox-topic').value;
//            var correctionmode;
//
//            var radios = document.getElementById('cardbox-form').elements['correctionmode'];
//
//            for (var i=0, len=radios.length; i<len; i++) {
//                if ( radios[i].checked ) {
//                    correctionmode = radios[i].value;
//                    break;
//                }
//            }
//
//            var goTo = window.location.pathname + '?id=' + __cmid + '&action=practice&correction=' + correctionmode + '&topic=' + topic;
//            window.location.href = goTo;
//
//        }
        /**
         * 
         * @returns {undefined}
         */
        function checkAnswer() {

            // Reset everything.
            answeriscorrect = 1;
            answeriscomplete = 0;
            answergiven = 1;
            considercardcorrect = false;

            var solutions = __data.answer.texts;            
            userinput = [];
            var matches = [];
            var answers = [];

            // 1. Collect the user's answers in an array.
            var i;
            for (i = 1; i <= solutions.length; i++) {
                (function (innerI){
                    var ui = document.getElementById('cardbox-userinput-' + innerI).value;
                    if (ui.trim() !== '') {
                        userinput.push(ui);
                    }

                })(i);
            }

            // 2. For each solution: Check whether it is among the user's answers and collect the matches.
            solutions.forEach(check);

            // 3. Collect matches and non-matches and transform them into a displayable form.
            //    Also determine whether there are incorrect answers.
            userinput.forEach(collect);
            __data['userinputitems'] = answers;

            // 4. Check whether there are as many answers as solutions.
            if (userinput.length < solutions.length) {
                answeriscomplete = 0;
                if (userinput.length === 0) {
                    answergiven = 0;
                }

            } else {
                answeriscomplete = 1;
            }
            // 5. Determine whether the card should count as known or unknown.
            if ( (answeriscorrect === 1) && (answeriscomplete === 1) ) {
                considercardcorrect = true;
            }

            /**
             * This function takes each solution and checks whether it contains
             * one of the user's answers or is contained in one of the user's answers.
             * 
             * @param {type} solutionitem
             * @param {type} index
             * @returns {undefined}
             */
            function check(solutionitem, index) {

                solutionitem = solutionitem.puretext;
                
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
             * Function returns true if one of the strings is contained within the other ('or' identical).
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
                    var answer = {
                        userinput: userinput,
                        colorclass: 'cardbox-input-color-incorrect'
                    };
                }

                answers.push(answer);
            }

        }
//        /**
//         * Function places a green or red feedback notification at the top of the page.
//         *
//         * @returns {undefined}
//         */
//        function giveFeedback() {
//
//            var wrapper = document.getElementById("cardbox-feedback-wrapper");
//            var feedbackbox = document.getElementById("cardbox-feedback");
//
//            if ( (answeriscorrect === 1) && (answeriscomplete === 1) ) {
//
//                wrapper.classList.add('cardbox-success');
//                feedbackbox.innerHTML = M.util.get_string('feedback:correctandcomplete', 'cardbox');
//
//            } else if ( (answergiven === 1) && (answeriscorrect === 1) ) {
//
//                wrapper.classList.add('cardbox-warning');
//                feedbackbox.innerHTML = M.util.get_string('feedback:incomplete', 'cardbox');
//
//            } else if (answergiven === 0) {
//
//                wrapper.classList.add('cardbox-error');
//                feedbackbox.innerHTML = M.util.get_string('feedback:notknown', 'cardbox');
//                
//            } 
//            else {
//
//                wrapper.classList.add('cardbox-error');
//                feedbackbox.innerHTML = M.util.get_string('feedback:incorrectandpossiblyincomplete', 'cardbox');
//            }
//
//        }

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
            var islastcard = false;
            
            // This was the last card of this practice session.
            if (position == (cardcount-1) && toRepeat.length === 0) {
                
                if (iscorrect == 1) {
                    next = 0;
                } else {
                    islastcard = true;
                    next = cardId;
                    willBeRepetition = 1;
                }

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
                data: {id: __cmid, action: 'updateandnext', case: __case, cardid: cardId, iscorrect: iscorrect, next: next, isrepetition: isrepetition, sesskey: M.cfg.sesskey},
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
                            // Unless this was the last card and the next card is going to be this card once more, anyway.
                            if (!islastcard) {
                                toRepeat.push(cardId);
                            }
                            
                        }

                    // Cards that are repeated because they were answered wrongly before:
                    // If it was answered wrongly again:
                    } else if (iscorrect == 0) {
                        // Mark the card for repetition once more.
                        // Unless this was the last card and the next card is going to be this card once more, anyway.
                        if (!islastcard) {
                            toRepeat.push(cardId);
                        }
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

//        /**
//         * Function rerenders the template with the question data of a new flashcard.
//         *
//         * @param {type} newdata
//         * @returns {undefined}
//         */
//        function renderNewCard(newdata, next) {
//
//            if (isrepetition === 0) {
//                position = position + 1;
//                cardId = __selection[position];
//            } else {
//                cardId = next;
//            }
//
//            (function (templates, data) {
//                        templates.render('mod_cardbox/practice', data)
//                                .then(function (html, js) {
//                                    templates.replaceNodeContents('#cardbox-practice', html, js);
//
//                                }).then(function () {
//                                        addQuestionEvents();
//
//                                }); // Add a catch.
//            })(templates, newdata);
//
//        }
//
//        function renderSolutionForSelfCheck() {
//
//            var newdata = __data;
//            newdata['case1'] = false;
//            newdata['case3'] = true;
//            
//            (function (templates, data) {
//                        templates.render('mod_cardbox/practice', data)
//                                .then(function (html, js) {
//                                    templates.replaceNodeContents('#cardbox-practice', html, js);
//
//                                }).then(function () {
//                                        addAnswerEvents();
//
//                                }); // Add a catch.
//            })(templates, newdata);
//
//        }
//        /**
//         * This function 'flips' the card from question to solution in the self-check mode.
//         *
//         * @returns {undefined}
//         */
//        function renderSolutionAutoCheck() {
//
//            // Tell the templatable to display the solution view instead of the question view.
//            var newdata = __data;
//            newdata['case2'] = false;
//            newdata['case4'] = true;
//            if (considercardcorrect) {
////                newdata['overridestyle'] = ' btn-danger';
//                newdata['overridelabel'] = M.util.get_string('override_isincorrect', 'cardbox');
//            } else {
////                newdata['overridestyle'] = ' btn-success';
//                newdata['overridelabel'] = M.util.get_string('override_iscorrect', 'cardbox');
//            }
//
//            (function (templates, data) {
//                        templates.render('mod_cardbox/practice', data)
//                                .then(function (html, js) {
//                                    templates.replaceNodeContents('#cardbox-practice', html, js);
//                                }).then(function () {
//                                        addAnswerEvents();
//                                        giveFeedback();
//                                }); // Add a catch.
//            })(templates, newdata);
//
//        }
        /**
         * Function tells the user that the session is finished.
         *
         * @returns {undefined}
         */
        function finishPractice() {

            // 1. Hide the last card that was practiced.
            $('#cardbox-practice-replacable').toggleClass('hidden');

            // 2. Save this session's performance in cardbox_statistics.
            $.ajax({
                type: 'POST',
                url: 'action.php',
                data: {id: __cmid, action: 'saveperformance', countright: countright, countwrong: countwrong, sesskey: M.cfg.sesskey},
                success: function(result){
                    result = JSON.parse(result);
                }
            });

            // 3. Then display it as a doughnut chart.
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
            
        }


    });
}

class Viewcontroller {
    
    constructor(casex, templates, data) {
        this.case = casex;
        this.templates = templates;
        this.data = data;
    }
    
    renderNewQuestion(newdata) { // renderNewQuestion(newdata, next) {
        
//        if (isrepetition === 0) {
//            position = position + 1;
//            cardId = __selection[position];
//        } else {
//            cardId = next;
//        }

        (function (templates, data) {
                    templates.render('mod_cardbox/practice', data)
                            .then(function (html, js) {
                                templates.replaceNodeContents('#cardbox-practice', html, js);

                            }).then(function () {
                                    //addQuestionEvents(); TODO: move

                            }); // Add a catch.
        })(this.templates, newdata);
        
    }
    
    renderAnswer(considercardcorrect = true) {
        
        var newdata = __data;
        
        if (this.case % 2 == 0) { // If the user is in auto-check mode.
            
            newdata['case2'] = false;
            newdata['case4'] = true;
            
            if (considercardcorrect) {
                newdata['overridelabel'] = M.util.get_string('override_isincorrect', 'cardbox');
            } else {
                newdata['overridelabel'] = M.util.get_string('override_iscorrect', 'cardbox');
            }
            
            
        } else { // If the user checks their own answers.
         
            newdata['case1'] = false;
            newdata['case3'] = true;
            
        }
        
        (function (templates, data) {
                    templates.render('mod_cardbox/practice', data)
                            .then(function (html, js) {
                                templates.replaceNodeContents('#cardbox-practice', html, js);
                            }).then(function () {
                                    addAnswerEvents();
                                    if (this.case % 2 == 0) {
                                        giveFeedback();
                                    }
                            }); // Add a catch.
        })(templates, newdata);
        
        /**
         * Function places a green or red feedback notification at the top of the page.
         *
         * @returns {undefined}
         */
        function giveFeedback() {

            var wrapper = document.getElementById("cardbox-feedback-wrapper");
            var feedbackbox = document.getElementById("cardbox-feedback");

            if ( (answeriscorrect === 1) && (answeriscomplete === 1) ) {

                wrapper.classList.add('cardbox-success');
                feedbackbox.innerHTML = M.util.get_string('feedback:correctandcomplete', 'cardbox');

            } else if ( (answergiven === 1) && (answeriscorrect === 1) ) {

                wrapper.classList.add('cardbox-warning');
                feedbackbox.innerHTML = M.util.get_string('feedback:incomplete', 'cardbox');

            } else if (answergiven === 0) {

                wrapper.classList.add('cardbox-error');
                feedbackbox.innerHTML = M.util.get_string('feedback:notknown', 'cardbox');
                
            } 
            else {

                wrapper.classList.add('cardbox-error');
                feedbackbox.innerHTML = M.util.get_string('feedback:incorrectandpossiblyincomplete', 'cardbox');
            }

        }
    }
    
    
    /**
     * Function rerenders the template with the question data of a new flashcard.
     *
     * @param {type} newdata
     * @returns {undefined}
     */
    renderNewCard(newdata, next) {

        if (isrepetition === 0) {
            position = position + 1;
            cardId = __selection[position];
        } else {
            cardId = next;
        }

        (function (templates, data) {
                    templates.render('mod_cardbox/practice', data)
                            .then(function (html, js) {
                                templates.replaceNodeContents('#cardbox-practice', html, js);

                            }).then(function () {
                                    addQuestionEvents();

                            }); // Add a catch.
        })(templates, newdata);

    }

    renderSolutionForSelfCheck() {

        var newdata = __data;
        newdata['case1'] = false;
        newdata['case3'] = true;

        (function (templates, data) {
                    templates.render('mod_cardbox/practice', data)
                            .then(function (html, js) {
                                templates.replaceNodeContents('#cardbox-practice', html, js);

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
    renderSolutionAutoCheck() {

        // Tell the templatable to display the solution view instead of the question view.
        var newdata = __data;
        newdata['case2'] = false;
        newdata['case4'] = true;
        if (considercardcorrect) {
//                newdata['overridestyle'] = ' btn-danger';
            newdata['overridelabel'] = M.util.get_string('override_isincorrect', 'cardbox');
        } else {
//                newdata['overridestyle'] = ' btn-success';
            newdata['overridelabel'] = M.util.get_string('override_iscorrect', 'cardbox');
        }

        (function (templates, data) {
                    templates.render('mod_cardbox/practice', data)
                            .then(function (html, js) {
                                templates.replaceNodeContents('#cardbox-practice', html, js);
                            }).then(function () {
                                    addAnswerEvents();
                                    giveFeedback();
                            }); // Add a catch.
        })(templates, newdata);

    }
}

class Statistics {
    
    
    
}


class 