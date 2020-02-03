<?php

declare(strict_types=1);

use App\Http\Admin\Software\GetAdminSoftwareAction;
use App\Http\Admin\Software\GetAdminSoftwareAddVersion;
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
use Slim\Routing\RouteCollectorProxy;

return static function (RouteCollectorProxy $r) : void {
    $r->get('/software', GetAdminSoftwareAction::class);

    $r->get('/software/create', GetAdminSoftwareCreateAction::class);

    $r->post('/software/create', PostAdminSoftwareCreateAction::class);

    $r->get('/software/view/{slug}', GetAdminSoftwareViewAction::class);

    $r->get('/software/edit/{slug}', GetAdminSoftwareEditAction::class);

    $r->post('/software/edit/{slug}', PostAdminSoftwareEditAction::class);

    $r->get('/software/version/edit/{id}', GetAdminSoftwareVersionEditAction::class);

    $r->post('/software/version/edit/{id}', PostAdminSoftwareVersionEditAction::class);

    $r->post('/software/delete/{id}', PostAdminSoftwareDeleteAction::class);

    $r->post('/software/version/delete/{id}', PostAdminSoftwareVersionDeleteAction::class);

    $r->get('/software/{slug}/add-version', GetAdminSoftwareAddVersion::class);

    $r->post('/software/{slug}/add-version', PostAdminSoftwareAddVersionAction::class);
};
