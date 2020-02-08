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

    public function __invoke(SoftwareModel $software) : void
    {
        $cart = $this->cartApi->fetchCurrentUserCart();

        $added = false;

        foreach ($cart->getItems() as $item) {
            $itemSoftware = $item->getSoftware();
            assert($itemSoftware instanceof SoftwareModel);

            if ($itemSoftware->getSlug() !== $software->getSlug()) {
                continue;
            }

            $item->setQuantity($item->getQuantity() + 1);

            $added = true;

            break;
        }

        if (! $added) {
            $cart->addItem(new CartItemModel([
                'software' => $software,
                'quantity' => 1,
            ]));
        }

        $this->cartApi->saveCart($cart);
    }
}
