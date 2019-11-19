export default {
    el: '[ref="Documentation"]',

    mounted () {
        const self = this;

        self.$watch('sidebarIsSticky', (val) => {
            if (val) {
                self.setSticky();

                return;
            }

            self.setNotSticky();
        });

        window.addEventListener('scroll', () => {
            self.positionCheck();
        });
    },

    data () {
        return {
            sidebarIsSticky: false,
        };
    },

    methods: {
        positionCheck () {
            const self = this;

            if (self.$el.getBoundingClientRect().y < 16) {
                self.sidebarIsSticky = true;

                return;
            }

            self.sidebarIsSticky = false;
        },

        setSticky () {
            console.log('setSticky');
        },

        setNotSticky () {
            console.log('setNotSticky');
        },
    },
};
