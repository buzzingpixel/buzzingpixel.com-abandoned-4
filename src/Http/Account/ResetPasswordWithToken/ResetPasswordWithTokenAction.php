<?php

declare(strict_types=1);

namespace App\Http\Account\ResetPasswordWithToken;

use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class ResetPasswordWithTokenAction
{
    private UserApi $userApi;
    private ResetPasswordWithTokenResponder $responder;

    public function __construct(
        UserApi $userApi,
        ResetPasswordWithTokenResponder $responder
    ) {
        $this->userApi   = $userApi;
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $token = (string) $request->getAttribute('token');

        $user = $this->userApi->fetchUserByResetToken($token);

        if ($user === null) {
            throw new HttpNotFoundException($request);
        }

        return ($this->responder)($user, $token);
    }
}
