import * as Str from 'core/str';
/**
 * Initializes the practice module.
 * @param {number} cmid - Course module ID (unused in current implementation)
 * @param {number} selection - selection of cards for practise
 * @param {number} disableautocorrect - value for disableautocorrect
 * @returns {Promise<void>}
 */
export const init = async (cmid, selection, disableautocorrect) => {// eslint-disable-line no-unused-vars
    const Ques_Selfcheck = 1; // eslint-disable-line no-unused-vars
    const Ques_Autocheck = 2; // eslint-disable-line no-unused-vars
    const Ans_Selfcheck = 3; // eslint-disable-line no-unused-vars
    const Ans_Autocheck = 4; // eslint-disable-line no-unused-vars
    const Suggest_Ans = 5; // eslint-disable-line no-unused-vars
    const EnableAutocorrect = 0; // eslint-disable-line no-unused-vars
    const Disable_Autocorrect = 1; // eslint-disable-line no-unused-vars
    const Case_Autocheck = 2; // eslint-disable-line no-unused-vars
    const Case_SelfCheck = 1; // eslint-disable-line no-unused-vars
    // eslint-disable-next-line no-console
    console.log("practice.js loaded");
    const [
        correctcomplete, // eslint-disable-line no-unused-vars
        overrideincorrect, // eslint-disable-line no-unused-vars
        overridecorrect, // eslint-disable-line no-unused-vars
        incomplete, // eslint-disable-line no-unused-vars
        notknown, // eslint-disable-line no-unused-vars
        incorrectandpossiblyincomplete, // eslint-disable-line no-unused-vars
        right, // eslint-disable-line no-unused-vars
        wrong, // eslint-disable-line no-unused-vars
        progresschart // eslint-disable-line no-unused-vars
    ] = await Str.get_strings([
        { key: 'feedback:correctandcomplete', component: 'cardbox' },
        { key: 'override_isincorrect', component: 'cardbox' },
        { key: 'override_iscorrect', component: 'cardbox' },
        { key: 'feedback:incomplete', component: 'cardbox' },
        { key: 'feedback:notknown', component: 'cardbox' },
        { key: 'feedback:incorrectandpossiblyincomplete', component: 'cardbox' },
        { key: 'right', component: 'cardbox' },
        { key: 'wrong', component: 'cardbox' },
        { key: 'titleprogresschart', component: 'cardbox' }
    ]);
    /**
     * Remove Notifications
     */
    function removeNotifications() {
        let notificationpanel = document.getElementById("user-notifications");
        if (notificationpanel) {
            while (notificationpanel.hasChildNodes()) {
                notificationpanel.removeChild(notificationpanel.firstChild);
            }
        }
    }
    // Main execution
    removeNotifications();

};