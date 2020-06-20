<?php

declare(strict_types=1);

namespace App\Http\Account\PaymentMethods\Delete;

use App\Users\Models\LoggedInUser;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class PostDeletePaymentMethodAction
{
    private LoggedInUser $user;
    private UserApi $userApi;
    private PostDeletePaymentMethodResponder $responder;

    public function __construct(
        LoggedInUser $user,
        UserApi $userApi,
        PostDeletePaymentMethodResponder $responder
    ) {
        $this->user      = $user;
        $this->userApi   = $userApi;
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = (string) $request->getAttribute('id');

        $card = $this->userApi->fetchUserCardById(
            $this->user->model(),
            $id
        );

        if ($card === null) {
            throw new HttpNotFoundException($request);
        }

        return ($this->responder)(
            $this->userApi->deleteUserCard($card)
        );
    }
}
