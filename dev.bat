@echo off

set composerDockerImage=composer:1.9.3
set cypressDockerImage=cypress/included:3.5.0
set nodeDockerImage=node:12.12.0
set composeFiles=-f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.dev.sync.yml

set cmd=%1
set allArgs=%*
for /f "tokens=1,* delims= " %%a in ("%*") do set allArgsExceptFirst=%%b
set secondArg=%2
set valid=false

:: If no command provided, list commands
if "%cmd%" == "" (
    set valid=true
    echo The following commands are available:
    echo   .\dev up
    echo   .\dev down
    echo   .\dev provision
    echo   .\dev login [args]
    echo   .\dev cli [args]
    echo   .\dev yarn [args]
    echo   .\dev composer [args]
    echo   .\dev phpcs
    echo   .\dev phpcbf
    echo   .\dev psalm
    echo   .\dev phpstan
    echo   .\dev phpunit [args]
    echo   .\dev eslint
    echo   .\dev cypress
    echo   .\dev cypress-interactive
    exit /b 0
)

if "%cmd%" == "up" (
    docker-compose %composeFiles% -p buzzingpixel build
    docker-compose %composeFiles% -p buzzingpixel up -d
    exit /b 0
)

if "%cmd%" == "down" (
    docker-compose %composeFiles% -p buzzingpixel down
    exit /b 0
)

if "%cmd%" == "provision" (
    docker build -t buzzingpixel:php-dev docker/php-dev
    docker run -it -v %cd%:/app -v buzzingpixel_composer-home-volume:/composer-home-volume --env COMPOSER_HOME=/composer-home-volume -w /app %composerDockerImage% bash -c "composer install"
    docker run -it -v %cd%:/app -v buzzingpixel_node-modules-volume:/app/node_modules -v buzzingpixel_yarn-cache-volume:/usr/local/share/.cache/yarn -w /app %nodeDockerImage% bash -c "yarn"
    docker run -it -v %cd%:/app -v buzzingpixel_node-modules-volume:/app/node_modules -v buzzingpixel_yarn-cache-volume:/usr/local/share/.cache/yarn -w /app %nodeDockerImage% bash -c "yarn run fab --build-only"
    cd platform
    call yarn
    cd ..
    docker exec -it --user root --workdir /opt/project buzzingpixel-php bash -c "php cli app-setup:setup-docker-database"
    exit /b 0
)

if "%cmd%" == "login" (
    docker exec -it --user root --workdir /opt/project buzzingpixel-%secondArg% bash
    exit /b 0
)

if "%cmd%" == "cli" (
    docker exec -it --user root --workdir /opt/project buzzingpixel-php bash -c "php cli %allArgsExceptFirst%"
    exit /b 0
)

if "%cmd%" == "yarn" (
    docker run -it -p 3000:3000 -p 3001:3001 -v %cd%:/app -v buzzingpixel_node-modules-volume:/app/node_modules -v buzzingpixel_yarn-cache-volume:/usr/local/share/.cache/yarn -w /app --network=buzzingpixel_common-buzzingpixel-network %nodeDockerImage% bash -c "%allArgs%"
    exit /b 0
)

if "%cmd%" == "composer" (
    docker run -it -v %cd%:/app -v buzzingpixel_composer-home-volume:/composer-home-volume --env COMPOSER_HOME=/composer-home-volume -w /app %composerDockerImage% bash -c "%allArgs%"
    exit /b 0
)

if "%cmd%" == "phpcs" (
    rem Run in Docker (disabled for no because of performance)
    rem docker run -it -v %cd%:/app -w /app buzzingpixel:php-dev bash -c "vendor/bin/phpcs --config-set installed_paths ../../doctrine/coding-standard/lib,../../slevomat/coding-standard; vendor/bin/phpcs src public/index.php config; vendor/bin/php-cs-fixer fix --verbose --dry-run --using-cache=no;"

    rem Run locally
    vendor/bin/phpcs --config-set installed_paths ../../doctrine/coding-standard/lib,../../slevomat/coding-standard; vendor/bin/phpcs src public/index.php config; vendor/bin/php-cs-fixer fix --verbose --dry-run --using-cache=no;
    exit /b 0
)

if "%cmd%" == "phpcbf" (
    rem Run in Docker (disabled for no because of performance)
    rem docker run -it -v %cd%:/app -w /app buzzingpixel:php-dev bash -c "vendor/bin/phpcbf --config-set installed_paths ../../doctrine/coding-standard/lib,../../slevomat/coding-standard; vendor/bin/phpcbf src public/index.php config; vendor/bin/php-cs-fixer fix --verbose --using-cache=no;"

    rem Run locally
    vendor/bin/phpcbf --config-set installed_paths ../../doctrine/coding-standard/lib,../../slevomat/coding-standard; vendor/bin/phpcbf src public/index.php config; vendor/bin/php-cs-fixer fix --verbose --using-cache=no;
    exit /b 0
)

if "%cmd%" == "psalm" (
    rem Run in Docker (disabled for no because of performance)
    rem docker run -it -v %cd%:/app -w /app buzzingpixel:php-dev bash -c "php -d memory_limit=4G /app/vendor/vimeo/psalm/psalm"
    exit /b 0

    rem Run locally
    php -d memory_limit=4G vendor/vimeo/psalm/psalm
)

if "%cmd%" == "phpstan" (
    rem Run in Docker (disabled for no because of performance)
    rem docker run -it -v %cd%:/app -w /app buzzingpixel:php-dev bash -c "php -d memory_limit=4G /app/vendor/phpstan/phpstan/bin/phpstan analyse config public/index.php src tests cli"

    rem Run locally
    php -d memory_limit=4G vendor/phpstan/phpstan/bin/phpstan analyse config public/index.php src tests cli;
    exit /b 0
)

if "%cmd%" == "phpunit" (
    rem Run in Docker (disabled for no because of performance)
    rem docker run -it -v %cd%:/app -w /app buzzingpixel:php-dev bash -c "php -d memory_limit=4G /app/vendor/phpunit/phpunit/phpunit --configuration /app/phpunit.xml %allArgsExceptFirst%"

    rem Run locally
    php -d memory_limit=4G vendor/phpunit/phpunit/phpunit --configuration /app/phpunit.xml %allArgsExceptFirst%

    exit /b 0
)

if "%cmd%" == "eslint" (
    docker run -it -v %cd%:/app -v buzzingpixel_node-modules-volume:/app/node_modules -v buzzingpixel_yarn-cache-volume:/usr/local/share/.cache/yarn -w /app %nodeDockerImage% bash -c "node_modules/.bin/eslint assetsSource/js/* assetsSource/tests/*"
    exit /b 0
)

if "%cmd%" == "cypress" (
    docker run -it -v %cd%:/e2e -w /e2e -e CYPRESS_baseUrl=https://buzzingpixel.localtest.me:26087/ --network=host %cypressDockerImage%
    exit /b 0
)

if "%cmd%" == "cypress-interactive" (
    .\platform\node_modules\.bin\cypress open
    exit /b 0
)

echo Specified command not found
exit /b 1
