#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function up() {
    touch storage/app.log;

    chmod -R 0777 storage;

    chmod -R 0777 secure-storage;

    docker-compose ${composeFiles} -p buzzingpixel up -d;

    return 0;
}
