<?php

declare(strict_types=1);

use App\Http\Cart\GetAddToCartAction;
use App\Http\Cart\GetCartAction;
use App\Http\Cart\GetCartUpdateQuantityAction;
use App\Http\Cart\GetClearCartAction;
use Config\NoOp;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) : void {
    $app->group('/cart', function (RouteCollectorProxy $r) : void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $r->get(
            '',
            GetCartAction::class
        );

        $r->get(
            '/add/{slug}',
            GetAddToCartAction::class
        );

        $r->get(
            '/update-quantity/{slug}/{quantity:\d+}',
            GetCartUpdateQuantityAction::class
        );

        $r->get(
            '/clear',
            GetClearCartAction::class
        );
    });
};
