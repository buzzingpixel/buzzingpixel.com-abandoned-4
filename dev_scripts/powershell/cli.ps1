$tasks.Add('cli',@{
    description="Invokes application CLI";
    arguments = @()
    script = {
        Invoke-Expression 'docker exec -it --user root --workdir /opt/project buzzingpixel-php bash -c "php cli $commandArgs"'
    }
})
