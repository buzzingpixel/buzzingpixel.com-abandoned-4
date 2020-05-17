/* eslint-disable no-new */

import CartQuantity from './Components/CartQuantity.js';
import ConfirmSubmit from './Components/ConfirmSubmit.js';
import Selects from './Components/Selects.js';

window.Methods.CartQuantity = CartQuantity;

// ConfirmSubmit
const forms = document.querySelectorAll('[ref="ConfirmSubmit"]');
forms.forEach((el) => {
    new ConfirmSubmit(el);
});

// Selects
const selectEls = document.querySelectorAll('[ref="select"]');
if (selectEls.length > 0) {
    new Selects(selectEls);
}
