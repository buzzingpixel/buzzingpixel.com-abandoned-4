<?php

declare(strict_types=1);

use App\Http\Admin\GetAdminAction;
use App\HttpRouteMiddleware\RequireAdmin\RequireAdminAction;
use App\HttpRouteMiddleware\RequireLogIn\RequireLogInAction;
use Config\NoOp;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {
    $app->group('/admin', function (RouteCollectorProxy $r): void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $r->get('', GetAdminAction::class);

        $softwareRoutes = require __DIR__ . '/Software.php';
        $softwareRoutes($r);

        $userRoutes = require __DIR__ . '/Users.php';
        $userRoutes($r);

        $queueRoutes = require __DIR__ . '/Queue.php';
        $queueRoutes($r);

        $analyticsRoutes = require __DIR__ . '/Analytics.php';
        $analyticsRoutes($r);
    })->add(RequireAdminAction::class)
    ->add(RequireLogInAction::class);
};
