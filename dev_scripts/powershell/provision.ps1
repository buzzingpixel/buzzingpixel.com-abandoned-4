$tasks.Add('provision',@{
    description="Runs provisioning";
    arguments = @()
    script = {
        Invoke-Expression 'docker exec -it --user root --workdir /opt/project buzzingpixel-php bash -c "composer install"'

        Invoke-Expression 'docker run -it -v $("$(Get-Location):/app".Trim()) -v buzzingpixel_node-modules-volume:/app/node_modules -v buzzingpixel_yarn-cache-volume:/usr/local/share/.cache/yarn -w /app $nodeDockerImage bash -c "yarn"'

        Invoke-Expression 'docker run -it -v $("$(Get-Location):/app".Trim()) -v buzzingpixel_node-modules-volume:/app/node_modules -v buzzingpixel_yarn-cache-volume:/usr/local/share/.cache/yarn -w /app $nodeDockerImage bash -c "yarn run fab --build-only"'

        Invoke-Expression 'cd platform; yarn; cd ..'

        Invoke-Expression 'docker exec -it --user root --workdir /opt/project buzzingpixel-php bash -c "php cli app-setup:setup-docker-database"'
    }
})
