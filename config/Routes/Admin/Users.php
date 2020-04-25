<?php

declare(strict_types=1);

use App\Http\Admin\Users\GetAdminSearchUsersDisplayAction;
use App\Http\Admin\Users\GetAdminUserCreateAction;
use App\Http\Admin\Users\GetAdminUserEditAction;
use App\Http\Admin\Users\GetAdminUsersDisplayAction;
use App\Http\Admin\Users\GetAdminUserViewAction;
use App\Http\Admin\Users\PostAdminUserCreateAction;
use App\Http\Admin\Users\PostAdminUserEditAction;
use Config\NoOp;
use Slim\Routing\RouteCollectorProxy;

return static function (RouteCollectorProxy $r) : void {
    $r->group('/users', function (RouteCollectorProxy $r) : void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $r->get(
            '[/page/{page:(?!(?:0|1)$)\d+}]',
            GetAdminUsersDisplayAction::class
        );

        $r->get(
            '/search[/page/{page:(?!(?:0|1)$)\d+}]',
            GetAdminSearchUsersDisplayAction::class
        );

        $r->get(
            '/create',
            GetAdminUserCreateAction::class
        );

        $r->post(
            '/create',
            PostAdminUserCreateAction::class
        );

        $r->get(
            '/view/{id}',
            GetAdminUserViewAction::class
        );

        $r->get(
            '/edit/{id}',
            GetAdminUserEditAction::class
        );

        $r->post(
            '/edit/{id}',
            PostAdminUserEditAction::class
        );
    });
};
