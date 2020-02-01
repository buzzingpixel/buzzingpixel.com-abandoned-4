<?php

declare(strict_types=1);

use App\Http\Account\LogIn\GetLogOutAction;
use App\Http\Account\LogIn\PostLogInAction;
use App\Http\Account\Register\PostRegisterAction;
use App\Http\Admin\GetAdminAction;
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
use App\Http\Ajax\User\GetPayloadAction;
use App\Http\Cart\GetAddToCartAction;
use App\Http\Cart\GetCartAction;
use App\Http\Cart\GetCartUpdateQuantityAction;
use App\Http\Cart\GetClearCartAction;
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
    // phpcs:enable
        $app->get('/admin', GetAdminAction::class);
        $app->get(
            '/admin/software',
            GetAdminSoftwareAction::class,
        );
        $app->get(
            '/admin/software/create',
            GetAdminSoftwareCreateAction::class,
        );
        $app->post(
            '/admin/software/create',
            PostAdminSoftwareCreateAction::class,
        );
        $app->get(
            '/admin/software/view/{slug}',
            GetAdminSoftwareViewAction::class,
        );
        $app->get(
            '/admin/software/edit/{slug}',
            GetAdminSoftwareEditAction::class,
        );
        $app->post(
            '/admin/software/edit/{slug}',
            PostAdminSoftwareEditAction::class,
        );
        $app->get(
            '/admin/software/version/edit/{id}',
            GetAdminSoftwareVersionEditAction::class,
        );
        $app->post(
            '/admin/software/version/edit/{id}',
            PostAdminSoftwareVersionEditAction::class,
        );
        $app->post(
            '/admin/software/delete/{id}',
            PostAdminSoftwareDeleteAction::class,
        );
        $app->post(
            '/admin/software/version/delete/{id}',
            PostAdminSoftwareVersionDeleteAction::class,
        );
        $app->get(
            '/admin/software/{slug}/add-version',
            GetAdminSoftwareAddVersion::class,
        );
        $app->post(
            '/admin/software/{slug}/add-version',
            PostAdminSoftwareAddVersionAction::class
        );
    })->add(RequireAdminAction::class)
    ->add(RequireLogInAction::class);

    // Account
    $app->post('/account/register', PostRegisterAction::class);
    $app->post('/account/log-in', PostLogInAction::class);
    $app->get('/account/log-out', GetLogOutAction::class);
    $app->post('/account/log-out', GetLogOutAction::class);

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

    // Cart
    $app->get('/cart', GetCartAction::class);
    $app->get('/cart/add/{slug}', GetAddToCartAction::class);
    $app->get(
        '/cart/update-quantity/{slug}/{quantity:\d+}',
        GetCartUpdateQuantityAction::class
    );
    $app->get('/cart/clear', GetClearCartAction::class);

    // Ajax
    $app->get('/ajax/user/payload', GetPayloadAction::class);
};
