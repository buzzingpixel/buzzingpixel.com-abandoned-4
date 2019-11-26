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
    public $billing_postal_code = '';

    /** @var string */
    public $created_at = '';
}
