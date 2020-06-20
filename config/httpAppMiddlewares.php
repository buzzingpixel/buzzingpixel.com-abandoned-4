<?php

declare(strict_types=1);

use App\HttpAppMiddleware\HoneyPotMiddleware;
use App\HttpAppMiddleware\MinifyMiddleware;
use Slim\App;
use Slim\Csrf\Guard as CsrfGuard;

return static function (App $app): void {
    $app->add(MinifyMiddleware::class);
    $app->add(HoneyPotMiddleware::class);
    $app->add(CsrfGuard::class);
};
