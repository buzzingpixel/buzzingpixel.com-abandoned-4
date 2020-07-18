#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function up() {
    touch storage/app.log;

    chmod -R 0777 storage;

    chmod -R 0777 secure-storage;

    docker network create proxy >/dev/null 2>&1

    COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker-compose ${composeFiles} -p buzzingpixel up -d;

    return 0;
}
