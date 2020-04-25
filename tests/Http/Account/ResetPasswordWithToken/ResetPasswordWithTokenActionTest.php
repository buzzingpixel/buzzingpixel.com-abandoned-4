<?php

declare(strict_types=1);

namespace Tests\Http\Account\ResetPasswordWithToken;

use App\Http\Account\ResetPasswordWithToken\ResetPasswordWithTokenAction;
use App\Http\Account\ResetPasswordWithToken\ResetPasswordWithTokenResponder;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class ResetPasswordWithTokenActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNoUser() : void
    {
        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserByResetToken')
            ->with(self::equalTo('fooToken'))
            ->willReturn(null);

        $responder = $this->createMock(
            ResetPasswordWithTokenResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('token'))
            ->willReturn('fooToken');

        $action = new ResetPasswordWithTokenAction(
            $userApi,
            $responder,
        );

        $exception = null;

        try {
            $action($request);
        } catch (HttpNotFoundException $e) {
            $exception = $e;
        }

        self::assertInstanceOf(
            HttpNotFoundException::class,
            $exception,
        );
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserByResetToken')
            ->with(self::equalTo('barToken'))
            ->willReturn($user);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            ResetPasswordWithTokenResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($user),
                self::equalTo('barToken'),
            )
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('token'))
            ->willReturn('barToken');

        $action = new ResetPasswordWithTokenAction(
            $userApi,
            $responder,
        );

        $exception = null;

        self::assertSame(
            $response,
            $action($request)
        );
    }
}
