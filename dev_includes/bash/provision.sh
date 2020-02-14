#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function provision() {
    docker build -t buzzingpixel:php-dev docker/php-dev;
    docker run -it -v ${PWD}:/app -v buzzingpixel_composer-home-volume:/composer-home-volume --env COMPOSER_HOME=/composer-home-volume -w /app ${composerDockerImage} bash -c "composer install";

    if [[ "${isMacOs}" = "true" ]]; then
        docker run -it -v ${PWD}:/app -v buzzingpixel_node-modules-volume:/app/node_modules -v buzzingpixel_yarn-cache-volume:/usr/local/share/.cache/yarn -w /app ${nodeDockerImage} bash -c "yarn";
        docker run -it -v ${PWD}:/app -v buzzingpixel_node-modules-volume:/app/node_modules -v buzzingpixel_yarn-cache-volume:/usr/local/share/.cache/yarn -w /app ${nodeDockerImage} bash -c "yarn run fab --build-only";
    else
        docker run -it -v ${PWD}:/app -v buzzingpixel_yarn-cache-volume:/usr/local/share/.cache/yarn -w /app ${nodeDockerImage} bash -c "yarn";
        docker run -it -v ${PWD}:/app -v buzzingpixel_yarn-cache-volume:/usr/local/share/.cache/yarn -w /app ${nodeDockerImage} bash -c "yarn run fab --build-only";
    fi

    (cd platform && yarn && cd ..)

    docker exec -it --user root --workdir /opt/project buzzingpixel-php bash -c "php cli app-setup:setup-docker-database";

    return 0;
}
