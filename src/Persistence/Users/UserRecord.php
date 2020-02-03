<?php

declare(strict_types=1);

namespace App\Persistence\Users;

use App\Persistence\Record;

class UserRecord extends Record
{
    /** @var string int|bool|string */
    public $is_admin = '0';

    /** @var string */
    public $email_address = '';

    /** @var string */
    public $password_hash = '';

    /** @var int|bool|string */
    public $is_active = '1';

    /** @var string */
    public $timezone = '';

    /** @var string */
    public $first_name = '';

    /** @var string */
    public $last_name = '';

    /** @var string */
    public $display_name = '';

    /** @var string */
    public $billing_name = '';

    /** @var string */
    public $billing_company = '';

    /** @var string */
    public $billing_phone = '';

    /** @var string */
    public $billing_country = '';

    /** @var string */
    public $billing_address = '';

    /** @var string */
    public $billing_city = '';

    /** @var string */
    public $billing_state_abbr = '';

    /** @var string */
    public $billing_postal_code = '';

    /** @var string */
    public $created_at = '';

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
