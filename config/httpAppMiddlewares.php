<?php

declare(strict_types=1);

use Slim\App;
use Slim\Csrf\Guard as CsrfGuard;

return static function (App $app) : void {
    $app->add(CsrfGuard::class);
};
