<?php

declare(strict_types=1);

namespace App\Http\Cart;

use App\Cart\CartApi;
use function dd;

class GetCartAction
{
    /** @var CartApi */
    private $cartApi;

    public function __construct(CartApi $cartApi)
    {
        $this->cartApi = $cartApi;
    }

    public function __invoke() : void
    {
        $cart = $this->cartApi->fetchCurrentUserCart();

        dd($cart);
    }
}
