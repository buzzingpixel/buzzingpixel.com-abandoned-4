<?php

declare(strict_types=1);

namespace App\Http\Account\PaymentMethods;

use App\Users\Models\UserModel;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

use function assert;

class GetAccountPaymentMethodAction
{
    private GetAccountPaymentMethodResponder $responder;
    private UserApi $userApi;

    public function __construct(
        GetAccountPaymentMethodResponder $responder,
        UserApi $userApi
    ) {
        $this->responder = $responder;
        $this->userApi   = $userApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = (string) $request->getAttribute('id');

        $user = $this->userApi->fetchLoggedInUser();

        assert($user instanceof UserModel);

        $card = $this->userApi->fetchUserCardById($user, $id);

        if ($card === null) {
            throw new HttpNotFoundException($request);
        }

        /** @psalm-suppress PossiblyNullArgument */
        return ($this->responder)($card);
    }
}
