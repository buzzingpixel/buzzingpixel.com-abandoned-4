<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\CartApi;
use App\Software\Models\SoftwareModel;

use function assert;

class UpdateCartItemQuantity
{
    private CartApi $cartApi;

    public function __construct(CartApi $cartApi)
    {
        $this->cartApi = $cartApi;
    }

    public function __invoke(int $quantity, SoftwareModel $software): void
    {
        $cart = $this->cartApi->fetchCurrentUserCart();

        foreach ($cart->items as $item) {
            $itemSoftware = $item->software;

            assert($itemSoftware instanceof SoftwareModel);

            if ($itemSoftware->slug === $software->slug) {
                $item->quantity = $quantity;

                break;
            }
        }

        $this->cartApi->saveCart($cart);
    }
}
