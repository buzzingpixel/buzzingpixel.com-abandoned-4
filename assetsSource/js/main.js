/* eslint-disable no-new */

// Import Components
import WindowResizeWatcher from './WindowResizeWatcher.js';
import Menu from './Components/Menu.js';
import Documentation from './Components/Documentation.js';
import PrismCodeHighlighting from './Components/PrismCodeHighlighting.js';

// Set up window resize watching
new WindowResizeWatcher();

// Run the Main menu
// noinspection TypeScriptUMDGlobal
new Vue(Menu);

// Run the Documentation sidebar
if (document.querySelector('[ref="Documentation"]')) {
    // noinspection TypeScriptUMDGlobal
    new Vue(Documentation);
}

// Load prism code highlighting
if (document.querySelector('code')
    || document.querySelector('pre')
) {
    new PrismCodeHighlighting();
}
