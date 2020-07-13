<?php

declare(strict_types=1);

namespace App\Persistence\Subscriptions;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class SubscriptionRecord extends Record
{
    protected static string $tableName = 'subscriptions';

    public string $user_id = '';

    public string $license_id = '';

    public ?string $order_ids = null;

    /** @var int|bool|string */
    public $auto_renew = '0';

    public ?string $card_id = null;
}
