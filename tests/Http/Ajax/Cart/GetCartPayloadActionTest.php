<?php

declare(strict_types=1);

namespace Tests\Http\Ajax\Cart;

use App\Cart\CartApi;
use App\Cart\Models\CartModel;
use App\Http\Ajax\Cart\GetCartPayloadAction;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestConfig;
use Throwable;

class GetCartPayloadActionTest extends TestCase
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

        $cart = $this->createMock(CartModel::class);

        $cart->totalQuantity = 4;

        $cart->method('calculateSubTotal')
            ->willReturn(123.2);

        $cart->method('calculateTax')
            ->with(self::equalTo(''))
            ->willReturn(45.0);

        $cart->method('calculateTotal')
            ->with(self::equalTo(''))
            ->willReturn(34.567);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('fetchCurrentUserCart')
            ->willReturn($cart);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getQueryParams')
            ->willReturn(['selected_payment_method' => 'foo']);

        $action = new GetCartPayloadAction(
            $cartApi,
            $userApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            )
        );

        $response = $action($request);

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

    /**
     * @throws Throwable
     */
    public function testWhenUserAndPaymentMethodInvalidId(): void
    {
        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('fetchUserCardById')
            ->with(
                self::equalTo($user),
                self::equalTo('foo'),
            )
            ->willReturn(null);

        $cart = $this->createMock(CartModel::class);

        $cart->totalQuantity = 4;

        $cart->method('calculateSubTotal')
            ->willReturn(123.2);

        $cart->method('calculateTax')
            ->with(self::equalTo(''))
            ->willReturn(45.0);

        $cart->method('calculateTotal')
            ->with(self::equalTo(''))
            ->willReturn(34.567);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('fetchCurrentUserCart')
            ->willReturn($cart);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getQueryParams')
            ->willReturn(['selected_payment_method' => 'foo']);

        $action = new GetCartPayloadAction(
            $cartApi,
            $userApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            )
        );

        $response = $action($request);

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

    /**
     * @throws Throwable
     */
    public function testWhenUserAndNoPaymentMethod(): void
    {
        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $cart = $this->createMock(CartModel::class);

        $cart->totalQuantity = 4;

        $cart->method('calculateSubTotal')
            ->willReturn(123.2);

        $cart->method('calculateTax')
            ->with(self::equalTo(''))
            ->willReturn(45.0);

        $cart->method('calculateTotal')
            ->with(self::equalTo(''))
            ->willReturn(34.567);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('fetchCurrentUserCart')
            ->willReturn($cart);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getQueryParams')
            ->willReturn([]);

        $action = new GetCartPayloadAction(
            $cartApi,
            $userApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            )
        );

        $response = $action($request);

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

    /**
     * @throws Throwable
     */
    public function testWhenUserAndPaymentMethod(): void
    {
        $paymentMethod = new UserCardModel();

        $paymentMethod->state = 'foo-bar-state';

        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('fetchUserCardById')
            ->with(
                self::equalTo($user),
                self::equalTo('foo'),
            )
            ->willReturn($paymentMethod);

        $cart = $this->createMock(CartModel::class);

        $cart->totalQuantity = 4;

        $cart->method('calculateSubTotal')
            ->willReturn(123.2);

        $cart->method('calculateTax')
            ->with(self::equalTo('foo-bar-state'))
            ->willReturn(45.0);

        $cart->method('calculateTotal')
            ->with(self::equalTo('foo-bar-state'))
            ->willReturn(34.567);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('fetchCurrentUserCart')
            ->willReturn($cart);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getQueryParams')
            ->willReturn(['selected_payment_method' => 'foo']);

        $action = new GetCartPayloadAction(
            $cartApi,
            $userApi,
            TestConfig::$di->get(
                ResponseFactoryInterface::class,
            )
        );

        $response = $action($request);

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
