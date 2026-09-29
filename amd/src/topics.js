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

import 'core/inplace_editable';
import Notification from 'core/notification';
import Ajax from 'core/ajax';
import {
    get_strings as getStrings,
} from 'core/str';
import Modal from 'core/modal';

export const init = (cmid) => {
        registerEventListeners(cmid);
};

/**
 * Function to register and defines event listeners
 * @param {int} cmid
 */
function registerEventListeners(cmid) {

    const deletebtns = document.querySelectorAll('#cardbox-topics .cardbox-edittopics-delete-button');
    deletebtns.forEach(btn => {
        const topic = btn.closest('#cardbox-topic-in-review');
        const topicid = topic.getAttribute('data-topicid');
        const topictitel = topic.getAttribute('data-titel');
        btn.addEventListener('click', () => {
            deletetopic(cmid, topicid, topictitel);
        });
    });

    const editbtns = document.querySelectorAll('#cardbox-topics .cardbox-edittopics-edit-button');
    editbtns.forEach(btn => {
        const edit = btn.closest('#cardbox-topic-in-review');
        const topicid = edit.getAttribute('data-topicid');

        btn.addEventListener('click', () => {
            const topictitel = edit.getAttribute('data-titel');
            edittopic(cmid, topicid, topictitel);
        });
    });

    document.getElementById("cardbox-submit-new-topic").addEventListener('click', () => {
        savenewtopic(cmid);
    });
}
/**
 * Function to delete a topic from Cardbox instance
 * @param {int} cmid
 * @param {int} topicid
 * @param {string} topictitel
 */
function deletetopic(cmid, topicid, topictitel) {
    getStrings([
        {key: 'deletetopic', component: 'cardbox'},
        {key: 'deletetopicinfo', component: 'cardbox', param: "'" + topictitel + "'"},
        {key: 'deletetopicandcards', component: 'cardbox'},
        {key: 'deletetopiconly', component: 'cardbox'},
        {key: 'cancel'}
    ])
    .then(strings => {

        Modal.create({
            title: strings[0],
            body: strings[1],
        }).then(modal => {

            // Create custom buttons
            const root = modal.getRoot();

            root[0].querySelector('.modal-footer').innerHTML = `
                <button type="button" class="btn btn-primary" data-action="delete-all" style ="background-color:#ca3120">
                    ${strings[2]}
                </button>
                <button type="button" class="btn btn-secondary" data-action="delete-topic" style ="background-color:#ca3120">
                    ${strings[3]}
                </button>
                <button type="button" class="btn btn-secondary" data-action="cancel">
                    ${strings[4]}
                </button>
            `;

            root.on('click', '[data-action="delete-all"]', () => {
                deleteTopicAjax(topicid, true);
                modal.hide();
            });

            root.on('click', '[data-action="delete-topic"]', () => {
                deleteTopicAjax(topicid, false);
                modal.hide();
            });

            root.on('click', '[data-action="cancel"]', () => {
                modal.hide();
            });

            modal.show();
        });

    })
    .catch(Notification.exception);
}
/**
 * Helper function for aja calls
 * @param {int} topicid
 * @param {bool} deletecards
 */
function deleteTopicAjax(topicid, deletecards) {
    Ajax.call([{
        methodname: 'mod_cardbox_deletetopic',
        args: {
            topicid: topicid,
            deletecards: deletecards
        },
        done: () => {
            window.location.reload();
        },
        fail: Notification.exception
    }]);
}
/**
 * Function to edit an existing topic from the cardbox instance
 * @param {int} cmid
 * @param {int} topicid
 * @param {string} topictitel
 */
function edittopic(cmid, topicid, topictitel) {

    document.getElementById("cardbox-topic-"+topicid).classList.add('displaynone');
    document.getElementById("cardbox-topic-rename-"+topicid).classList.remove('displaynone');
    document.getElementById("cardbox-changedtopic-"+topicid).value = topictitel;
    document.getElementById("cardbox-changedtopic-"+topicid).focus();
    document.getElementById("cardbox-topic-rename-"+topicid).addEventListener('keydown', () => {
        if (window.event.keyCode == 13) {
            var newtopicname = document.getElementById("cardbox-changedtopic-"+topicid).value;
            if (newtopicname != topictitel){
                Ajax.call([{
                    methodname:'mod_cardbox_renametopic',
                    args: {
                        'topicid': topicid,
                        'newtopicname': newtopicname,
                    },
                    done: () => {
                        document.getElementById("topictitel-"+topicid).innerHTML = newtopicname;
                        document.getElementById("cardbox-topic-"+topicid).parentElement.setAttribute('data-titel', newtopicname);
                    },
                    fail: Notification.exception
                }]);
            }
            document.getElementById("cardbox-topic-"+topicid).classList.remove('displaynone');
            document.getElementById("cardbox-topic-rename-"+topicid).classList.add('displaynone');
        }
        if (window.event.keyCode == 27) {
            document.getElementById("cardbox-topic-"+topicid).classList.remove('displaynone');
            document.getElementById("cardbox-topic-rename-"+topicid).classList.add('displaynone');
        }
    });
    document.getElementById("cardbox-changedtopic-"+topicid).addEventListener('blur', () => {
        document.getElementById("cardbox-topic-"+topicid).classList.remove('displaynone');
        document.getElementById("cardbox-topic-rename-"+topicid).classList.add('displaynone');
    });

}
/**
 * Function to save new topic to the cardbox instance
 * @param {int} cmid
 */
function savenewtopic (cmid) {
    var newtopic = document.getElementById("create-new-topic").value;
    if (newtopic != "") {
        var goTo = window.location.pathname + '?id=' + cmid + '&action=savenewtopic&newtopic=' + newtopic;
        window.location.href = goTo;
    }
}