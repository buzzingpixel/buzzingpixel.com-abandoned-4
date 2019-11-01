/* eslint-disable import/prefer-default-export */

// noinspection TypeScriptUMDGlobal,ES6ModulesDependencies
export const EventBus = new Vue({
    data () {
        return {
            windowWidth: 0,
            windowHeight: 0,
            windowWidthHeight: '',
            mobileMenuBreakPoint: 600,
            mobileMenuIsOpen: false,
            desktopMenuActiveId: '',
        };
    },
});
