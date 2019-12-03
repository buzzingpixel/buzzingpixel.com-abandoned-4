<?php

declare(strict_types=1);

namespace App\Http\Account\Register;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\Services\SaveUser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostRegisterAction
{
    /** @var PostRegisterResponder */
    private $responder;
    /** @var SaveUser */
    private $saveUser;

    public function __construct(
        PostRegisterResponder $responder,
        SaveUser $saveUser
    ) {
        $this->responder = $responder;
        $this->saveUser  = $saveUser;
    }

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $postData = $request->getParsedBody();

        $emailAddress = (string) ($postData['email_address'] ?? '');

        $password = (string) ($postData['password'] ?? '');

        $confirmPassword = (string) ($postData['confirm_password'] ?? '');

        $redirectTo = (string) ($postData['redirect_to'] ?? '/');

        if ($password !== $confirmPassword) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'Password confirmation must match password',
                        'active' => 'register',
                        'inputs' => [
                            'password' => 'Password must match Password Confirmation',
                            'confirmPassword' => 'Password Confirmation must match password',
                        ],
                    ]
                ),
                $redirectTo
            );
        }

        $user = new UserModel([
            'emailAddress' => $emailAddress,
            'newPassword' => $password,
        ]);

        $saveUserPayload = ($this->saveUser)($user);

        return ($this->responder)(
            new Payload(
                $saveUserPayload->getStatus(),
                [
                    'message' => $saveUserPayload->getResult()['message'] ?? '',
                    'active' => 'register',
                    'inputs' => $saveUserPayload->getResult(),
                ]
            ),
            $redirectTo
        );
    }
}
