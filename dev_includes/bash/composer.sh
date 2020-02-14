#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function composer() {
    docker run -it -v ${PWD}:/app -v buzzingpixel_composer-home-volume:/composer-home-volume --env COMPOSER_HOME=/composer-home-volume -w /app ${composerDockerImage} bash -c "${allArgs}";

    return 0;
}
