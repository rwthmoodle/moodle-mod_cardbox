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
import * as Str from 'core/str';
export const init = async(cmid, answersvisible, data) => {
    const [
        addimage,
        removeimage,
        addsound,
        removesound,
        addcontext,
        removecontext
    ] = await Str.get_strings([
        {key: 'addimage', component: 'mod_cardbox'},
        {key: 'removeimage', component: 'mod_cardbox'},
        {key: 'addsound', component: 'mod_cardbox'},
        {key: 'removesound', component: 'mod_cardbox'},
        {key: 'addcontext', component: 'mod_cardbox'},
        {key: 'removecontext', component: 'mod_cardbox'}
    ]);
    registerEventListeners();
    showFieldsWithInput();
    /**
     * Register page event listeners.
     */
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
        var countans = (answersvisible + 1);
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
        };
        btnimageques.addEventListener('click', () => {
            if (imageques.style.display === '') {
                imageques.style.display = 'flex';
                imagedescription.style.display = 'flex';
                imagecheckbox.style.display = 'flex';
                btnimageques.innerHTML = removeimage;
            } else {
                imageques.style.display = '';
                imagedescription.style.display = '';
                imagecheckbox.style.display = '';
                btnimageques.innerHTML = addimage;
            }
        });
        btnsoundques.addEventListener('click', () => {
            if (soundques.style.display === '') {
                soundques.style.display = 'flex';
                btnsoundques.innerHTML = removesound;
            } else {
                soundques.style.display = '';
                btnsoundques.innerHTML = addsound;
            }
        });
        btnquescontext.addEventListener('click', () => {
            if (quescontext.style.display === '') {
                quescontext.style.display = 'flex';
                btnquescontext.innerHTML = removecontext;
            } else {
                quescontext.style.display = '';
                btnquescontext.innerHTML = addcontext;
            }
        });
        btnanscontext.addEventListener('click', () => {
            if (anscontext.style.display === '') {
                anscontext.style.display = 'flex';
                btnanscontext.innerHTML = removecontext;
            } else {
                anscontext.style.display = '';
                btnanscontext.innerHTML = addcontext;
            }
        });
        document.getElementById('id_addanswer').addEventListener('click', () => {
            document.getElementById('fitem_id_answer' + countans).style.display = 'flex';
            countans++;
        });
    }
    /**
     * Show fields that already contain data.
     */
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
        if (data) {
            if (data['showquesimage']) {
                imageques.style.display = 'flex';
                imagedescription.style.display = 'flex';
                imagecheckbox.style.display = 'flex';
                btnimageques.innerHTML = removeimage;
            }
            if (data['showquessound']) {
                soundques.style.display = 'flex';
                btnsoundques.innerHTML = removesound;
            }
            if (data['showquescontext']) {
                quescontext.style.display = 'flex';
                btnquescontext.innerHTML = removecontext;
            }
            if (data['showanscontext']) {
                anscontext.style.display = 'flex';
                btnanscontext.innerHTML = removecontext;
            }
        }
        if (document.getElementById('id_questioncontext').value != '') {
            quescontext.style.display = 'flex';
            btnquescontext.innerHTML = removecontext;
        }
        if (document.getElementById('id_answercontext').value != '') {
            quescontext.style.display = 'flex';
            btnquescontext.innerHTML = removecontext;
        }
    }
};