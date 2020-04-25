<?php

declare(strict_types=1);

namespace Tests\Http\Account\RequestPasswordReset;

use App\Http\Account\RequestPasswordReset\PostRequestPasswordResetAction;
use App\Http\Account\RequestPasswordReset\PostRequestPasswordResetResponder;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class PostRequestPasswordResetActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNoUser() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserByEmailAddress')
            ->with(self::equalTo(''))
            ->willReturn(null);

        $responder = $this->createMock(
            PostRequestPasswordResetResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([]);

        $action = new PostRequestPasswordResetAction(
            $userApi,
            $responder
        );

        self::assertSame(
            $response,
            $action($request)
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenResetTokensGreaterThan5() : void
    {
        $user = new UserModel();

        $response = $this->createMock(
            ResponseInterface::class
        );

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::at(0))
            ->method('fetchUserByEmailAddress')
            ->with(self::equalTo('fooEmailAddress'))
            ->willReturn($user);

        $userApi->expects(self::at(1))
            ->method('fetchTotalUserResetTokens')
            ->with(self::equalTo($user))
            ->willReturn(6);

        $responder = $this->createMock(
            PostRequestPasswordResetResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(['email_address' => 'fooEmailAddress']);

        $action = new PostRequestPasswordResetAction(
            $userApi,
            $responder
        );

        self::assertSame(
            $response,
            $action($request)
        );
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $user = new UserModel();

        $response = $this->createMock(
            ResponseInterface::class
        );

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::at(0))
            ->method('fetchUserByEmailAddress')
            ->with(self::equalTo('fooEmailAddress'))
            ->willReturn($user);

        $userApi->expects(self::at(1))
            ->method('fetchTotalUserResetTokens')
            ->with(self::equalTo($user))
            ->willReturn(3);

        $userApi->expects(self::at(2))
            ->method('requestPasswordResetEmail')
            ->with(self::equalTo($user));

        $responder = $this->createMock(
            PostRequestPasswordResetResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(['email_address' => 'fooEmailAddress']);

        $action = new PostRequestPasswordResetAction(
            $userApi,
            $responder
        );

        self::assertSame(
            $response,
            $action($request)
        );
    }
}
