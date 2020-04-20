<?php

declare(strict_types=1);

namespace App\Http\Account\RequestPasswordReset;

use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
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

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $post = $request->getParsedBody();

        assert(is_array($post));

        $emailAddress = (string) ($post['email_address'] ?? '');

        $user = $this->userApi->fetchUserByEmailAddress($emailAddress);

        if ($user === null) {
            return ($this->responder)();
        }

        if ($this->userApi->fetchTotalUserResetTokens($user) > 5) {
            return ($this->responder)();
        }

        $this->userApi->requestPasswordResetEmail($user);

        return ($this->responder)();
    }
}
