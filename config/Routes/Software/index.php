<?php

declare(strict_types=1);

use Config\NoOp;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) : void {
    $app->group('/software', function (RouteCollectorProxy $r) : void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $anselCraftRoutes = require __DIR__ . '/AnselCraft.php';
        $anselCraftRoutes($r);

        $anselEERoutes = require __DIR__ . '/AnselEE.php';
        $anselEERoutes($r);
    });
};
