<?php

declare(strict_types=1);

namespace App\Cart\Transformers;

use App\Cart\Models\CartItemModel;
use App\Persistence\Cart\CartItemRecord;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformCartItemModelToRecord
{
    public function __invoke(CartItemModel $cartItem): CartItemRecord
    {
        $record = new CartItemRecord();

        $record->id = $cartItem->id;

        if ($cartItem->cart !== null) {
            $record->cart_id = $cartItem->cart->id;
        }

        if ($cartItem->software !== null) {
            $record->item_slug = $cartItem->software->slug;
        }

        $record->quantity = (string) $cartItem->quantity;

        return $record;
    }
}
