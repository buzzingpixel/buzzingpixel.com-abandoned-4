#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function cypress() {
    docker run -it -v ${PWD}:/e2e -w /e2e -e CYPRESS_baseUrl=https://buzzingpixel.localtest.me:26087/ --network=host ${cypressDockerImage};

    return 0;
}

function cypress-interactive() {
    ./platform/node_modules/.bin/cypress open

    return 0;
}
