<?php

declare(strict_types=1);

namespace App\Http\Account\RequestPasswordReset;

use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function assert;
use function is_array;

class PostRequestPasswordResetAction
{
    private UserApi $userApi;
    private PostRequestPasswordResetResponder $responder;

    public function __construct(
        UserApi $userApi,
        PostRequestPasswordResetResponder $responder
    ) {
        $this->userApi   = $userApi;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $post = $request->getParsedBody();
        assert(is_array($post));

        $emailAddress = (string) ($post['email_address'] ?? '');

        $user = $this->userApi->fetchUserByEmailAddress($emailAddress);

        if ($user === null) {
            return ($this->responder)();
        }

        // TODO: throttle the amount of password resets user can attempt
        // query for the amount of tokens that are currently in existence
        // if there are 5 or more, don't send a token

        $this->userApi->requestPasswordResetEmail($user);

        return ($this->responder)();
    }
}
