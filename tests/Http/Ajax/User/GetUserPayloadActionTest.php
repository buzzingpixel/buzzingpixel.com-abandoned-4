<?php

declare(strict_types=1);

namespace Tests\Http\Ajax\User;

use App\Cart\CartApi;
use App\Cart\Models\CartModel;
use App\Http\Ajax\User\GetUserPayloadAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;

class GetUserPayloadActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $cart = $this->createMock(CartModel::class);

        $cart->expects(self::once())
            ->method('asArray')
            ->willReturn(['test-array']);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('fetchCurrentUserCart')
            ->willReturn($cart);

        $action = new GetUserPayloadAction(
            $cartApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            )
        );

        $response = $action();

        self::assertSame(200, $response->getStatusCode());

        $headers = $response->getHeaders();

        self::assertCount(1, $headers);

        self::assertCount(1, $headers['Content-type']);

        self::assertSame(
            'application/json',
            $headers['Content-type'][0]
        );

        self::assertSame(
            '{"cart":["test-array"]}',
            $response->getBody()->__toString(),
        );
    }
}
