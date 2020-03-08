<?php

declare(strict_types=1);

namespace Tests\Http\Account;

use App\Http\Account\GetAccountAction;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;

class GetAccountActionTest extends TestCase
{
    public function test() : void
    {
        $action = TestConfig::$di->get(GetAccountAction::class);

        $response = $action();

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        $locationHeader = $headers['Location'];

        self::assertCount(1, $locationHeader);

        self::assertSame($locationHeader[0], '/account/licenses');
    }
}
