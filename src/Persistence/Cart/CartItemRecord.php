<?php

declare(strict_types=1);

namespace App\Persistence\Cart;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class CartItemRecord extends Record
{
    protected static string $tableName = 'cart_items';

    public string $cart_id = '';

    public string $item_slug = '';

    public string $quantity = '';
}
