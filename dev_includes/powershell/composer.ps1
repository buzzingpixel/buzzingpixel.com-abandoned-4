$tasks.Add('composer',@{
    description="Runs composer commands in Docker environment";
    arguments = @("install (etc)")
    script = {
        Invoke-Expression 'docker run -it -v $("$(Get-Location):/app".Trim()) -v buzzingpixel_composer-home-volume:/composer-home-volume --env COMPOSER_HOME=/composer-home-volume -w /app $composerDockerImage bash -c "composer $commandArgs"'
    }
})
