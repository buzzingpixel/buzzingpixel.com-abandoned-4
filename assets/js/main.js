/* eslint-disable no-new */

import CartQuantity from './Components/CartQuantity.js';
import ConfirmSubmit from './Components/ConfirmSubmit.js';
import Flatpickr from './Components/Flatpickr.js';
import PreventEmptySubmit from './Components/PreventEmptySubmit.js';
import Selects from './Components/Selects.js';

window.Methods.CartQuantity = CartQuantity;

// ConfirmSubmit
const forms = document.querySelectorAll('[ref="ConfirmSubmit"]');
forms.forEach((el) => {
    new ConfirmSubmit(el);
});

// Flatpickr
const flatpickrEls = document.querySelectorAll('[ref="flatpickr"]');
if (flatpickrEls.length > 0) {
    new Flatpickr(flatpickrEls);
}

// Prevent empty submit
const emptySubmitEls = document.querySelectorAll('[ref="PreventEmptySubmit"]');
emptySubmitEls.forEach((el) => {
    new PreventEmptySubmit(el);
});

// Selects
const selectEls = document.querySelectorAll('[ref="select"]');
if (selectEls.length > 0) {
    new Selects(selectEls);
}
