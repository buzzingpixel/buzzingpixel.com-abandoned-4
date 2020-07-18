import Loader from '../Helpers/Loader.js';

const axiosJs = 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js';

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

export default (data) => {
    if (!window.axios) {
        Loader.loadJs(axiosJs).then(() => {
            firstRun(data);
        });

        return;
    }

    firstRun(data);
};
