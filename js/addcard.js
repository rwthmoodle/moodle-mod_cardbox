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
 function addCard(Y, __cmid, _answersvisible, _data) { // Wrapper function that is called by controller.php.

    require(['jquery', 'core/notification'], function ($, notification) {
        
        registerEventListeners();

        showFieldsWithInput();

        function registerEventListeners() {

            var btnimageques = document.getElementById('id_addimage');
            var imageques = document.getElementById('fitem_id_cardimage');
            var imagedescription = document.getElementById('fitem_id_imagedescription');
            var imagecheckbox = document.getElementById('fgroup_id_imgdescriptionar');
            var btnsoundques = document.getElementById('id_addsound');
            var soundques = document.getElementById('fitem_id_cardsound');
            var btnquescontext = document.getElementById('id_addcontextques');
            var quescontext = document.getElementById('fitem_id_questioncontext');
            var btnanscontext = document.getElementById('id_addcontextans');
            var anscontext = document.getElementById('fitem_id_answercontext');
            var countans = (_answersvisible + 1);

            if (countans>2) {
                for (var i=1; i<countans; i++) {
                    document.getElementById('fitem_id_answer' + i).style.display = 'flex';
                }
            }

            document.getElementById('id_topic').onchange = function() {
                if (document.getElementById('id_topic').value == '0') {
                    document.getElementById('id_newtopic').classList.add("shown");
                } else {
                    document.getElementById('id_newtopic').classList.remove("shown");
                }
            }
            
            btnimageques.addEventListener('click', e => {
                if (imageques.style.display === '') {
                    imageques.style.display = 'flex';
                    imagedescription.style.display = 'flex';
                    imagecheckbox.style.display = 'flex';
                    btnimageques.innerHTML = M.util.get_string('removeimage', 'cardbox');
                } else {
                    imageques.style.display = '';
                    imagedescription.style.display = '';
                    imagecheckbox.style.display = '';  
                    btnimageques.innerHTML = M.util.get_string('addimage', 'cardbox'); 
                }
            });

            btnsoundques.addEventListener('click', e => {
                if (soundques.style.display === '') {
                    soundques.style.display = 'flex';
                    btnsoundques.innerHTML = M.util.get_string('removesound', 'cardbox');
                } else {
                    soundques.style.display = '';
                    btnsoundques.innerHTML = M.util.get_string('addsound', 'cardbox');
                }
            });

            btnquescontext.addEventListener('click', e => {
                if (quescontext.style.display === '') {
                    quescontext.style.display = 'flex';
                    btnquescontext.innerHTML = M.util.get_string('removecontext', 'cardbox');
                } else {
                    quescontext.style.display = '';
                    btnquescontext.innerHTML = M.util.get_string('addcontext', 'cardbox');
                }            
            });

            btnanscontext.addEventListener('click', e => {
                if (anscontext.style.display === '') {
                    anscontext.style.display = 'flex';
                    btnanscontext.innerHTML = M.util.get_string('removecontext', 'cardbox');
                } else {
                    anscontext.style.display = '';
                    btnanscontext.innerHTML = M.util.get_string('addcontext', 'cardbox');
                }            
            });

            document.getElementById('id_addanswer').addEventListener('click', e => {
                document.getElementById('fitem_id_answer' + countans).style.display = 'flex';
                countans++;
            });

        }

        function showFieldsWithInput() {

            var btnimageques = document.getElementById('id_addimage');
            var imageques = document.getElementById('fitem_id_cardimage');
            var imagedescription = document.getElementById('fitem_id_imagedescription');
            var imagecheckbox = document.getElementById('fgroup_id_imgdescriptionar');
            var btnsoundques = document.getElementById('id_addsound');
            var soundques = document.getElementById('fitem_id_cardsound');
            var btnquescontext = document.getElementById('id_addcontextques');
            var quescontext = document.getElementById('fitem_id_questioncontext');
            var btnanscontext = document.getElementById('id_addcontextans');
            var anscontext = document.getElementById('fitem_id_answercontext');
/*             var image = document.getElementById('id_cardimage_fieldset').getElementsByClassName('filemanager');
            var sound = document.getElementById('id_cardimage_fieldset').getElementsByClassName('filemanager'); */


            if (_data != null) {
                if (_data['showquesimage']) {
                    imageques.style.display = 'flex';
                    imagedescription.style.display = 'flex';
                    imagecheckbox.style.display = 'flex';
                    btnimageques.innerHTML = M.util.get_string('removeimage', 'cardbox');
                }
    
                if (_data['showquessound']) {
                    soundques.style.display = 'flex';
                    btnsoundques.innerHTML = M.util.get_string('removesound', 'cardbox');
                }
    
                if (_data['showquescontext']) {
                    quescontext.style.display = 'flex';
                    btnquescontext.innerHTML = M.util.get_string('removecontext', 'cardbox');
                }
    
                if (_data['showanscontext']) {
                    anscontext.style.display = 'flex';
                    btnanscontext.innerHTML = M.util.get_string('removecontext', 'cardbox');
                }
            }

            if (document.getElementById('id_questioncontext').value != '') {
                quescontext.style.display = 'flex';
                btnquescontext.innerHTML = M.util.get_string('removecontext', 'cardbox');
            }
            if (document.getElementById('id_answercontext').value != '') {
                quescontext.style.display = 'flex';
                btnquescontext.innerHTML = M.util.get_string('removecontext', 'cardbox');
            }

            /* if (image[0].style.display === "") {
                imageques.style.display = 'flex';
                imagedescription.style.display = 'flex';
                imagecheckbox.style.display = 'flex';
                btnimageques.innerHTML = M.util.get_string('removeimage', 'cardbox');
            }

            if (sound[0].style.display === "") {
                soundques.style.display = 'flex';
                btnsoundques.innerHTML = M.util.get_string('removesound', 'cardbox');
            } */

        }
    });

    
}