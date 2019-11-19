/* eslint-disable no-new */
import WindowResizeWatcher from './WindowResizeWatcher.js';
import Menu from './Components/Menu.js';
import Documentation from './Components/Documentation.js';

new WindowResizeWatcher();

// Main menu
// noinspection TypeScriptUMDGlobal
new Vue(Menu);

// Documentation sidebar
if (document.querySelector('[ref="Documentation"]')) {
    // noinspection TypeScriptUMDGlobal
    new Vue(Documentation);
}
