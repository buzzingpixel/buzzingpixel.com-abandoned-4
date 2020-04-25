<?php

declare(strict_types=1);

namespace Tests\Http\Account\ResetPasswordWithToken;

use App\Http\Account\ResetPasswordWithToken\PostResetPasswordWithTokenAction;
use App\Http\Account\ResetPasswordWithToken\PostResetPasswordWithTokenResponder;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class PostResetPasswordWithTokenActionTest extends TestCase
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
            PostResetPasswordWithTokenResponder::class
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

        $action = new PostResetPasswordWithTokenAction(
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
    public function testWhenPasswordsDontMatch() : void
    {
        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserByResetToken')
            ->with(self::equalTo('fooToken'))
            ->willReturn($user);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostResetPasswordWithTokenResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo(new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'Password confirmation must match password',
                        'inputs' => [
                            'password' => 'Password must match Password Confirmation',
                            'confirmPassword' => 'Password Confirmation must match password',
                        ],
                    ]
                )),
                self::equalTo('fooToken')
            )
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('token'))
            ->willReturn('fooToken');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'password' => 'foo',
                'confirm_password' => 'bar',
            ]);

        $action = new PostResetPasswordWithTokenAction(
            $userApi,
            $responder,
        );

        self::assertSame(
            $response,
            $action($request)
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenPasswordsIsEmpty() : void
    {
        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserByResetToken')
            ->with(self::equalTo('fooToken'))
            ->willReturn($user);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostResetPasswordWithTokenResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo(new Payload(
                    Payload::STATUS_NOT_VALID,
                    [
                        'message' => 'Password cannot be empty',
                        'inputs' => ['password' => 'Password cannot be empty'],
                    ]
                )),
                self::equalTo('fooToken')
            )
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('token'))
            ->willReturn('fooToken');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([]);

        $action = new PostResetPasswordWithTokenAction(
            $userApi,
            $responder,
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

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserByResetToken')
            ->with(self::equalTo('fooToken'))
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('resetPasswordByToken')
            ->with(
                self::equalTo('fooToken'),
                self::equalTo('fooPassword'),
            )
            ->willReturn(
                new Payload(
                    Payload::STATUS_UPDATED,
                    ['testInput' => 'tesInputResult']
                )
            );

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostResetPasswordWithTokenResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo(new Payload(
                    Payload::STATUS_UPDATED,
                    [
                        'message' => '',
                        'inputs' => ['testInput' => 'tesInputResult'],
                    ]
                )),
                self::equalTo('fooToken')
            )
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('token'))
            ->willReturn('fooToken');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'password' => 'fooPassword',
                'confirm_password' => 'fooPassword',
            ]);

        $action = new PostResetPasswordWithTokenAction(
            $userApi,
            $responder,
        );

        self::assertSame(
            $response,
            $action($request)
        );
    }
}
