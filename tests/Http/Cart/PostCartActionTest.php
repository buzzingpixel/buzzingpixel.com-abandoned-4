<?php

declare(strict_types=1);

namespace Tests\Http\Cart;

use App\Cart\CartApi;
use App\Cart\Models\CartModel;
use App\Http\Cart\PostCartAction;
use App\Http\Cart\PostCartResponder;
use App\Payload\Payload;
use App\Users\Models\LoggedInUser;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;
use Throwable;

use function assert;

class PostCartActionTest extends TestCase
{
    public function testWhenNoCard(): void
    {
        $user = new UserModel();

        $loggedInUser = new LoggedInUser($user);

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserCardById')
            ->with(
                self::equalTo($user),
                self::equalTo('foo-card'),
            )
            ->willReturn(null);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::never())
            ->method(self::anything());

        $responder = $this->createMock(
            PostCartResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->method('getParsedBody')
            ->willReturn(['card' => 'foo-card']);

        $action = new PostCartAction(
            $loggedInUser,
            $userApi,
            $cartApi,
            $responder,
        );

        $exception = null;

        try {
            $action($request);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpException);

        self::assertSame($request, $exception->getRequest());

        self::assertSame(
            'Invalid card on post',
            $exception->getMessage(),
        );
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $payload = new Payload(Payload::STATUS_SUCCESSFUL);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $cart = new CartModel();

        $card = new UserCardModel();

        $user = new UserModel();

        $loggedInUser = new LoggedInUser($user);

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserCardById')
            ->with(
                self::equalTo($user),
                self::equalTo('foo-card'),
            )
            ->willReturn($card);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('fetchCurrentUserCart')
            ->willReturn($cart);

        $cartApi->expects(self::once())
            ->method('processCartOrder')
            ->with(
                self::equalTo($cart),
                self::equalTo($card),
            )
            ->willReturn($payload);

        $responder = $this->createMock(
            PostCartResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($payload))
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->method('getParsedBody')
            ->willReturn(['card' => 'foo-card']);

        $action = new PostCartAction(
            $loggedInUser,
            $userApi,
            $cartApi,
            $responder,
        );

        self::assertSame(
            $response,
            $action($request),
        );
    }
}
