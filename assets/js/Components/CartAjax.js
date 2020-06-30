import Loader from '../Helpers/Loader.js';

const axiosJs = 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js';

let tries = 0;

let debouncer = 0;

const currentQuantities = {};

const runDebounce = (modelData) => {
    if (tries > 100) {
        throw new Error('Could not update cart');
    }

    if (!window.axios) {
        setTimeout(() => {
            tries += 1;

            runDebounce(modelData);
        }, 50);

        return;
    }

    const keys = Object.keys(modelData.items);

    const requests = [];

    keys.forEach((key) => {
        const quantity = parseInt(modelData.items[key].quantity, 10);
        let wasUndefined = false;

        if (currentQuantities[key] === undefined) {
            wasUndefined = true;

            currentQuantities[key] = quantity;
        }

        if (currentQuantities[key] < 1 && !wasUndefined) {
            requests.push(window.axios.get(
                `/cart/add/${key}`,
            ));
        } else {
            requests.push(window.axios.get(
                `/cart/update-quantity/${key}/${quantity}`,
            ));
        }

        currentQuantities[key] = quantity;
    });

    Promise.all(requests).then(() => {
        window.dispatchEvent(window.cartUpdatedEvent);

        // noinspection JSUnresolvedVariable,ES6ModulesDependencies
        window.axios.get('/ajax/cart/payload')
            .then((obj) => {
                modelData.totalQuantity = obj.data.totalQuantity;
                modelData.subTotal = obj.data.subTotal;
                modelData.tax = obj.data.tax;
                modelData.total = obj.data.total;
            });
    });
};

const run = (modelData) => {
    clearTimeout(debouncer);

    debouncer = setTimeout(() => {
        runDebounce(modelData);
    }, 300);
};

export default (modelData) => {
    if (!window.axios) {
        Loader.loadJs(axiosJs).then(() => {
            run(modelData);
        });

        return;
    }

    run(modelData);
};
