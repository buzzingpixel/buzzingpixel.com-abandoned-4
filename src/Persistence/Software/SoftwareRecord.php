<?php

declare(strict_types=1);

namespace App\Persistence\Software;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class SoftwareRecord extends Record
{
    protected static string $tableName = 'software';

    public string $slug = '';

    public string $name = '';

    /** @var int|bool|string */
    public $is_for_sale = '1';

    public string $price = '0.0';

    public string $renewal_price = '0.0';

    /** @var int|bool|string */
    public $is_subscription = '0';
}
