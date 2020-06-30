<?php

declare(strict_types=1);

namespace Tests\Http\Ajax\Cart;

use App\Cart\CartApi;
use App\Cart\Models\CartModel;
use App\Http\Ajax\Cart\GetCartPayloadAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;

class GetCartPayloadActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $cart = $this->createMock(CartModel::class);

        $cart->totalQuantity = 4;

        $cart->method('calculateSubTotal')
            ->willReturn(123.2);

        $cart->method('calculateTax')
            ->willReturn(45.0);

        $cart->method('calculateTotal')
            ->willReturn(34.567);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('fetchCurrentUserCart')
            ->willReturn($cart);

        $action = new GetCartPayloadAction(
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
            '{"totalQuantity":4,"subTotal":"$123.20","tax":"$45.00","total":"$34.57"}',
            $response->getBody()->__toString(),
        );
    }
}
