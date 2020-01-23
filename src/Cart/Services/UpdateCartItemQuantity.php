<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\CartApi;
use App\Software\Models\SoftwareModel;

class UpdateCartItemQuantity
{
    /** @var CartApi */
    private $cartApi;

    public function __construct(CartApi $cartApi)
    {
        $this->cartApi = $cartApi;
    }

    public function __invoke(int $quantity, SoftwareModel $software) : void
    {
        $cart = $this->cartApi->fetchCurrentUserCart();

        foreach ($cart->getItems() as $item) {
            /** @var SoftwareModel $itemSoftware */
            $itemSoftware = $item->getSoftware();

            if ($itemSoftware->getSlug() !== $software->getSlug()) {
                continue;
            }

            $item->setQuantity($quantity);

            break;
        }

        $this->cartApi->saveCart($cart);
    }
}
