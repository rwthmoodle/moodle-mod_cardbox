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

    require(['jquery', 'core/templates', 'core/notification'], function ($, templates, notification) {

        var position = 0;
        var answeredCorrectly = 0;
//        var toRepeat = array();

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
        
        
        
        
        
//        document.getElementById('cardbox-get-next-card').addEventListener('click', function(e) {
//            proceed();
//
//        });
        
        function flipCard() {    

            // 1. Hide the question and the send button and display the answer instead.
            $('.cardbox-image').toggleClass('hidden');
            $('.cardbox-text').toggleClass('hidden');
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
        
        
        function proceed(iscorrect) {
            
            $.ajax({
                type: 'POST',
                url: 'action.php',
                data: {id: __cmid, action: 'updateandnext', cardid: __selection[position], iscorrect: iscorrect, next: __selection[position+1], sesskey: M.cfg.sesskey},
                success: function(result){
                    
                    result = JSON.parse(result);
                    
                    (function (templates, data) {
                                templates.render('mod_cardbox/studyview', data)
                                        .then(function (html, js) {
                                            templates.replaceNodeContents('#cardbox-cardcontainer', html, js);

                                        }).then(function () {
                                            
                                            $('#cardbox-mark-as-correct').toggleClass('hidden');
                                            $('#cardbox-mark-as-incorrect').toggleClass('hidden');
                                            $('#cardbox-submit-answer').toggleClass('hidden');
                                            
                                        }); // Add a catch.
                    })(templates, result.newdata);


                }
            });
            
            
            
            
//            return $.ajax({
//                    type: "POST",
//                    url: "action.php",
//                    data: { "documentId": documentId, "page_Number": pageNumber, "action": 'read', sesskey: M.cfg.sesskey}
//                }).then(function(data){
//                    return JSON.parse(data);
//                });
            
            
            
        }
        
        
    });
 
    
    
}