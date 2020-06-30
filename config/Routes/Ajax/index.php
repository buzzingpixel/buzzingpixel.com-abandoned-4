<?php

declare(strict_types=1);

use App\Http\Ajax\Cart\GetCartPayloadAction;
use App\Http\Ajax\User\GetUserPayloadAction;
use Config\NoOp;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {
    $app->group('/ajax', function (RouteCollectorProxy $r): void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $r->get(
            '/user/payload',
            GetUserPayloadAction::class
        );

        $r->get(
            '/cart/payload',
            GetCartPayloadAction::class,
        );
    });
};
