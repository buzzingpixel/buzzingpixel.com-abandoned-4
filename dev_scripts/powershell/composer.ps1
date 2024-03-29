$tasks.Add('composer',@{
    description="Runs composer commands in Docker environment";
    arguments = @("install (etc)")
    script = {
        Invoke-Expression 'docker exec -it --user root --workdir /opt/project buzzingpixel-php bash -c "composer $commandArgs"'
    }
})
