<?php

declare(strict_types=1);

namespace App\Persistence\Orders;

use App\Persistence\Record;

class OrderItemRecord extends Record
{
    /** @var string */
    protected static $tableName = 'order_items';

    /** @var string */
    public $order_id = '';

    /** @var string */
    public $license_id = '';

    /** @var string */
    public $item_key = '';

    /** @var string */
    public $item_title = '';

    /** @var string */
    public $major_version = '';

    /** @var string */
    public $version = '';

    /** @var int|float|string */
    public $price = 0;

    /** @var int|float|string */
    public $original_price = 0;

    /** @var int|bool|string */
    public $is_upgrade = '1';

    /** @var int|bool|string */
    public $has_been_upgraded = '1';

    /** @var string */
    public $expires = '';
}
