<?php

declare(strict_types=1);

namespace App\Persistence\Cart;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class CartRecord extends Record
{
    protected static string $tableName = 'cart';

    public ?string $user_id = null;

    public string $total_items = '';

    public string $total_quantity = '';

    public string $last_touched_at = '';

    public string $created_at = '';
}
