<?php

declare(strict_types=1);

use App\Http\Home\GetHomeAction;
use App\Http\Software\GetSoftwareAction;
use Slim\App;

return static function (App $app) : void {
    // Home
    $app->get('/', GetHomeAction::class);

    // Ansel Craft
    $app->get('/software/ansel-craft', GetSoftwareAction::class);
};
