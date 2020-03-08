<?php

declare(strict_types=1);

namespace App\Persistence\Licenses;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class LicenseRecord extends Record
{
    protected static string $tableName = 'licenses';

    public string $owner_user_id = '';

    public string $item_key = '';

    public string $item_title = '';

    public string $major_version = '';

    public string $version = '';

    public string $last_available_version = '';

    public string $notes = '';

    public string $authorized_domains = '';

    /** @var int|bool|string */
    public $is_disabled = '1';
}
