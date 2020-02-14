#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function phpunit() {
    # Run in Docker (disabled for no because of performance)
    # docker run -it -v ${PWD}:/app -w /app buzzingpixel:php-dev bash -c "php -d memory_limit=4G /app/vendor/phpunit/phpunit/phpunit --configuration /app/phpunit.xml ${allArgsExceptFirst}";

    # Run locally
    xdebug-disable;
    php -d memory_limit=4G vendor/phpunit/phpunit/phpunit --configuration phpunit.xml ${allArgsExceptFirst};

    return 0;
}

function phpunit-coverage() {
    # Run in Docker (disabled for no because of performance)
    # docker run -it -v ${PWD}:/app -w /app buzzingpixel:php-dev bash -c "php -d memory_limit=4G /app/vendor/phpunit/phpunit/phpunit --configuration /app/phpunit.xml ${allArgsExceptFirst}";

    # Run locally
    xdebug-enable;
    php -d memory_limit=4G vendor/phpunit/phpunit/phpunit --configuration phpunit.xml ${allArgsExceptFirst};
    xdebug-disable;

    return 0;
}
