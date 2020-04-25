<?php

declare(strict_types=1);

namespace Tests\Http\Account\RequestPasswordReset;

use App\Http\Account\RequestPasswordReset\PostRequestPasswordResetResponder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;

class PostRequestPasswordResetResponderTest extends TestCase
{
    public function test() : void
    {
        $responder = new PostRequestPasswordResetResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            )
        );

        $response = $responder();

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame(
            '/account/request-password-reset/msg',
            $headers['Location'][0],
        );
    }
}
