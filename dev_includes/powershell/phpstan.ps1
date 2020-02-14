$tasks.Add('phpstan',@{
    description="Runs PHPStan";
    arguments = @()
    script = {
        # Run in Docker (disabled for no because of performance)
        # Invoke-Expression 'docker run -it -v $("$(Get-Location):/app".Trim()) -w /app buzzingpixel:php-dev bash -c "php -d memory_limit=4G /app/vendor/phpstan/phpstan/bin/phpstan analyse config public/index.php src tests cli"'

        # Run locally
        Invoke-Expression 'php -d memory_limit=4G vendor/phpstan/phpstan/bin/phpstan analyse config public/index.php src tests cli'
    }
})
