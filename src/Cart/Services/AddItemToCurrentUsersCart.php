<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\CartApi;
use App\Cart\Models\CartItemModel;
use App\Software\Models\SoftwareModel;

use function assert;

class AddItemToCurrentUsersCart
{
    private CartApi $cartApi;

    public function __construct(CartApi $cartApi)
    {
        $this->cartApi = $cartApi;
    }

    public function __invoke(SoftwareModel $software): void
    {
        $cart = $this->cartApi->fetchCurrentUserCart();

        $added = false;

        foreach ($cart->items as $item) {
            $itemSoftware = $item->software;

            assert($itemSoftware instanceof SoftwareModel);

            if ($itemSoftware->slug !== $software->slug) {
                continue;
            }

            $item->quantity += 1;

            $added = true;

            break;
        }

        if (! $added) {
            $item           = new CartItemModel();
            $item->software = $software;
            $item->quantity = 1;

            $cart->addItem($item);
        }

        $this->cartApi->saveCart($cart);
    }
}
