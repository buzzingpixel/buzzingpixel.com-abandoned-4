$tasks.Add('up',@{
    description="Starts docker containers";
    arguments = @()
    script = {
        Invoke-Expression "docker-compose $composeFiles -p buzzingpixel up -d"
    }
})
