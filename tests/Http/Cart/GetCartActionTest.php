<?php

declare(strict_types=1);

namespace Tests\Http\Cart;

use App\Cart\CartApi;
use App\Cart\Models\CartModel;
use App\Http\Cart\GetCartAction;
use App\Http\Cart\GetCartResponder;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;

class GetCartActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNoUser(): void
    {
        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn(null);

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
            ->with(
                self::equalTo($cart),
            )
            ->willReturn($response);

        $action = new GetCartAction(
            $cartApi,
            $userApi,
            $responder
        );

        self::assertSame($response, $action());
    }

    /**
     * @throws Throwable
     */
    public function testWithUser(): void
    {
        $cards = [
            new UserCardModel(),
            new UserCardModel(),
        ];

        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('fetchUserCards')
            ->with(self::equalTo($user))
            ->willReturn($cards);

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
            ->with(
                self::equalTo($cart),
                self::equalTo($cards),
            )
            ->willReturn($response);

        $action = new GetCartAction(
            $cartApi,
            $userApi,
            $responder
        );

        self::assertSame($response, $action());
    }
}
