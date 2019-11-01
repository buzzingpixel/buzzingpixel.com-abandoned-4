import { EventBus } from './EventBus.js';

class WindowResizeWatcher {
    constructor () {
        const self = this;

        self.resizeTimer = 0;

        setTimeout(() => {
            WindowResizeWatcher.setWindowSize();
        }, 200);

        window.addEventListener('resize', () => {
            clearTimeout(self.resizeTimer);

            self.resizeTimer = setTimeout(() => {
                WindowResizeWatcher.setWindowSize();
            }, 100);
        });
    }

    static setWindowSize () {
        EventBus.windowWidth = window.innerWidth;
        EventBus.windowHeight = window.innerHeight;
        EventBus.windowWidthHeight = `${window.innerWidth}x${window.innerHeight}`;
    }
}

export default WindowResizeWatcher;
