<?php

declare(strict_types=1);

namespace App\Persistence\Cart;

use App\Persistence\Record;

class CartItemRecord extends Record
{
    /** @var string */
    protected static $tableName = 'cart_items';

    /** @var string */
    public $cart_id = '';

    /** @var string */
    public $item_slug = '';

    /** @var string */
    public $quantity = '';
}
