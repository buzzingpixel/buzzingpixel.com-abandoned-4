<?php

declare(strict_types=1);

namespace App\Http\Account\PaymentMethods;

use App\Users\Models\UserModel;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function assert;

class GetAccountPaymentMethodsAction
{
    private GetAccountPaymentMethodsResponder $responder;
    private UserApi $userApi;

    public function __construct(
        GetAccountPaymentMethodsResponder $responder,
        UserApi $userApi
    ) {
        $this->responder = $responder;
        $this->userApi   = $userApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        $user = $this->userApi->fetchLoggedInUser();

        assert($user instanceof UserModel);

        return ($this->responder)(
            $this->userApi->fetchUserCards($user),
        );
    }
}
