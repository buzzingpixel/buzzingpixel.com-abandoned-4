/* eslint-disable no-new */

// Setup
import Events from './Events.js';
import SetGlobalData from './SetUp/SetGlobalData.js';
import LoadAxios from './SetUp/LoadAxios.js';
import Analytics from './SetUp/Analytics.js';

// Components
import CartAjax from './Components/CartAjax.js';
import CartQuantity from './Components/CartQuantity.js';
import ConfirmSubmit from './Components/ConfirmSubmit.js';
import CreditCardInputFormatting from './Components/CreditCardInputFormatting.js';
import Flatpickr from './Components/Flatpickr.js';
import LeftFixedScroll from './Components/LeftFixedScroll.js';
import PreventEmptySubmit from './Components/PreventEmptySubmit.js';
import PrismCodeHighlighting from './Components/PrismCodeHighlighting.js';
import Selects from './Components/Selects.js';

// Setup
Events();
SetGlobalData();
LoadAxios();
Analytics();
window.Methods.CartAjax = CartAjax;
window.Methods.CartQuantity = CartQuantity;
window.Methods.LeftFixedScroll = LeftFixedScroll;

// Components

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

// Credit card input formatting
const creditCardInputs = document.querySelectorAll('[ref="creditCardInput"]');
if (creditCardInputs.length > 0) {
    new CreditCardInputFormatting(creditCardInputs);
}

// Prevent empty submit
const emptySubmitEls = document.querySelectorAll(
    '[ref="PreventEmptySubmit"]',
);
emptySubmitEls.forEach((el) => {
    new PreventEmptySubmit(el);
});

// Load prism code highlighting
if (document.querySelector('code')
    || document.querySelector('pre')
) {
    new PrismCodeHighlighting();
}

// Selects
const selectEls = document.querySelectorAll('[ref="select"]');
if (selectEls.length > 0) {
    new Selects(selectEls);
}
