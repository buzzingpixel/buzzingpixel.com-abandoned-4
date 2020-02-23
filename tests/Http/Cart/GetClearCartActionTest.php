<?php

declare(strict_types=1);

namespace Tests\Http\Cart;

use App\Cart\CartApi;
use App\Http\Cart\GetClearCartAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;

class GetClearCartActionTest extends TestCase
{
    public function test() : void
    {
        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('clearCart');

        $action = new GetClearCartAction(
            $cartApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            ),
        );

        $response = $action();

        self::assertSame(303, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Location']);

        self::assertSame($headers['Location'][0], '/cart');
    }
}
