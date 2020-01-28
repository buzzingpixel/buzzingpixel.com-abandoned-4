<?php

declare(strict_types=1);

namespace App\Http\Cart;

use App\Cart\CartApi;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetCartAction
{
    /** @var CartApi */
    private $cartApi;
    /** @var GetCartResponder */
    private $responder;

    public function __construct(CartApi $cartApi, GetCartResponder $responder)
    {
        $this->cartApi   = $cartApi;
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        return ($this->responder)($this->cartApi->fetchCurrentUserCart());
    }
}
