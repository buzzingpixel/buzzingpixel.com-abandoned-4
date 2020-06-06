class PreventEmptySubmit {
    constructor (el) {
        this.el = el;
        this.inputs = this.el.querySelectorAll(
            '[data-empty-submit-class]',
        );

        el.addEventListener('submit', (e) => {
            this.checkSubmit(e);
        });

        this.inputs.forEach((inputEl) => {
            el.addEventListener('change', () => {
                PreventEmptySubmit.changeEvent(inputEl);
            });
            el.addEventListener('keyup', () => {
                PreventEmptySubmit.changeEvent(inputEl);
            });
        });
    }

    static changeEvent (el) {
        el.classList.remove(el.dataset.emptySubmitClass);
    }

    checkSubmit (e) {
        this.inputs.forEach((el) => {
            PreventEmptySubmit.checkInput(el, e);
        });
    }

    static checkInput (el, e) {
        if (el.value.length > 0) {
            return;
        }

        e.preventDefault();

        el.classList.add(el.dataset.emptySubmitClass);
    }
}

export default PreventEmptySubmit;
