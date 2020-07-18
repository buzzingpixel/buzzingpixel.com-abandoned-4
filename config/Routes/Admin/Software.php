<?php

declare(strict_types=1);

use App\Http\Admin\Software\GetAdminSoftwareAction;
use App\Http\Admin\Software\GetAdminSoftwareAddVersionAction;
use App\Http\Admin\Software\GetAdminSoftwareCreateAction;
use App\Http\Admin\Software\GetAdminSoftwareEditAction;
use App\Http\Admin\Software\GetAdminSoftwareVersionEditAction;
use App\Http\Admin\Software\GetAdminSoftwareViewAction;
use App\Http\Admin\Software\PostAdminSoftwareAddVersionAction;
use App\Http\Admin\Software\PostAdminSoftwareCreateAction;
use App\Http\Admin\Software\PostAdminSoftwareDeleteAction;
use App\Http\Admin\Software\PostAdminSoftwareEditAction;
use App\Http\Admin\Software\PostAdminSoftwareVersionDeleteAction;
use App\Http\Admin\Software\PostAdminSoftwareVersionEditAction;
use Config\NoOp;
use Slim\Routing\RouteCollectorProxy;

return static function (RouteCollectorProxy $r): void {
    $r->group('/software', function (RouteCollectorProxy $r): void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $r->get(
            '',
            GetAdminSoftwareAction::class
        );

        $r->get(
            '/create',
            GetAdminSoftwareCreateAction::class
        );

        $r->post(
            '/create',
            PostAdminSoftwareCreateAction::class
        );

        $r->get(
            '/view/{id}',
            GetAdminSoftwareViewAction::class
        );

        $r->get(
            '/edit/{id}',
            GetAdminSoftwareEditAction::class
        );

        $r->post(
            '/edit/{id}',
            PostAdminSoftwareEditAction::class
        );

        $r->get(
            '/version/edit/{id}',
            GetAdminSoftwareVersionEditAction::class
        );

        $r->post(
            '/version/edit/{id}',
            PostAdminSoftwareVersionEditAction::class
        );

        $r->post(
            '/delete/{id}',
            PostAdminSoftwareDeleteAction::class
        );

        $r->post(
            '/version/delete/{id}',
            PostAdminSoftwareVersionDeleteAction::class
        );

        $r->get(
            '/{id}/add-version',
            GetAdminSoftwareAddVersionAction::class
        );

        $r->post(
            '/{id}/add-version',
            PostAdminSoftwareAddVersionAction::class
        );
    });
};
