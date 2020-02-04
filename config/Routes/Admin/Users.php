<?php

declare(strict_types=1);

use App\Http\Admin\Users\GetSearchUsersDisplayAction;
use App\Http\Admin\Users\GetUserCreateAction;
use App\Http\Admin\Users\GetUsersDisplayAction;
use App\Http\Admin\Users\PostUserCreateAction;
use Config\NoOp;
use Slim\Routing\RouteCollectorProxy;

return static function (RouteCollectorProxy $r) : void {
    $r->group('/users', function (RouteCollectorProxy $r) : void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $r->get('[/page/{page:(?!(?:0|1)$)\d+}]', GetUsersDisplayAction::class);

        $r->get('/search[/page/{page:(?!(?:0|1)$)\d+}]', GetSearchUsersDisplayAction::class);

        $r->get('/create', GetUserCreateAction::class);

        $r->post('/create', PostUserCreateAction::class);
    });
};
