<?php

declare(strict_types=1);

namespace Tests\Persistence\Users;

use App\Persistence\Users\UserRecord;
use PHPUnit\Framework\TestCase;

class UserRecordTest extends TestCase
{
    public function testGetSearchableFields() : void
    {
        $record = new UserRecord();

        self::assertSame(
            $record->getSearchableFields(),
            [
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
            ]
        );
    }
}
