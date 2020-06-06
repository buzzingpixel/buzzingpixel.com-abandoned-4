/* eslint-disable no-alert,no-restricted-globals */
export default {
    methods: {
        confirmSubmit (e) {
            if (prompt('Type "CONFIRM" to delete') === 'CONFIRM') {
                return;
            }

            e.preventDefault();
        },
    },
};
