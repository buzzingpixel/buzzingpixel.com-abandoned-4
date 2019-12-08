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
}
