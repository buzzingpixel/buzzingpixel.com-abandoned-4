export default class {
    constructor (el) {
        this.actionName = el.dataset.confirmAction || 'delete';

        el.addEventListener('submit', (e) => {
            this.checkSubmit(e);
        });
    }

    checkSubmit (e) {
        const msg = `Type "CONFIRM" to ${this.actionName}`;

        // eslint-disable-next-line no-alert
        if (prompt(msg) === 'CONFIRM') {
            return;
        }

        e.preventDefault();
    }
}
