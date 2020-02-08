<?php

declare(strict_types=1);

namespace App\Persistence\Users;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class UserRecord extends Record
{
    protected static string $tableName = 'users';

    /** @var string int|bool|string */
    public string $is_admin = '0';

    public string $email_address = '';

    public string $password_hash = '';

    /** @var int|bool|string */
    public $is_active = '1';

    public string $timezone = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $display_name = '';

    public string $billing_name = '';

    public string $billing_company = '';

    public string $billing_phone = '';

    public string $billing_country = '';

    public string $billing_address = '';

    public string $billing_city = '';

    public string $billing_state_abbr = '';

    public string $billing_postal_code = '';

    public string $created_at = '';

    /**
     * @return array<string, string>
     */
    public function getSearchableFields() : array
    {
        return [
            'email_address' => 'email_address',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'display_name' => 'display_name',
            'billing_name' => 'billing_name',
            'billing_company' => 'billing_company',
            'billing_phone' => 'billing_phone',
            'billing_country' => 'billing_country',
            'billing_address' => 'billing_address',
            'billing_city' => 'billing_city',
            'billing_state_abbr' => 'billing_state_abbr',
            'billing_postal_code' => 'billing_postal_code',
        ];
    }
}
