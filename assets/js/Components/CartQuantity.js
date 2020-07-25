const run = (data) => {
    // noinspection JSUnresolvedVariable,ES6ModulesDependencies
    window.axios.get('/ajax/user/payload')
        .then((obj) => {
            data.cartTotalQuantity = obj.data.cart.totalQuantity;
        });
};

const firstRun = (data) => {
    run(data);

    window.addEventListener('cartUpdated', () => {
        run(data);
    });
};

const preRun = (data) => {
    if (!window.axios) {
        setTimeout(() => {
            preRun(data);
        }, 50);

        return;
    }

    firstRun(data);
};

export default (data) => {
    preRun(data);
};
