<?php

declare(strict_types=1);

namespace App\Http\Cart;

use App\Cart\CartApi;
use App\Users\UserApi;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetCartAction
{
    private CartApi $cartApi;
    private UserApi $userApi;
    private GetCartResponder $responder;

    public function __construct(
        CartApi $cartApi,
        UserApi $userApi,
        GetCartResponder $responder
    ) {
        $this->cartApi   = $cartApi;
        $this->userApi   = $userApi;
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        $user = $this->userApi->fetchLoggedInUser();

        if ($user === null) {
            return ($this->responder)(
                $this->cartApi->fetchCurrentUserCart(),
            );
        }

        return ($this->responder)(
            $this->cartApi->fetchCurrentUserCart(),
            $this->userApi->fetchUserCards($user),
        );
    }
}
