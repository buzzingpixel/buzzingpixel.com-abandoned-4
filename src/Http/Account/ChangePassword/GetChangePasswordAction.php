<?php

declare(strict_types=1);

namespace App\Http\Account\ChangePassword;

use App\Users\Models\UserModel;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function assert;

class GetChangePasswordAction
{
    private UserApi $userApi;
    private GetChangePasswordResponder $responder;

    public function __construct(
        UserApi $userApi,
        GetChangePasswordResponder $responder
    ) {
        $this->userApi   = $userApi;
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        $user = $this->userApi->fetchLoggedInUser();

        assert($user instanceof UserModel);

        return ($this->responder)($user);
    }
}
