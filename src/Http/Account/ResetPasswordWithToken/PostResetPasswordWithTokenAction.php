<?php

declare(strict_types=1);

namespace App\Http\Account\ResetPasswordWithToken;

use App\Payload\Payload;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

use function assert;
use function is_array;

class PostResetPasswordWithTokenAction
{
    private UserApi $userApi;
    private PostResetPasswordWithTokenResponder $responder;

    public function __construct(
        UserApi $userApi,
        PostResetPasswordWithTokenResponder $responder
    ) {
        $this->userApi   = $userApi;
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $token = (string) $request->getAttribute('token');

        $user = $this->userApi->fetchUserByResetToken($token);

        if ($user === null) {
            throw new HttpNotFoundException($request);
        }

        $postData = $request->getParsedBody();

        assert(is_array($postData));

        $password = (string) ($postData['password'] ?? '');

        $confirmPassword = (string) ($postData['confirm_password'] ?? '');

        if ($password !== $confirmPassword) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'Password confirmation must match password',
                        'inputs' => [
                            'password' => 'Password must match Password Confirmation',
                            'confirmPassword' => 'Password Confirmation must match password',
                        ],
                    ]
                ),
                $token
            );
        }

        if ($password === '') {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'Password cannot be empty',
                        'inputs' => ['password' => 'Password cannot be empty'],
                    ]
                ),
                $token
            );
        }

        $savePayload = $this->userApi->resetPasswordByToken(
            $token,
            $password
        );

        return ($this->responder)(
            new Payload(
                $savePayload->getStatus(),
                [
                    'message' => $savePayload->getResult()['message'] ?? '',
                    'inputs' => $savePayload->getResult(),
                ]
            ),
            $token
        );
    }
}
