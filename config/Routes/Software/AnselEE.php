<?php

declare(strict_types=1);

use App\Http\Software\GetChangelogAction;
use App\Http\Software\GetChangelogItemAction;
use App\Http\Software\GetChangelogRawAction;
use App\Http\Software\GetDocumentationPageAction;
use App\Http\Software\GetSoftwareAction;
use Slim\Routing\RouteCollectorProxy;

return static function (RouteCollectorProxy $r): void {
    $r->get(
        '/ansel-ee',
        GetSoftwareAction::class
    );

    $r->get(
        '/ansel-ee/changelog[/page/{page:(?!(?:0|1)$)\d+}]',
        GetChangelogAction::class
    );

    $r->get(
        '/ansel-ee/changelog/raw',
        GetChangelogRawAction::class
    );

    $r->get(
        '/ansel-ee/changelog/{slug:[^\/]+}',
        GetChangelogItemAction::class
    );

    $r->get(
        '/ansel-ee/{versionString:documentation}[/{pageSlug:[^\/]+}]',
        GetDocumentationPageAction::class
    );

    $r->get(
        '/ansel-ee/{versionString:documentation-[^\/]+}[/{pageSlug:[^\/]+}]',
        GetDocumentationPageAction::class
    );
};
