<?php

declare(strict_types=1);

namespace App\Persistence\Licenses;

use App\Persistence\Record;

class LicenseRecord extends Record
{
    /** @var string */
    protected static $tableName = 'licenses';

    /** @var string */
    public $item_key = '';

    /** @var string */
    public $item_title = '';

    /** @var string */
    public $major_version = '';

    /** @var string */
    public $version = '';

    /** @var string */
    public $last_available_version = '';

    /** @var string */
    public $notes = '';

    /** @var string */
    public $authorized_domains = '';

    /** @var int|bool|string */
    public $is_disabled = '1';
}
