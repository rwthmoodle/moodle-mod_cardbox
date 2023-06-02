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
const Ques_Selfcheck = 1;
const Ques_Autocheck = 2;
const Ans_Selfcheck  = 3;
const Ans_Autocheck  = 4;
const Suggest_Ans    = 5;
const EnableAutocorrect   = 0;
const Disable_Autocorrect = 1;
const Case_Autocheck = 2;
const Case_SelfCheck = 1;
/**
 * This script controlls the behaviour of the page during practice.
 *
 * @param {type} Y required by moodle
 * @param int __cmid course module id
 * @param array __selection ids of those cards selected for practice
 * @param {type} __boxcount
 * @param int __case specifies whether the practice mode is auto- or selfcheck and whether a question or answer is shown.
 * @param {type} __data card contents (question and answer) to be passed to the template for rendering.
 * @returns {undefined}
 */
function startPractice(Y, __cmid, __selection, __case, __data, __mode, __disableacvals) { // Wrapper function that is called by controller.php.

    require(['jquery', 'core/templates', 'core/notification', 'core/chartjs'], function ($, templates, notification, chart) {
        
        /*********** 1. Variables and Calls ***********/

        removeNotifications();

        var evaluate = new Evaluate();
        var output = new Output(__case, templates, notification);
        var statistics = new Statistics(chart);

        var coordinate = new Coordinate(__cmid, evaluate, output, statistics, __selection, __data, __case, __mode, __disableacvals);
        var eventhandling = new EventHandling(coordinate);
        coordinate.addEventHandler(eventhandling);
        var acval = eventhandling.controller.acvals.filter(checkacvalue, eventhandling.controller.cardId);
        if (__case == Case_SelfCheck) {
            var quescase = Ques_Selfcheck;
        } else {
            if (acval.length == 1) {
                var disableautocorrect = acval[0].split('_')[1];
                if (disableautocorrect == Disable_Autocorrect) {
                    var quescase = Ques_Selfcheck;
                } else {
                    var quescase = Ques_Autocheck;
                }
            }
        }
        
        function checkacvalue(acval) {
            if (acval.split('_')[0] == this) {
                var val_arr = acval.split('_');
                return val_arr[1];
            }

        }

        if (quescase == Ques_Autocheck) {
            eventhandling.registerEventsForQuestionAutoCheck();
        } else {
            eventhandling.registerEventsForQuestionSelfCheck();
        }

        var bluebox = document.getElementById('nocardsduenotification');
        if (bluebox !== null) {
            bluebox.parentNode.removeChild(bluebox);
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

    });
}


class EventHandling {
        
    constructor(controller) {
        this.controller = controller;
    }
    /**
     * 
     * @returns {undefined}
     */
    registerEventsForQuestionSelfCheck() {

        document.getElementById('cardbox-check-answer').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('let-me-check-the-answer');

        }.bind(this));

        document.getElementById('cardbox-end-session').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('end-practice');

        }.bind(this));

    }
    /**
     * 
     * @returns {undefined}
     */
    registerEventsForQuestionAutoCheck() {

        document.getElementById('cardbox-userinput-1').focus();

        document.getElementById('cardbox-submit-answer').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('submit-answer');

        }.bind(this));

        document.getElementById('cardbox-do-not-know').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('do-not-know');

        }.bind(this));

        document.getElementById('cardbox-end-session').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('end-practice');

        }.bind(this));

    }
    /**
     * 
     * @returns {undefined}
     */
    registerEventsForAnswerSelfCheck() {

        // Button tells the server that the current card was answered correctly and requests a new flashcard.
        document.getElementById('cardbox-mark-as-correct').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('mark-as-correct');

        }.bind(this));

        // Button tells the server that the current card was answered incorrectly and requests a new flashcard.
        document.getElementById('cardbox-mark-as-incorrect').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
/*             this.controller.reactTo('mark-as-incorrect'); */
            document.getElementById('cardbox-proceed').hidden = false;
            document.getElementById('cardbox-suggestanswer').hidden = false; 
            document.getElementById('cardbox-mark-as-incorrect').disabled = true;
            document.getElementById('cardbox-mark-as-correct').disabled = true;

        }.bind(this));

        document.getElementById('cardbox-suggestanswer').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('suggest-answer');

        }.bind(this));

        document.getElementById('cardbox-proceed').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('mark-as-incorrect');

        }.bind(this));

    }
    /**
     * 
     * @returns {undefined}
     */
    registerEventsForAnswerAutoCheck() {

        // Button overrides the result of the automatic check, tells the server and requests a new flashcard to render.
        document.getElementById('cardbox-override').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('override');

        }.bind(this));


        // Button sends the result of the automatic check to the server and requests a new flashcard to render.
        document.getElementById('cardbox-proceed').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('proceed');

        }.bind(this));

        document.getElementById('cardbox-suggestanswer').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('suggest-answer');

        }.bind(this));


    }

    registerEventsForSuggestAnswerAutoCheck() {

        document.getElementById('cardbox-suggestanswer-input').focus();

        document.getElementById('cardbox-cancel-button').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('proceed');

        }.bind(this));

        document.getElementById('cardbox-savesuggestedanswer').addEventListener('click', function(e) {

            // Prevent page reload.
            e.preventDefault();

            // Notify controller of this click event.
            this.controller.reactTo('savesuggestedanswer');

        }.bind(this)); 

    }

} // EventHandling


class Coordinate {

    constructor(cmid, evaluate, output, statistics, selection, data, __case, __mode, __disableacvals) {

            this.cmid = cmid;
            this.selection = selection;
            this.data = data; // contents of the current flashcard
            this.case = __case;
            this.mode = __mode;
            
            this.cardcount = selection.length;
            this.cardsleft = selection.length;
            
            // Information about the current flashcard.
            this.position = 0;
            this.cardId = selection[0];
            this.isrepetition = 0;
            this.considercardcorrect = false; // XXX move
            
            this.next;
            this.willBeRepetition = 0;
            this.islastcard = false;
            
            // Collection of cards that were answered wrongly. They will be repeated until answered correctly once.
            // Their status in the database won't change, however, i.e. they go back to the first box.
            this.toRepeat = [];
            
            this.evaluate = evaluate;
            this.output = output;
            this.statistics = statistics;
            this.acvals = __disableacvals;
        }

        addEventHandler(eventhandling) {
            this.eventhandling = eventhandling;
        }

        reactTo(clicked) {

            switch(clicked) {
                
                // I. question-view events for flipping the card.
                
                case 'let-me-check-the-answer':
                    
                    // Render the solution.
                    this.output.renderAnswer(this.evaluate, this.eventhandling, this.data);
                    break;
                    
                    
                case 'submit-answer':
                    
                    // 1. Check whether the answer is correct and complete.
                    this.evaluate.checkAnswer(this.data);
                    // 2. Render the solution along with the user's corrected answer(s) and give feedback.
                    this.output.renderAnswer(this.evaluate, this.eventhandling);
                    break;
     
                case 'do-not-know':
                    // 1. Inform evaluation that no answer was given.
                    this.evaluate.registerUnknownAnswer(this.data);
                    // 2. Render the solution and give feedback.
                    this.output.renderAnswer(this.evaluate, this.eventhandling);
                    break;
                
                // II. answer-view events for updating the card data and getting a new card.
                
                case 'mark-as-correct':

                    this.proceed(1);
                    break;
   
                case 'mark-as-incorrect':

                    this.proceed(0);
                    break;

                case 'proceed':

                    if (this.evaluate.isCardCorrect()) {
                        this.proceed(1);
                    } else {
                        this.proceed(0);
                    }
                    break;
                    
                case 'override':

                    this.evaluate.overrideJudgement();
                    document.getElementById('cardbox-override').disabled = true;
                    var feedbackbox = document.getElementById("cardbox-feedback");
                    feedbackbox.classList.remove('cardbox-error');                
                    feedbackbox.classList.add('cardbox-success');
                    feedbackbox.innerHTML = M.util.get_string('feedback:correctandcomplete', 'cardbox');
                    document.getElementById('cardbox-user-solution').classList.replace('cardbox-input-color-incorrect', 'cardbox-input-color-correct');
                    break;
                
                case 'suggest-answer':

                    this.suggestAnswer(this.data);
                    break;

                case 'savesuggestedanswer':

                    if (this.case === Ques_Selfcheck) {
                        var iscorrect = false;     
                    } else {
                        if (this.evaluate.isCardCorrect()) {
                            var iscorrect = true;
                        } else {
                            var iscorrect = false;
                        }   
                    }
                    this.savesuggestedAnswer(this.data, iscorrect);
                    break;

                case 'end-practice':

                    this.statistics.finishPractice(this.cmid);

            }
 
        }
        /**
         * Function initiates update of the progress status of the current card
         * and then renders the next card or wraps up the practice session.
         *
         * @param {type} iscorrect
         * @returns {undefined}
         */
        proceed(iscorrect) {

            // 1. Determine which (if any) card is next to come.
            this.determineNextCard(iscorrect);
            if (iscorrect === 1) {
                this.cardsleft = this.cardsleft - 1;
            }

            // 2. Update the status of the current card and request the next card (if there is one).
            $.ajax({
                type: 'POST',
                url: 'action.php',
                data: {id: this.cmid, action: 'updateandnext', case: this.case, cardid: this.cardId, iscorrect: iscorrect, next: this.next, isrepetition: this.isrepetition, sesskey: M.cfg.sesskey, cardsleft: this.cardsleft, mode: this.mode}
            }).then(function(data) {
                var result = JSON.parse(data);
                
                // 3. Adjust the statistics etc..
                this.registerProgress(iscorrect);

                // 4. Show the next card or finish the practice session with a doughnut progress chart.
                
                this.registerAndRenderNextCard(result.newdata);

            }.bind(this));
        }

        suggestAnswer(data) {


            data['case'.concat(Ques_Selfcheck)] = false;
            data['case'.concat(Ques_Autocheck)] = false;
            data['case'.concat(Ans_Selfcheck)] = false;
            data['case'.concat(Ans_Autocheck)] = false;
            data['case'.concat(Suggest_Ans)] = true;

            this.output.renderSuggestAnswerTemplate(this.eventhandling, data);

        }

        savesuggestedAnswer(data, iscorrect) {

            var userinput = document.getElementById('cardbox-suggestanswer-input').value;  

            $.ajax({
                type: 'POST',
                url: 'action.php',
                data: {id: this.cmid, action: 'savesuggestedanswer', case: this.case, cardid: this.cardId, iscorrect: iscorrect, next: this.next, isrepetition: this.isrepetition, sesskey: M.cfg.sesskey, userinput: userinput}
            }).then(function(iscorrect) {

                if(iscorrect) {
                    this.proceed(1);
                } else {
                    var quescase = Ques_Autocheck;
                    this.proceed(0);
                }
            }.bind(this, iscorrect));
        }

        getRandomInt(max) {
            return Math.floor(Math.random() * (max));
        }

        registerProgress(iscorrect) {
            // Regular cards, i.e. cards that still count for the statistics:
            if (this.isrepetition === 0) {

                if (iscorrect === 1) {
                    this.statistics.incrementCountRight();

                } else {
                    this.statistics.incrementCountWrong();
                    /* If a wrong answer was given, mark this card for repetition.
                    /* Unless this was the last card and the next card is going to
                     * be this card once more, anyway.
                     */
                    if (!this.islastcard) {
                        this.toRepeat.push(this.cardId);
                    }
                }

            // Cards that are repeated because they were answered wrongly before:
            // If it was answered wrongly again:
            } else if (iscorrect === 0) {
                // Mark the card for repetition once more.
                // Unless this was the last card and the next card is going to be this card once more, anyway.
                if (!this.islastcard) {
                    this.toRepeat.push(this.cardId);
                }
            }
        }

        registerAndRenderNextCard(newdata) {

            if (this.next === 0) {
                this.statistics.finishPractice(this.cmid);

            } else {
                this.data = newdata;
                this.isrepetition = this.willBeRepetition;
                if (this.isrepetition === 0) {
                    this.position = this.position + 1;
                    this.cardId = this.selection[this.position];
                } else {
                    this.cardId = this.next;
                }
                var acval = this.acvals.filter(checkacvalue, this.cardId);
                if (this.case !== Case_SelfCheck) {
                    if (acval.length == 1) {
                        var disableautocorrect = acval[0].split('_')[1]    ;
                    }
                }
                
/*                 newdata['selectionsize'] = this.cardcount; */
                newdata['cardsleft'] = this.cardsleft;
                if (newdata['case'.concat(this.case)] == false) {
                    if (this.case == Ques_Selfcheck) {
                        newdata['case'.concat(Ques_Selfcheck)] = true;
                        newdata['case'.concat(Ques_Autocheck)] = false;
                    } else {
                        if (disableautocorrect == Disable_Autocorrect) {
                            newdata['case'.concat(Ques_Selfcheck)] = true;
                            newdata['case'.concat(Ques_Autocheck)] = false;
                        } else {
                            newdata['case'.concat(Ques_Selfcheck)] = false;
                            newdata['case'.concat(Ques_Autocheck)] = true;
                        }
                    }
                }
                //this.output.renderNewQuestion(this.eventhandling).bind(this);
                
                this.output.renderNewQuestion(this.eventhandling, newdata);
            }
            function checkacvalue(acval) {
                if (acval.split('_')[0] == this) {
                    var val_arr = acval.split('_');
                    return val_arr[1];
                }

            }
        }
        /**
         * This function determines which card to request from the server next.
         * It also figures out whether that card will be the last card of the session
         * and/or whether it is repeated because it could not be answered before.
         * In that case, its status won't be updated.
         * 
         * @param {type} iscorrect
         * @returns {undefined}
         */
        determineNextCard(iscorrect) {

            this.willBeRepetition = 0;
            this.islastcard = false;
            
            // This was the last card of this practice session.
            if (this.position === (this.cardcount-1) && this.toRepeat.length === 0) {
                
                if (iscorrect === 1) {
                    this.next = 0;
                } else {
                    this.islastcard = true;
                    this.next = this.cardId;
                    this.willBeRepetition = 1;
                }

            // There are only regular cards left.
            } else if (this.position < (this.cardcount-1) && this.toRepeat.length === 0) {
                this.next = this.selection[this.position+1];
            
            // There are only cards left that are to be repeated.
            } else if (this.position === (this.cardcount-1) && this.toRepeat.length !== 0) {
                this.next = this.toRepeat.shift();
                this.willBeRepetition = 1;
                
            // There are both regular cards and cards to be repeated left.
            } else {
                if (this.getRandomInt(3) < 2) {
                    this.next = this.selection[this.position+1];
                } else {
                    this.next = this.toRepeat.shift();
                    this.willBeRepetition = 1;
                }
            }
            
        }

} // Coordinate


/**
 * This class checks a user's answer for existance, correctness and completeness.
 *
 * @type type
 */
class Evaluate {
    
    constructor() {
        
        this.considercardcorrect = false;
        this.answeriscorrect = 0;
        this.answeriscomplete = 0;
        this.answergiven = 1;
        this.data;
        this.necessaryanswers;
        this.casesensitive;
        
    }
    
    registerUnknownAnswer(data) {
        this.considercardcorrect = false;
        this.answergiven = 0;
        this.answeriscorrect = 0;
        this.answeriscomplete = 0;
        this.data = data;
        var answer = {
            userinput: ' ',
            colorclass: 'cardbox-input-color-incorrect'
        };        
        this.data['userinputitems'] = answer;
    }

    checkAnswer(data) {

        // Reset everything.
        this.data = data;
        this.answeriscorrect = 1;
        this.answeriscomplete = 0;
        this.answergiven = 1;
        this.useranswers;
        this.necessaryanswers = data.necessaryanswers;
        this.casesensitive = data.casesensitive;

        var solutions = data.answer.texts;            
        var userinput = [];
        var userinputtocompare = [];
        var matches = [];
        var answers = [];
        
        // 1. Collect the user's answers in an array.
        var i;
        var length;
        if (this.necessaryanswers === "1") {
            length = 1;
        } else {
            length = solutions.length;
        }
        for (i = 1; i <= length; i++) {
            (function (innerI){
                var ui = document.getElementById('cardbox-userinput-' + innerI).value;  
                if (ui.trim() !== '') {
                    userinput.push(ui.trim());
                }

            })(i);
        }
/*         data["morethanonesolution"] = (solutions.length>1); */

        if (this.casesensitive === "1") {
            for (var i=0; i<solutions.length; i++) {
                solutions[i].puretext = solutions[i].puretext.toLowerCase();
            }       
            for (var i=0; i<userinput.length; i++) {
                userinputtocompare[i] = userinput[i].toLowerCase();
            }
        } else {
            userinputtocompare = userinput;
        }

        // 2. For each solution: Check whether it is among the user's answers and collect the matches.
        solutions.forEach(check);
        // 3. Collect matches and non-matches and transform them into a displayable form.
        //    Also determine whether there are incorrect answers.
        userinput.forEach(collect.bind(this));
        if (answers.length === 0) {
            var answer = {
                userinput: ' ',
                colorclass: 'cardbox-input-color-incorrect'
            };        
            answers.push(answer);
            this.data['userinputitems'] = answers;
        } else {
            this.data['userinputitems'] = answers; // auslagern in Output   
        }
        this.useranswers = answers; // testweise

        // 4. Check whether there are as many answers as solutions.
        if (userinput.length < solutions.length) {
            this.answeriscomplete = 0;
            if (userinput.length === 0) {
                this.answergiven = 0;
            }

        } else {
            this.answeriscomplete = 1;
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
            var userinputitemtocompare;
            for (j = 0; j < userinputtocompare.length; j++) {
                (function (innerI){

                    userinputitem = userinput[innerI];
                    userinputitemtocompare = userinputtocompare[innerI];
                    if (compare(solutionitem, userinputitemtocompare)) {

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

            if (a === b) {
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
                if (this.necessaryanswers === "1") {
                    this.necessaryanswers = -1;
/*                     document.getElementById('cardbox-proceed').disabled = false; */
                }

            } /* else if (userinput === "" || userinput === null)  {
                // Note that the user made at least one mistake.
                this.answeriscorrect = 0;
                var answer = {
                    userinput: "-",
                    colorclass: 'cardbox-input-color-incorrect'
                }
            } */ else {
                // Note that the user made at least one mistake.
                this.answeriscorrect = 0;
                var answer = {
                    userinput: userinput,
                    colorclass: 'cardbox-input-color-incorrect'
                };
            }

            answers.push(answer);
        }
    }
    
    getDataToDisplay() {
        return this.data;
    }
    
    getEvaluation() {
        
        if ( ((this.answeriscorrect === 1) && (this.answeriscomplete === 1)) || this.necessaryanswers === -1 ) {
            this.answeriscorrect === 1;
            this.answeriscomplete === 1;
            return 'correctandcomplete';

        } else if ( (this.answergiven === 1) && (this.answeriscorrect === 1) ) {
            return 'incomplete';

        } else if (this.answergiven === 0) {
            return 'notknown';

        } else {
            return 'incorrectandpossiblyincomplete';
        }
        
    }
    
    isCardCorrect() {
        if ( (this.answeriscorrect === 1) && (this.answeriscomplete === 1) || this.necessaryanswers === -1) {
            return true;
        }
        return false;
    }
    
    overrideJudgement() {
        if ( (this.answeriscorrect === 1) && (this.answeriscomplete === 1)) {
            this.answeriscorrect = 0;
        } else {
            this.answeriscorrect = 1;
            this.answeriscomplete = 1;
        }
    }

}

class Output {
    
    constructor(casex, templates, notification) {
        this.case = casex;
        this.templates = templates;
        this.notification = notification;
    }
    /**
     * Function rerenders the template with the question data of a new flashcard.
     *
     * @param {type} newdata
     * @returns {undefined}
     */
    renderNewQuestion(eventhandling, data) {

        (function (templates, data, mode) {
                    templates.render('mod_cardbox/practice', data)
                            .then(function (html, js) {
                                templates.replaceNodeContents('#cardbox-practice', html, js);
                            }).then(function () {
                                    // Register event listeners for the newly rendered partial.
                                    if (eventhandling.controller.case == Case_Autocheck) { 
                                        var acval = eventhandling.controller.acvals.filter(checkacvalue, eventhandling.controller.cardId);   
                                        if (acval.length == 1) {
                                            var disableautocorrect = acval[0].split('_')[1];
                                        }

                                        if (disableautocorrect == Disable_Autocorrect) {
                                            eventhandling.registerEventsForQuestionSelfCheck();
                                        } else {
                                            eventhandling.registerEventsForQuestionAutoCheck();
                                        }
                                    } else {
                                        eventhandling.registerEventsForQuestionSelfCheck();
                                    }
                                    function checkacvalue(acval) {
                                        if (acval.split('_')[0] == this) {
                                            var val_arr = acval.split('_');
                                            return val_arr[1];
                                        }
                        
                                    }
                            }); // Add a catch.
        })(this.templates, data, this.case);

    }

    renderSuggestAnswerTemplate(eventhandling, data) {
        
        var length = data.answer.texts.length;
        if (length < 2) {
            var uiinputonobj = data.userinputitems[0];
            var ui = uiinputonobj.userinput;
            if (ui !== " ") {
                data['suggestedansinput'] = ui;
            }
        }

        (function (templates, data) {
                    templates.render('mod_cardbox/practice', data)
                            .then(function (html, js) {
                                templates.replaceNodeContents('#cardbox-practice', html, js);
                            }).then(function () {

                            eventhandling.registerEventsForSuggestAnswerAutoCheck();

                            });// Add a catch.
        })(this.templates, data);

    }
    

    /**
     * 
     * @param bool considercardcorrect
     * @param string evaluation
     * @returns {undefined}
     */
    renderAnswer(evaluate, eventhandling, data = null) {
        var acval = eventhandling.controller.acvals.filter(checkacvalue, eventhandling.controller.cardId);
        if (eventhandling.controller.case == Case_SelfCheck) {
            var quescase = Ques_Selfcheck;
        } else {
            if (acval.length == 1) {
                var disableautocorrect = acval[0].split('_')[1]    ;
                if (disableautocorrect == Disable_Autocorrect) {
                    var quescase = Ques_Selfcheck;
                } else {
                    var quescase = Ques_Autocheck;
                }
            }
        }
        function checkacvalue(acval) {
            if (acval.split('_')[0] == this) {
                var val_arr = acval.split('_');
                return val_arr[1];
            }

        }
        if (quescase == Ques_Autocheck || quescase == Ans_Autocheck) { // If the user is in auto-check mode.
            
            var evaluation = evaluate.getEvaluation();
            var considercardcorrect = evaluate.isCardCorrect();
            var newdata = evaluate.getDataToDisplay();
            
            if (evaluation === 'incomplete') {
                var solutionstodisplay = [];
                newdata['answer']['texts'].forEach(function(answer) {
                    var match = false;
                    newdata['userinputitems'].forEach(function(userinput) {
                        if (answer['puretext'] === userinput['userinput']) {
                            match = true;
                        } 
                    }.bind(answer));
                    if (!match) {
                        solutionstodisplay.push(answer);
                    }
                }.bind(newdata));
                newdata['answer']['texts'] = solutionstodisplay;
            }

            newdata['case'.concat(Ques_Autocheck)] = false;
            newdata['case'.concat(Ans_Autocheck)] = true;
            
            if (considercardcorrect) {
                /* if (newdata['morethanonesolution'] && (newdata['necessaryanswers']==="1")) {
                    newdata['cardcorrect'] = false;    
                } else {
                    newdata['cardcorrect'] = true;
                } */
                newdata['cardcorrect'] = true;
                newdata['showbuttonsuggestanswer'] = false;
            } else {
                newdata['showbuttonsuggestanswer'] = true;
                newdata['cardcorrect'] = false;    
            }
            
            if (considercardcorrect) {
                newdata['overridelabel'] = M.util.get_string('override_isincorrect', 'cardbox');
            } else {
                newdata['overridelabel'] = M.util.get_string('override_iscorrect', 'cardbox');
            }
            
            
        } else { // If the user checks their own answers.
         
            var newdata = data;
            newdata['case'.concat(Ques_Selfcheck)] = false;
            newdata['case'.concat(Ans_Selfcheck)] = true;

            
        }
        
        (function (templates, notification, data, mode) {
                    templates.render('mod_cardbox/practice', data)
                            .then(function (html, js) {
                                templates.replaceNodeContents('#cardbox-practice', html, js);
                            })
                            .then(function () {
                                var acval = eventhandling.controller.acvals.filter(checkacvalue, eventhandling.controller.cardId);
                                if (eventhandling.controller.case == Case_SelfCheck) {
                                    var quescase = Ques_Selfcheck;
                                } else {
                                    if (acval.length == 1) {
                                        var disableautocorrect = acval[0].split('_')[1]    ;
                                        if (disableautocorrect == Disable_Autocorrect) {
                                            var quescase = Ques_Selfcheck;
                                        } else {
                                            var quescase = Ques_Autocheck;
                                        }
                                    }
                                }
                                
                                function checkacvalue(acval) {
                                    if (acval.split('_')[0] == this) {
                                        var val_arr = acval.split('_');
                                        return val_arr[1];
                                    }

                                }
                                if (quescase == Ans_Autocheck || quescase == Ques_Autocheck) {
                                    giveFeedback(evaluation);
                                    // Register event listeners for the newly rendered partial.
                                    eventhandling.registerEventsForAnswerAutoCheck();
                                } else {
                                    // Register event listeners for the newly rendered partial.
                                    eventhandling.registerEventsForAnswerSelfCheck();
                                }
                                
                            })
                            .fail(notification.exception);
        })(this.templates, this.notification, newdata, this.case);

        /**
         * Function places a green, yellow or red feedback notification around the proceed buttons.
         *
         * @param {type} evaluation
         * @returns {undefined}
         */
        function giveFeedback(evaluation) {

/*             var wrapper = document.getElementById("cardbox-feedback-wrapper"); */
            var feedbackbox = document.getElementById("cardbox-feedback");

            if (evaluation === 'correctandcomplete') {
                
//                feedbackbox.classList.add('cardbox-success');
                feedbackbox.innerHTML = M.util.get_string('feedback:correctandcomplete', 'cardbox');


            } else if (evaluation === 'incomplete') {

//                feedbackbox.classList.add('cardbox-warning');
                feedbackbox.innerHTML = M.util.get_string('feedback:incomplete', 'cardbox');


            } else if (evaluation === 'notknown') {

//                feedbackbox.classList.add('cardbox-error');
                feedbackbox.innerHTML = M.util.get_string('feedback:notknown', 'cardbox');
                document.getElementById('cardbox-override').disabled = true; 


            } else {

//                feedbackbox.classList.add('cardbox-error');
                feedbackbox.innerHTML = M.util.get_string('feedback:incorrectandpossiblyincomplete', 'cardbox');
            }

        }
    }
    
}

class Statistics {
    
    constructor(chart) {
        // Statistical information that will be displayed to the user at the end of practice.
        this.countright = 0;
        this.countwrong = 0;
        this.chart = chart;
        this.starttime = Math.floor(new Date().getTime()/1000.0);
    }
    
    incrementCountRight() {
        this.countright++;
    }
    
    incrementCountWrong() {
        this.countwrong++;
    }
    /**
     * Function tells the user that the session is finished.
     *
     * @param {type} cmid
     * @returns {undefined}
     */
    finishPractice(cmid) {

        // 1. Hide the last card that was practiced.
        $('#cardbox-practice-replacable').toggleClass('hidden');

        // 2. Save this session's performance in cardbox_statistics.
        $.ajax({
            type: 'POST',
            url: 'action.php',
            data: {id: cmid, action: 'saveperformance', countright: this.countright, countwrong: this.countwrong, sesskey: M.cfg.sesskey, starttime: this.starttime},
            success: function(result){
                result = JSON.parse(result);
            }
        });

        // 3. Then display it as a doughnut chart.
        var ctx = document.getElementById("cardbox-practice-feedback").getContext("2d");

        var chartdata = {
            datasets: [{
                label: 'Progress',
                data: [this.countright, this.countwrong],
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

}