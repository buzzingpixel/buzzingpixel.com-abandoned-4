<?php

declare(strict_types=1);

namespace App\Persistence\Orders;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class OrderItemRecord extends Record
{
    protected static string $tableName = 'order_items';

    public string $order_id = '';

    public string $license_id = '';

    public string $item_key = '';

    public string $item_title = '';

    public string $major_version = '';

    public string $version = '';

    /** @var int|float|string */
    public $price = 0;

    /** @var int|float|string */
    public $original_price = 0;

    /** @var int|bool|string */
    public $is_upgrade = '1';

    /** @var int|bool|string */
    public $has_been_upgraded = '1';

    public string $expires = '';
}
