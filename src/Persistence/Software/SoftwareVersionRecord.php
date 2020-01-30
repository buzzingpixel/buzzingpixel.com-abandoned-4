<?php

declare(strict_types=1);

namespace App\Persistence\Software;

use App\Persistence\Record;

class SoftwareVersionRecord extends Record
{
    /** @var string */
    protected static $tableName = 'software_versions';

    /** @var string */
    public $software_id = '';

    /** @var string */
    public $major_version = '';

    /** @var string */
    public $version = '';

    /** @var string */
    public $download_file = '';

    /** @var string */
    public $upgrade_price = '0.0';

    /** @var string */
    public $released_on = '';
}
