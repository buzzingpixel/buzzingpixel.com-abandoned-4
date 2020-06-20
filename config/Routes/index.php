<?php

declare(strict_types=1);

use App\Http\Home\GetHomeAction;
use App\Http\Tinker\GetTinkerAction;
use Slim\App;

return static function (App $app): void {
    // Match all integers except 0 or 1
    // {page:(?!(?:0|1)$)\d+}

    // Match anything except a forward slash
    // {slug:[^\/]+}

    // Tinker
    $app->get('/tinker', GetTinkerAction::class);

    // Home
    $app->get('/', GetHomeAction::class);

    // Admin
    $adminRoutes = require __DIR__ . '/Admin/index.php';
    $adminRoutes($app);

    // Account
    $accountRoutes = require __DIR__ . '/Account/index.php';
    $accountRoutes($app);

    // Software
    $softwareRoutes = require __DIR__ . '/Software/index.php';
    $softwareRoutes($app);

    // Cart
    $cartRoutes = require __DIR__ . '/Cart/index.php';
    $cartRoutes($app);

    // Ajax
    $ajaxRoutes = require __DIR__ . '/Ajax/index.php';
    $ajaxRoutes($app);
};
