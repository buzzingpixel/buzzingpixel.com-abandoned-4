#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function up () {
    chmod -R 0777 storage;

    chmod -R 0777 secure-storage;

    docker-compose ${composeFiles} -p buzzingpixel build;

    docker-compose ${composeFiles} -p buzzingpixel up -d;

    return 0;
}
