<?php

declare(strict_types=1);

namespace App\Cart\Transformers;

use App\Cart\Models\CartItemModel;
use App\Persistence\Cart\CartItemRecord;

class TransformCartItemModelToRecord
{
    public function __invoke(CartItemModel $cartItem) : CartItemRecord
    {
        $record = new CartItemRecord();

        $record->id = $cartItem->getId();

        $cart = $cartItem->getCart();

        if ($cart !== null) {
            $record->cart_id = $cart->getId();
        }

        $software = $cartItem->getSoftware();

        if ($software !== null) {
            $record->item_slug = $software->getSlug();
        }

        $record->quantity = (string) $cartItem->getQuantity();

        return $record;
    }
}
