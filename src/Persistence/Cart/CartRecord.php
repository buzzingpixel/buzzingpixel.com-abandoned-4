<?php

declare(strict_types=1);

namespace App\Persistence\Cart;

use App\Persistence\Record;

class CartRecord extends Record
{
    /** @var string */
    protected static $tableName = 'cart';

    /** @var string|null */
    public $user_id;

    /** @var string */
    public $total_items = '';

    /** @var string */
    public $total_quantity = '';

    /** @var string */
    public $last_touched_at = '';

    /** @var string */
    public $created_at = '';
}
