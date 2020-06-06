$tasks.Add('build',@{
    description="Builds docker containers";
    arguments = @()
    script = {
        Invoke-Expression "docker-compose $composeFiles -p buzzingpixel build"
    }
})
