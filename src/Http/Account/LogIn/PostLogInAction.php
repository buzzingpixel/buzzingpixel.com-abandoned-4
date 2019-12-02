<?php

declare(strict_types=1);

namespace App\Http\Account\LogIn;

use App\Payload\Payload;
use App\Users\UserApi;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PostLogInAction
{
    /** @var PostLogInResponder */
    private $responder;
    /** @var UserApi */
    private $userApi;

    public function __construct(
        PostLogInResponder $responder,
        UserApi $userApi
    ) {
        $this->responder = $responder;
        $this->userApi   = $userApi;
    }

    public function __invoke(RequestInterface $request) : ResponseInterface
    {
        $postData = $request->getParsedBody();

        $emailAddress = $postData['email_address'] ?? '';

        $password = $postData['password'] ?? '';

        $redirectTo = $postData['redirect_to'] ?? '/';

        $user = $this->userApi->fetchUserByEmailAddress($emailAddress);

        $errorPayload = new Payload(
            Payload::STATUS_ERROR,
            ['message' => 'Unable to log you in with that email address and password']
        );

        if ($user === null) {
            return ($this->responder)(
                $errorPayload,
                $redirectTo
            );
        }

        $logInPayload = $this->userApi->logUserIn(
            $user,
            $password
        );

        if ($logInPayload->getStatus() !== Payload::STATUS_SUCCESSFUL) {
            return ($this->responder)(
                $errorPayload,
                $redirectTo
            );
        }

        return ($this->responder)($logInPayload, $redirectTo);
    }
}
