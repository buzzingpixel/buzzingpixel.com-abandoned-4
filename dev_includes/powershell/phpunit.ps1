$tasks.Add('phpunit',@{
    description="Runs PHPUnit";
    arguments = @()
    script = {
        # Invoke-Expression 'php -d memory_limit=4G vendor/phpunit/phpunit/phpunit --configuration phpunit-no-coverage.xml $commandArgs'

        Invoke-Expression 'docker run -it -v $("$(Get-Location):/app".Trim()) -w /app buzzingpixel:php-dev bash -c "php -d memory_limit=4G /app/vendor/phpunit/phpunit/phpunit --configuration /app/phpunit-no-coverage.xml $commandArgs"'
    }
})

$tasks.Add('phpunit-coverage',@{
    description="Runs PHPUnit with coverage";
    arguments = @()
    script = {
        # Invoke-Expression 'php -d memory_limit=4G vendor/phpunit/phpunit/phpunit --configuration phpunit.xml $commandArgs'

        Invoke-Expression 'docker run -it -v $("$(Get-Location):/app".Trim()) -w /app buzzingpixel:php-dev bash -c "php -d memory_limit=4G /app/vendor/phpunit/phpunit/phpunit --configuration /app/phpunit.xml $commandArgs"'
    }
})
