<?php

declare(strict_types=1);

use App\Http\Account\LogIn\PostLogInAction;
use App\Http\Account\Register\PostRegisterAction;
use App\Http\Admin\GetAdminAction;
use App\Http\Home\GetHomeAction;
use App\Http\Software\GetChangelogAction;
use App\Http\Software\GetChangelogItemAction;
use App\Http\Software\GetChangelogRawAction;
use App\Http\Software\GetDocumentationPageAction;
use App\Http\Software\GetSoftwareAction;
use App\Http\Tinker\GetTinkerAction;
use App\HttpRouteMiddleware\RequireAdmin\RequireAdminAction;
use App\HttpRouteMiddleware\RequireLogIn\RequireLogInAction;
use Slim\App;

return static function (App $app) : void {
    // Match all integers except 0 or 1
    // {page:(?!(?:0|1)$)\d+}

    // Match anything except a forward slash
    // {slug:[^\/]+}

    // Tinker
    $app->get('/tinker', GetTinkerAction::class);

    // Home
    $app->get('/', GetHomeAction::class);

    // Admin
    // phpcs:disable
    $app->group('/admin', function () use ($app) : void {
        $app->get('/admin', GetAdminAction::class);
    })->add(RequireAdminAction::class)
        ->add(RequireLogInAction::class);
    // phpcs:enable

    // Account
    $app->post('/account/register', PostRegisterAction::class);
    $app->post('/account/log-in', PostLogInAction::class);

    // Ansel Craft
    $app->get(
        '/software/ansel-craft',
        GetSoftwareAction::class
    );
    $app->get(
        '/software/ansel-craft/changelog[/page/{page:(?!(?:0|1)$)\d+}]',
        GetChangelogAction::class
    );
    $app->get(
        '/software/ansel-craft/changelog/raw',
        GetChangelogRawAction::class
    );
    $app->get(
        '/software/ansel-craft/changelog/{slug:[^\/]+}',
        GetChangelogItemAction::class
    );
    $app->get(
        '/software/ansel-craft/{versionString:documentation}[/{pageSlug:[^\/]+}]',
        GetDocumentationPageAction::class
    );
    $app->get(
        '/software/ansel-craft/{versionString:documentation-[^\/]+}[/{pageSlug:[^\/]+}]',
        GetDocumentationPageAction::class
    );

    // Ansel EE
    $app->get(
        '/software/ansel-ee',
        GetSoftwareAction::class
    );
    $app->get(
        '/software/ansel-ee/changelog[/page/{page:(?!(?:0|1)$)\d+}]',
        GetChangelogAction::class
    );
    $app->get(
        '/software/ansel-ee/changelog/raw',
        GetChangelogRawAction::class
    );
    $app->get(
        '/software/ansel-ee/changelog/{slug:[^\/]+}',
        GetChangelogItemAction::class
    );
    $app->get(
        '/software/ansel-ee/{versionString:documentation}[/{pageSlug:[^\/]+}]',
        GetDocumentationPageAction::class
    );
    $app->get(
        '/software/ansel-ee/{versionString:documentation-[^\/]+}[/{pageSlug:[^\/]+}]',
        GetDocumentationPageAction::class
    );
};
