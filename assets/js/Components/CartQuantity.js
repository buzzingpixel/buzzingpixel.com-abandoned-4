import Loader from '../Helpers/Loader.js';

export default (data) => {
    const js = 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js';

    Loader.loadJs(js).then(() => {
        // noinspection JSUnresolvedVariable,ES6ModulesDependencies
        axios.get('/ajax/user/payload')
            .then((obj) => {
                data.cartTotalQuantity = obj.data.cart.totalQuantity;
            });
    });
};
