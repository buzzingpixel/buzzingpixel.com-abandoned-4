/**
 * @see https://flatpickr.js.org/
 */

import Loader from '../Helpers/Loader.js';

class Flatpickr {
    /**
     * @param {NodeList} els
     */
    constructor (els) {
        Loader.loadCss(
            'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
        );

        Loader.loadJs('https://cdn.jsdelivr.net/npm/flatpickr').then(() => {
            els.forEach((el) => {
                window.flatpickr(el, {
                    enableTime: true,
                    dateFormat: 'Y-m-d h:i K',
                });
            });
        });
    }
}

export default Flatpickr;
