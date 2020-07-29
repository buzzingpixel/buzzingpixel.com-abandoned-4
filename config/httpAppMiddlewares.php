<?php

declare(strict_types=1);

use App\HttpAppMiddleware\CsrfInjectionMiddleware;
use App\HttpAppMiddleware\HoneyPotMiddleware;
use App\HttpAppMiddleware\MinifyMiddleware;
use App\HttpAppMiddleware\StaticCacheMiddleware;
use Slim\App;
use Slim\Csrf\Guard as CsrfGuard;

return static function (App $app): void {
    $app->add(MinifyMiddleware::class);
    $app->add(HoneyPotMiddleware::class);
    $app->add(StaticCacheMiddleware::class);
    $app->add(CsrfGuard::class);
    $app->add(CsrfInjectionMiddleware::class);
};
