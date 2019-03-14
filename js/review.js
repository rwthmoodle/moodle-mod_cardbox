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
 * @authors   Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function startReview(Y, __cmid, __selection, __boxcount, __selfchecking) { // Wrapper function that is called by controller.php

    require(['jquery', 'core/templates', 'core/notification', 'chartjs'], function ($, templates, notification, chart) {
        
        var cardId = document.getElementById('cardbox-card-in-review').dataset.cardid;
        
        console.log('cardId: ', cardId);
        
        function registerEventListeners() {

            document.getElementById('cardbox-approve').addEventListener('click', function(e) {
                
            });

            document.getElementById('cardbox-edit').addEventListener('click', function(e) {
                
            });

            document.getElementById('cardbox-reject').addEventListener('click', function(e) {
                
            });
            
            document.getElementById('cardbox-skip').addEventListener('click', function(e) {
                
            });
        }
        
    });

}