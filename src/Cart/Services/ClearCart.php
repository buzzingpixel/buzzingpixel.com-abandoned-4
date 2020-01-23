<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\CartApi;

class ClearCart
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

        foreach ($cart->getItems() as $item) {
            $item->setQuantity(0);
        }

        $this->cartApi->saveCart($cart);
    }
}
