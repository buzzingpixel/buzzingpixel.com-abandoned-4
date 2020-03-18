<?php

declare(strict_types=1);

use App\Http\Account\GetAccountAction;
use App\Http\Account\Licenses\AuthorizedDomains\GetEditAuthorizedDomainsAction;
use App\Http\Account\Licenses\AuthorizedDomains\PostEditAuthorizedDomainsAction;
use App\Http\Account\Licenses\GetAccountLicensesAction;
use App\Http\Account\Licenses\Notes\GetEditNotesAction;
use App\Http\Account\Licenses\Notes\PostEditNotesAction;
use App\Http\Account\Licenses\View\GetAccountLicenseViewAction;
use App\Http\Account\LogIn\GetLogOutAction;
use App\Http\Account\LogIn\PostLogInAction;
use App\Http\Account\Purchases\GetAccountPurchasesAction;
use App\Http\Account\Register\PostRegisterAction;
use App\HttpRouteMiddleware\RequireLogIn\RequireLogInAction;
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

    $app->group('/account', function (RouteCollectorProxy $r) : void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $r->get('', GetAccountAction::class);

        $r->get('/licenses', GetAccountLicensesAction::class);

        $r->get(
            '/licenses/view/{id}',
            GetAccountLicenseViewAction::class
        );

        $r->get(
            '/licenses/authorized-domains/{id}',
            GetEditAuthorizedDomainsAction::class
        );

        $r->post(
            '/licenses/authorized-domains/{id}',
            PostEditAuthorizedDomainsAction::class
        );

        $r->get(
            '/licenses/notes/{id}',
            GetEditNotesAction::class
        );

        $r->post(
            '/licenses/notes/{id}',
            PostEditNotesAction::class
        );

        $r->get(
            '/purchases',
            GetAccountPurchasesAction::class
        );
    })->add(RequireLogInAction::class);
};
