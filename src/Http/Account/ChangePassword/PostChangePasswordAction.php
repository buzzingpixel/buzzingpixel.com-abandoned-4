<?php

declare(strict_types=1);

namespace App\Http\Account\ChangePassword;

use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function is_array;

class PostChangePasswordAction
{
    private PostChangePasswordResponder $responder;
    private UserApi $userApi;

    public function __construct(
        PostChangePasswordResponder $responder,
        UserApi $userApi
    ) {
        $this->responder = $responder;
        $this->userApi   = $userApi;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->userApi->fetchLoggedInUser();

        assert($user instanceof UserModel);

        $postData = $request->getParsedBody();

        assert(is_array($postData));

        $password = (string) ($postData['password'] ?? '');

        $validPassword = $this->userApi->validateUserPassword(
            $user,
            $password,
            false
        );

        if (! $validPassword) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'The password you submitted is invalid',
                        'inputMessages' => ['password' => 'Invalid password'],
                    ]
                )
            );
        }

        $newPassword = (string) ($postData['new_password'] ?? '');

        $confirmNewPassword = (string) ($postData['confirm_new_password'] ?? '');

        if ($newPassword !== $confirmNewPassword) {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'Password confirmation must match new password',
                        'inputMessages' => [
                            'new_password' => 'New Password must match Confirmation',
                            'confirm_new_password' => 'Password Confirmation must match New Password',
                        ],
                    ]
                )
            );
        }

        if ($newPassword === '') {
            return ($this->responder)(
                new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'Password cannot be empty',
                        'inputMessages' => ['new_password' => 'Password cannot be empty'],
                    ]
                )
            );
        }

        $user->newPassword = $newPassword;

        $savePayload = $this->userApi->saveUser($user);

        $result = $savePayload->getResult();

        if (isset($result['password'])) {
            /** @psalm-suppress MixedAssignment */
            $result['new_password'] = $result['password'];

            unset($result['password']);
        }

        if ($savePayload->getStatus() === Payload::STATUS_UPDATED) {
            $this->userApi->logCurrentUserOut();
        }

        return ($this->responder)(
            new Payload(
                $savePayload->getStatus(),
                [
                    'message' => $savePayload->getResult()['message'] ?? '',
                    'inputMessages' => $result,
                ]
            )
        );
    }
}
