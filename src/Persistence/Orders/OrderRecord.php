<?php

declare(strict_types=1);

namespace App\Persistence\Orders;

use App\Persistence\Record;

class OrderRecord extends Record
{
    /** @var string */
    protected static $tableName = 'orders';

    /** @var int|string */
    public $old_order_number = 0;

    /** @var string */
    public $user_id = '';

    /** @var string */
    public $stripe_id = '';

    /** @var int|float|string */
    public $stripe_amount = 0;

    /** @var string */
    public $stripe_balance_transaction = '';

    /** @var int|bool|string */
    public $stripe_captured = '1';

    /** @var int|string */
    public $stripe_created = 0;

    /** @var string */
    public $stripe_currency = '';

    /** @var int|bool|string */
    public $stripe_paid = '1';

    /** @var float|int|string */
    public $subtotal = 0;

    /** @var float|int|string */
    public $tax = 0;

    /** @var float|int|string */
    public $total = 0;

    /** @var string */
    public $name = '';

    /** @var string */
    public $company = '';

    /** @var string */
    public $phone_number = '';

    /** @var string */
    public $country = '';

    /** @var string */
    public $address = '';

    /** @var string */
    public $address_continued = '';

    /** @var string */
    public $city = '';

    /** @var string */
    public $state = '';

    /** @var string */
    public $postal_code = '';

    /** @var string */
    public $date = '';
}
