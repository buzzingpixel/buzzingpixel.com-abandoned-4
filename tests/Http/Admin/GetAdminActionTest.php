<?php

declare(strict_types=1);

namespace Tests\Http\Admin;

use App\Http\Admin\GetAdminAction;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;

class GetAdminActionTest extends TestCase
{
    public function test() : void
    {
        $action = TestConfig::$di->get(GetAdminAction::class);

        $response = $action();

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        $locationHeader = $headers['Location'];

        self::assertCount(1, $locationHeader);

        self::assertSame($locationHeader[0], '/admin/software');
    }
}
