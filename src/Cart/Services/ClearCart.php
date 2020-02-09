<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\CartApi;

class ClearCart
{
    private CartApi $cartApi;

    public function __construct(CartApi $cartApi)
    {
        $this->cartApi = $cartApi;
    }

    public function __invoke() : void
    {
        $cart = $this->cartApi->fetchCurrentUserCart();

        foreach ($cart->items as $item) {
            $item->quantity = 0;
        }

        $this->cartApi->saveCart($cart);
    }
}
