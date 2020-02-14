$tasks.Add('cypress',@{
    description="Runs Cypress testing suite";
    arguments = @()
    script = {
        Invoke-Expression 'docker run -it -v $("$(Get-Location):/e2e".Trim()) -w /e2e -e CYPRESS_baseUrl=https://buzzingpixel.localtest.me:26087/ --network=host $cypressDockerImage'
    }
})

$tasks.Add('cypress-interactive',@{
    description="Runs Cypress testing suite interactively";
    arguments = @()
    script = {
        Invoke-Expression '.\platform\node_modules\.bin\cypress open'
    }
})
