<?php

declare(strict_types=1);

namespace Tests\Http\Account\PaymentMethods;

use App\Http\Account\PaymentMethods\GetAccountPaymentMethodAction;
use App\Http\Account\PaymentMethods\GetAccountPaymentMethodResponder;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function assert;

class GetAccountPaymentMethodActionTest extends TestCase
{
    public function testWhenNoCard() : void
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
                self::equalTo('234')
            )
            ->willReturn(null);

        $responder = $this->createMock(
            GetAccountPaymentMethodResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn(234);

        $action = new GetAccountPaymentMethodAction(
            $responder,
            $userApi,
        );

        $exception = null;

        try {
            $action($request);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertSame(
            $request,
            $exception->getRequest(),
        );
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $user = new UserModel();

        $card = new UserCardModel();

        $response = $this->createMock(
            ResponseInterface::class
        );

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('fetchUserCardById')
            ->with(
                self::equalTo($user),
                self::equalTo('556')
            )
            ->willReturn($card);

        $responder = $this->createMock(
            GetAccountPaymentMethodResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('556');

        $action = new GetAccountPaymentMethodAction(
            $responder,
            $userApi,
        );

        self::assertSame(
            $response,
            $action($request),
        );
    }
}
