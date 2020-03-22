#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function build() {
    docker-compose ${composeFiles} -p buzzingpixel build;

    return 0;
}
