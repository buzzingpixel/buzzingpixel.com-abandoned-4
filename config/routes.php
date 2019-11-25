<?php

declare(strict_types=1);

use App\Http\Home\GetHomeAction;
use App\Http\Software\GetChangelogAction;
use App\Http\Software\GetChangelogItemAction;
use App\Http\Software\GetChangelogRawAction;
use App\Http\Software\GetDocumentationPageAction;
use App\Http\Software\GetSoftwareAction;
use App\Http\Tinker\GetTinkerAction;
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
