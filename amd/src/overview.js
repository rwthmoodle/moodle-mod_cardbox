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
import $ from 'jquery';
import Notification from 'core/notification';
import {get_strings} from 'core/str';

export const init = (cmid, topic, sort, deck) => {
    const topicfilter = document.getElementById('cardbox-overview-topicfilter');
    const filterselect = document.getElementById('cardbox-filter-options');
    const deckfilter = document.getElementById('cardbox-overview-deckfilter');
    const searchbtn = document.getElementById('cardbox-search-btn');
    if (!topicfilter || !filterselect || !deckfilter) {
        return;
    }

    filterselect.value = sort;

    topicfilter.onchange = function () {
        const select = this.options[this.selectedIndex];
        topic = select.value;

        window.location.href = buildUrl(cmid, topic, sort, deck);
    };

    deckfilter.onchange = function () {
        const select = this.options[this.selectedIndex];
        deck = select.value;

        window.location.href = buildUrl(cmid, topic, sort, deck);
    };

    filterselect.onchange = function () {
        const select = this.options[this.selectedIndex];
        sort = select.value;

        window.location.href = buildUrl(cmid, topic, sort, deck);
    };
    searchbtn.addEventListener('click', function() {
        const searchText = document.getElementById('cardbox-search').value;
        // eslint-disable-next-line no-console
        console.log('Search text:', searchText);
        window.location.href = buildUrl(cmid, topic, sort, deck, searchText);
    });
    document.querySelectorAll('#cardbox-overview .cardbox-overview-button-edit')
        .forEach(btn => {
            const card = btn.closest('#cardbox-card-in-overview');
            const cardid = card.getAttribute('data-cardid');

            btn.addEventListener('click', () => {
                openCardFormForEditing(cardid);
            });
        });

    $('.cardbox-delete-button').on('click', function(e) {
        e.preventDefault();

        const id = this.id.split('-');
        const cardid = id[2];

        deleteCard(cardid);
    });
    /**
     * create URLs
     *
     * @param {int} cmid
     * @param {int} topic
     * @param {int} sort
     * @param {int} deck
     * @param {string} search
     */
    function buildUrl(cmid, topic, sort, deck, search) {
        let targeturl = window.location.pathname +
            '?id=' + cmid +
            '&action=overview' +
            '&topic=' + topic +
            '&sort=' + sort +
            '&deck=' + deck;
        if (search) {
            targeturl += '&search=' + encodeURIComponent(search);
        }
        return targeturl;
    }
    /**
     * Open card foe editing
     *
     * @param {String} cardid
     */
    function openCardFormForEditing(cardid) {
        window.location.href =
            window.location.pathname +
            '?id=' + cmid +
            '&action=editcard' +
            '&cardid=' + cardid +
            '&from=overview';
    }
     // eslint-disable-next-line no-console
        //console.log('deleteCard called');
    /**
     * Delete card
     *
     * @param {String} cardid
     */
    function deleteCard(cardid) {
        // eslint-disable-next-line no-console
        console.log('deleteCard called');
        get_strings([
            {key: 'deletecard', component: 'cardbox'},
            {key: 'deletecardinfo', component: 'cardbox'},
            {key: 'yes', component: 'cardbox'},
            {key: 'cancel', component: 'cardbox'}
        ]).then(([title, message, yes, cancel]) => {

            Notification.confirm(
                title,
                message,
                yes,
                cancel,
                function() {
                    // User clicked YES.
                    window.location.href =
                        window.location.pathname +
                        '?id=' + cmid +
                        '&action=deletecard' +
                        '&cardid=' + cardid +
                        '&sesskey=' + M.cfg.sesskey;
                }
            );
        });
    }
};