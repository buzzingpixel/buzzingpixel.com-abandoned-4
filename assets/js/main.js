/* eslint-disable no-new */

import CartQuantity from './Components/CartQuantity.js';
import Selects from './Components/Selects.js';

window.Methods.CartQuantity = CartQuantity;

// Selects
const selectEls = document.querySelectorAll('[ref="select"]');
if (selectEls.length > 0) {
    new Selects(selectEls);
}
