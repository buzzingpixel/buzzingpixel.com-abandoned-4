<?php

declare(strict_types=1);

namespace App\Persistence\Orders;

use App\Persistence\Record;

class OrderRecord extends Record
{
    protected static string $tableName = 'orders';

    /** @var int|string */
    public $old_order_number = 0;

    public string $user_id = '';

    public string $stripe_id = '';

    /** @var int|float|string */
    public $stripe_amount = 0;

    public string $stripe_balance_transaction = '';

    /** @var int|bool|string */
    public $stripe_captured = '1';

    /** @var int|string */
    public $stripe_created = 0;

    public string $stripe_currency = '';

    /** @var int|bool|string */
    public $stripe_paid = '1';

    /** @var float|int|string */
    public $subtotal = 0;

    /** @var float|int|string */
    public $tax = 0;

    /** @var float|int|string */
    public $total = 0;

    public string $name = '';

    public string $company = '';

    public string $phone_number = '';

    public string $country = '';

    public string $address = '';

    public string $address_continued = '';

    public string $city = '';

    public string $state = '';

    public string $postal_code = '';

    public string $date = '';
}
