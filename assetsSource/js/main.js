/* eslint-disable no-new */

// Import Components
import Documentation from './Components/Documentation.js';
import FormSubmitConfirm from './Components/FormSubmitConfirm.js';
import LogInForm from './Components/LogInForm.js';
import Menu from './Components/Menu.js';
import PrismCodeHighlighting from './Components/PrismCodeHighlighting.js';
import WindowResizeWatcher from './WindowResizeWatcher.js';

// Set up window resize watching
new WindowResizeWatcher();

// Run the Main menu
// noinspection TypeScriptUMDGlobal,JSValidateTypes
new Vue(Menu);

// Run the Documentation sidebar
if (document.querySelector('[ref="Documentation"]')) {
    // noinspection TypeScriptUMDGlobal,JSValidateTypes
    new Vue(Documentation);
}

// Load prism code highlighting
if (document.querySelector('code')
    || document.querySelector('pre')
) {
    new PrismCodeHighlighting();
}

// Run LogInForm
if (document.querySelector('[ref="LogInForm"]')) {
    // noinspection TypeScriptUMDGlobal,JSValidateTypes
    new Vue(LogInForm);
}

// Run form submit config
document.querySelectorAll('[ref="FormSubmitConfirm"]').forEach((el) => {
    const obj = Object.assign({}, FormSubmitConfirm);
    obj.el = el;

    // noinspection TypeScriptUMDGlobal,JSValidateTypes
    new Vue(obj);
});
