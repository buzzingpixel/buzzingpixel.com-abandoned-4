<?php

declare(strict_types=1);

use App\Http\Account\ChangePassword\GetChangePasswordAction;
use App\Http\Account\ChangePassword\PostChangePasswordAction;
use App\Http\Account\GetAccountAction;
use App\Http\Account\Licenses\AuthorizedDomains\GetEditAuthorizedDomainsAction;
use App\Http\Account\Licenses\AuthorizedDomains\PostEditAuthorizedDomainsAction;
use App\Http\Account\Licenses\GetAccountLicensesAction;
use App\Http\Account\Licenses\Notes\GetEditNotesAction;
use App\Http\Account\Licenses\Notes\PostEditNotesAction;
use App\Http\Account\Licenses\View\GetAccountLicenseViewAction;
use App\Http\Account\LogIn\GetLogOutAction;
use App\Http\Account\LogIn\PostLogInAction;
use App\Http\Account\PaymentMethods\Create\GetCreatePaymentMethodAction;
use App\Http\Account\PaymentMethods\Create\PostCreatePaymentMethodAction;
use App\Http\Account\PaymentMethods\GetAccountPaymentMethodAction;
use App\Http\Account\PaymentMethods\GetAccountPaymentMethodsAction;
use App\Http\Account\Profile\GetAccountProfileAction;
use App\Http\Account\Profile\PostAccountProfileEditAction;
use App\Http\Account\Purchases\GetAccountPurchasesAction;
use App\Http\Account\Purchases\Printing\GetAccountPurchasePrintAction;
use App\Http\Account\Purchases\View\GetAccountPurchaseViewAction;
use App\Http\Account\Register\PostRegisterAction;
use App\Http\Account\RequestPasswordReset\Msg\GetMessageAction;
use App\Http\Account\RequestPasswordReset\PostRequestPasswordResetAction;
use App\Http\Account\ResetPasswordWithToken\GetResetPasswordWithTokenAction;
use App\Http\Account\ResetPasswordWithToken\PostResetPasswordWithTokenAction;
use App\HttpRouteMiddleware\RequireLogIn\RequireLogInAction;
use Config\NoOp;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) : void {
    // No auth required
    $app->group('/account', function (RouteCollectorProxy $r) : void {
        // We have to use $this so PHPCS will be happy and not convert to
        // static function. $this is an instance of the DI Container
        $this->get(NoOp::class)();

        $r->post('/register', PostRegisterAction::class);

        $r->post('/log-in', PostLogInAction::class);

        $r->get('/log-out', GetLogOutAction::class);

        $r->post('/log-out', GetLogOutAction::class);

        $r->post(
            '/request-password-reset',
            PostRequestPasswordResetAction::class
        );

        $r->get(
            '/request-password-reset/msg',
            GetMessageAction::class
        );

        $r->get(
            '/reset-pw-with-token/{token}',
            GetResetPasswordWithTokenAction::class
        );

        $r->post(
            '/reset-pw-with-token/{token}',
            PostResetPasswordWithTokenAction::class
        );
    });

    // Auth required
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

        $r->get(
            '/purchases/view/{id}',
            GetAccountPurchaseViewAction::class
        );

        $r->get(
            '/purchases/print/{id}',
            GetAccountPurchasePrintAction::class
        );

        $r->get(
            '/profile',
            GetAccountProfileAction::class
        );

        $r->post(
            '/profile',
            PostAccountProfileEditAction::class
        );

        $r->get(
            '/payment-methods',
            GetAccountPaymentMethodsAction::class
        );

        $r->get(
            '/payment-methods/create',
            GetCreatePaymentMethodAction::class,
        );

        $r->post(
            '/payment-methods/create',
            PostCreatePaymentMethodAction::class,
        );

        $r->get(
            '/payment-methods/{id}',
            GetAccountPaymentMethodAction::class
        );

        $r->get(
            '/change-password',
            GetChangePasswordAction::class
        );

        $r->post(
            '/change-password',
            PostChangePasswordAction::class
        );
    })->add(RequireLogInAction::class);
};
