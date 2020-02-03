<?php

declare(strict_types=1);

use App\Http\Account\LogIn\GetLogOutAction;
use App\Http\Account\LogIn\PostLogInAction;
use App\Http\Account\Register\PostRegisterAction;
use Config\NoOp;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) : void {
    $app->group('/account', function (RouteCollectorProxy $r) : void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $r->post('/register', PostRegisterAction::class);

        $r->post('/log-in', PostLogInAction::class);

        $r->get('/log-out', GetLogOutAction::class);

        $r->post('/log-out', GetLogOutAction::class);
    });
};
