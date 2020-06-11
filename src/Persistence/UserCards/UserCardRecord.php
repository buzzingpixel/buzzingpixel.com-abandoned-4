<?php

declare(strict_types=1);

namespace App\Persistence\UserCards;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class UserCardRecord extends Record
{
    protected static string $tableName = 'user_cards';

    public string $user_id = '';

    public string $stripe_id = '';

    public string $nickname = '';

    public string $last_four = '';

    public string $provider = '';

    /** @var int|bool|string */
    public $is_default = '';

    public string $expiration = '';
}
