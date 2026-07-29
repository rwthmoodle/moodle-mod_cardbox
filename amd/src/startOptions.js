import ModalFactory from 'core/modal_factory';
import * as Str from 'core/str';
export const init = async (cmid, openmodal) => {

    const optionsTemplate = document.getElementById('cardbox-options-template');
    const optionsButton = document.getElementById('cardbox-see-options');
    const [title, beginpractice, cancel] = await Str.get_strings([
        {key: 'titleforchoosesettings', component: 'cardbox'},
        {key: 'beginpractice', component: 'cardbox'},
        {key: 'cancel', component: 'cardbox'}
    ]);
    let modal;
    ModalFactory.create({
        title: title,
        body: optionsTemplate.innerHTML
    }).then(createdModal => {
        modal = createdModal;
        modal.setFooter(`
            <button type="button" class="btn btn-primary" data-action="save">
                ${beginpractice}
            </button>
            <button type="button" class="btn btn-secondary" data-action="cancel">
                ${cancel}
            </button>
        `);
        modal.getRoot().on('click', '[data-action="save"]', () => {
            applySettings(modal, cmid);
        });
        modal.getRoot().on('click', '[data-action="cancel"]', () => {
            modal.hide();
        });
        if (openmodal) {
            modal.show();
        }
        addEventListeners(modal, cmid);
    });
    optionsButton.addEventListener('click', () => {
        if (modal) {
            const root = modal.getRoot();
             // Force "Yes" to be selected.
            root.find('#cardbox-practiceall-yes').prop('checked', true);
            // Disable both radio buttons.
            root.find('#cardbox-practiceall-yes').prop('disabled', true);
            root.find('#cardbox-practiceall-no').prop('disabled', true);
            modal.show();
        }
    });
};
const addEventListeners = (modal) => {
    const root = modal.getRoot();
    root.on('change', '#cardbox-onlyonetopic', () => {
        const value = root.find('#cardbox-onlyonetopic').val();
        if (value !== '-1') {
            root.find('#cardbox-topic-select').hide();
            root.find('#cardbox-topic-description').hide();
        } else {
            root.find('#cardbox-topic-select').show();
            root.find('#cardbox-topic-description').show();
        }
    });
};
const applySettings = (modal, cmid) => {
    const root = modal.getRoot();
    const topic = root.find('#cardbox-topic').val();
    const practiceall =
        root.find('#cardbox-practiceall-yes').is(':checked');
    const onlyonetopic =
        root.find('#cardbox-onlyonetopic').val();
    const amountcards =
        root.find('#cardbox-amountcards').val();
    let correctionmode;
    const radios =
        root.find('#cardbox-form input[name="correctionmode"]');
    radios.each(function() {
        if (this.checked) {
            correctionmode = this.value;
        }
    });
    const url =
        `${window.location.pathname}?id=${cmid}` +
        `&action=practice` +
        `&start=true` +
        `&mode=${correctionmode}` +
        `&topic=${topic}` +
        `&practiceall=${practiceall}` +
        `&onlyonetopic=${onlyonetopic}` +
        `&amountcards=${amountcards}`;
    window.location.href = url;
};