<?php

declare(strict_types=1);

namespace App\Http\Cart;

use App\Cart\CartApi;
use App\Users\Models\LoggedInUser;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetCartAction
{
    private CartApi $cartApi;
    private LoggedInUser $user;
    private UserApi $userApi;
    private GetCartResponder $responder;

    public function __construct(
        CartApi $cartApi,
        LoggedInUser $user,
        UserApi $userApi,
        GetCartResponder $responder
    ) {
        $this->cartApi   = $cartApi;
        $this->user      = $user;
        $this->userApi   = $userApi;
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        return ($this->responder)(
            $this->cartApi->fetchCurrentUserCart(),
            $this->userApi->fetchUserCards($this->user->model()),
        );
    }
}
