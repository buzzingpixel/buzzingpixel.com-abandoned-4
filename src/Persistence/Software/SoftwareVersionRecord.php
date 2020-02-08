<?php

declare(strict_types=1);

namespace App\Persistence\Software;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class SoftwareVersionRecord extends Record
{
    protected static string $tableName = 'software_versions';

    public string $software_id = '';

    public string $major_version = '';

    public string $version = '';

    public string $download_file = '';

    public string $upgrade_price = '0.0';

    public string $released_on = '';
}
