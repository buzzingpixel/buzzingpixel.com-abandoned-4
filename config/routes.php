<?php

declare(strict_types=1);

use App\Http\Home\GetHomeAction;
use App\Http\Software\GetChangelogAction;
use App\Http\Software\GetSoftwareAction;
use Slim\App;

return static function (App $app) : void {
    // Match all integers except 0 or 1
    // (?!(?:0|1)$)\d+;

    // Home
    $app->get('/', GetHomeAction::class);

    // Ansel Craft
    $app->get('/software/ansel-craft', GetSoftwareAction::class);
    $app->get('/software/ansel-craft/changelog[/page/{page:(?!(?:0|1)$)\d+}]', GetChangelogAction::class);
};
