/**
 * @see https://nosir.github.io/cleave.js/
 */

import Loader from '../Helpers/Loader.js';

class CreditCardInputFormatting {
    /**
     * @param {NodeList} els
     */
    constructor (els) {
        Loader.loadJs('https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js').then(() => {
            els.forEach((el) => {
                // eslint-disable-next-line no-undef,no-new
                new Cleave(el, {
                    creditCard: true,
                });
            });
        });
    }
}

export default CreditCardInputFormatting;
