<?php

declare(strict_types=1);

use App\Http\Admin\Analytics\GetAnalyticsViewAction;
use Config\NoOp;
use Slim\Routing\RouteCollectorProxy;

return static function (RouteCollectorProxy $r): void {
    $r->group('/analytics', function (RouteCollectorProxy $r): void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        /** @phpstan-ignore-next-line */
        $this->get(NoOp::class)();

        $r->get('', GetAnalyticsViewAction::class);
    });
};
