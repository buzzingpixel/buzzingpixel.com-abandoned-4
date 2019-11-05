import { EventBus } from '../EventBus.js';

export default {
    el: '[ref="SiteNav"]',

    mounted () {
        const self = this;

        EventBus.$watch('mobileMenuIsOpen', (val) => {
            if (val) {
                self.openMenu();
                return;
            }

            self.closeMenu();
        });

        EventBus.$watch('windowWidth', (val) => {
            if (val < EventBus.mobileMenuBreakPoint) {
                EventBus.desktopMenuActiveId = '';

                return;
            }

            EventBus.mobileMenuIsOpen = false;
        });

        EventBus.$watch('desktopMenuActiveId', (val) => {
            if (EventBus.windowWidth < EventBus.mobileMenuBreakPoint) {
                return;
            }

            self.activateDesktopMenu(val);
        });

        document.getElementById('SiteContent').addEventListener('click', self.siteContentClick);
    },

    data () {
        return {
            isActive: false,
            siteNavMouseLeaveTimer: 0,
            subNavActionInProgress: false,
        };
    },

    methods: {
        mobileOpenerClick () {
            EventBus.mobileMenuIsOpen = !EventBus.mobileMenuIsOpen;
        },

        openMenu () {
            this.isActive = true;
        },

        closeMenu () {
            this.isActive = false;
        },

        siteContentClick () {
            EventBus.desktopMenuActiveId = '';
        },

        subMenuMouseEnter (e) {
            this.subNavAction(e);
        },

        subMenuClick (e) {
            e.preventDefault();

            this.subNavAction(e);
        },

        subMenuItemClick (e) {
            e.stopPropagation();
        },

        subNavAction (e) {
            const self = this;

            if (self.subNavActionInProgress) {
                return;
            }

            self.subNavActionInProgress = true;

            EventBus.desktopMenuActiveId = e.currentTarget.dataset.id;

            setTimeout(() => {
                self.subNavActionInProgress = false;
            }, 100);
        },

        siteNavMouseEnter () {
            clearTimeout(this.siteNavMouseLeaveTimer);
        },

        siteNavMouseLeave () {
            const self = this;

            if (self.subNavActionInProgress) {
                return;
            }

            clearTimeout(self.siteNavMouseLeaveTimer);

            self.siteNavMouseLeaveTimer = setTimeout(() => {
                EventBus.desktopMenuActiveId = '';
            }, 400);
        },

        activateDesktopMenu (id) {
            const self = this;

            clearTimeout(self.siteNavMouseLeaveTimer);

            Object.keys(self.$refs).forEach((i) => {
                const el = self.$refs[i];

                if (el.dataset.isSubNav !== 'true') {
                    return;
                }

                if (i === id) {
                    el.classList.add(el.dataset.activeClass);

                    return;
                }

                el.classList.remove(el.dataset.activeClass);
            });
        },

        closeDesktopMenus () {
            const self = this;

            Object.keys(self.$refs).forEach((i) => {
                const el = self.$refs[i];

                if (el.dataset.isSubNav !== 'true') {
                    return;
                }

                el.classList.remove(el.dataset.activeClass);
            });
        },
    },
};
