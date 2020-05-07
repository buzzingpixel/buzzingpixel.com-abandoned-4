export default {
    el: '[ref="LogInForm"]',

    mounted () {
        const self = this;
        const { activeTab } = self.$el.dataset;

        if (!activeTab) {
            return;
        }

        self.activeTab = activeTab;
    },

    data () {
        return {
            activeTab: 'logIn',
        };
    },
};
