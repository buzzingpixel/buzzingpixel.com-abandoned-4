<?php

declare(strict_types=1);

namespace Tests\Http\Cart;

use App\Cart\CartApi;
use App\Cart\Models\CartModel;
use App\Http\Cart\GetCartAction;
use App\Http\Cart\GetCartResponder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;

class GetCartActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $cart = new CartModel();

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('fetchCurrentUserCart')
            ->willReturn($cart);

        $response = TestConfig::$di->get(ResponseFactoryInterface::class)
            ->createResponse();

        $responder = $this->createMock(
            GetCartResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($cart))
            ->willReturn($response);

        $action = new GetCartAction(
            $cartApi,
            $responder
        );

        self::assertSame($response, $action());
    }
}
