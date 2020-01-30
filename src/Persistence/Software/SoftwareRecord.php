<?php

declare(strict_types=1);

namespace App\Persistence\Software;

use App\Persistence\Record;

class SoftwareRecord extends Record
{
    /** @var string */
    protected static $tableName = 'software';

    /** @var string */
    public $slug = '';

    /** @var string */
    public $name = '';

    /** @var int|bool|string */
    public $is_for_sale = '1';

    /** @var string */
    public $price = '0.0';

    /** @var string */
    public $renewal_price = '0.0';

    /** @var int|bool|string */
    public $is_subscription = '0';
}
