<?php

declare(strict_types=1);

namespace Tests\Http\Account\ChangePassword;

use App\Http\Account\ChangePassword\PostChangePasswordAction;
use App\Http\Account\ChangePassword\PostChangePasswordResponder;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostChangePasswordActionTest extends TestCase
{
    public function testWhenInvalidPassword() : void
    {
        $user = new UserModel();

        $userApi = $this->createMock(
            UserApi::class,
        );

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('validateUserPassword')
            ->with(
                self::equalTo($user),
                self::equalTo('fooPassword'),
                self::equalTo(false),
            )
            ->willReturn(false);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostChangePasswordResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new Payload(
                Payload::STATUS_NOT_VALID,
                [
                    'message' => 'The password you submitted is invalid',
                    'inputMessages' => ['password' => 'Invalid password'],
                ]
            )))
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(['password' => 'fooPassword']);

        $action = new PostChangePasswordAction(
            $responder,
            $userApi,
        );

        self::assertSame($response, $action($request));
    }

    public function testWhenPasswordsDontMatch() : void
    {
        $user = new UserModel();

        $userApi = $this->createMock(
            UserApi::class,
        );

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('validateUserPassword')
            ->with(
                self::equalTo($user),
                self::equalTo('fooPassword'),
                self::equalTo(false),
            )
            ->willReturn(true);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostChangePasswordResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new Payload(
                Payload::STATUS_NOT_VALID,
                [
                    'message' => 'Password confirmation must match new password',
                    'inputMessages' => [
                        'new_password' => 'New Password must match Confirmation',
                        'confirm_new_password' => 'Password Confirmation must match New Password',
                    ],
                ]
            )))
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'password' => 'fooPassword',
                'new_password' => 'barPassword',
            ]);

        $action = new PostChangePasswordAction(
            $responder,
            $userApi,
        );

        self::assertSame($response, $action($request));
    }

    public function testWhenNewPasswordIsEmpty() : void
    {
        $user = new UserModel();

        $userApi = $this->createMock(
            UserApi::class,
        );

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('validateUserPassword')
            ->with(
                self::equalTo($user),
                self::equalTo('fooPassword'),
                self::equalTo(false),
            )
            ->willReturn(true);

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostChangePasswordResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new Payload(
                Payload::STATUS_NOT_VALID,
                [
                    'message' => 'Password cannot be empty',
                    'inputMessages' => ['new_password' => 'Password cannot be empty'],
                ]
            )))
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn(['password' => 'fooPassword']);

        $action = new PostChangePasswordAction(
            $responder,
            $userApi,
        );

        self::assertSame($response, $action($request));
    }

    public function testWhenNotUpdated() : void
    {
        $user = new UserModel();

        $userApi = $this->createMock(
            UserApi::class,
        );

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('validateUserPassword')
            ->with(
                self::equalTo($user),
                self::equalTo('fooPassword'),
                self::equalTo(false),
            )
            ->willReturn(true);

        $userApi->expects(self::once())
            ->method('saveUser')
            ->with(self::equalTo($user))
            ->willReturn(new Payload(
                Payload::STATUS_ERROR,
                ['password' => 'testPassMsg'],
            ));

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostChangePasswordResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new Payload(
                Payload::STATUS_ERROR,
                [
                    'message' => '',
                    'inputMessages' => ['new_password' => 'testPassMsg'],
                ]
            )))
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'password' => 'fooPassword',
                'new_password' => 'barPassword',
                'confirm_new_password' => 'barPassword',
            ]);

        $action = new PostChangePasswordAction(
            $responder,
            $userApi,
        );

        self::assertSame($response, $action($request));
    }

    public function test() : void
    {
        $user = new UserModel();

        $userApi = $this->createMock(
            UserApi::class,
        );

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('validateUserPassword')
            ->with(
                self::equalTo($user),
                self::equalTo('fooPassword'),
                self::equalTo(false),
            )
            ->willReturn(true);

        $userApi->expects(self::once())
            ->method('saveUser')
            ->with(self::equalTo($user))
            ->willReturn(new Payload(
                Payload::STATUS_UPDATED,
            ));

        $userApi->expects(self::once())
            ->method('logCurrentUserOut')
            ->willReturn(new Payload(Payload::STATUS_SUCCESSFUL));

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostChangePasswordResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(new Payload(
                Payload::STATUS_UPDATED,
                [
                    'message' => '',
                    'inputMessages' => [],
                ]
            )))
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'password' => 'fooPassword',
                'new_password' => 'barPassword',
                'confirm_new_password' => 'barPassword',
            ]);

        $action = new PostChangePasswordAction(
            $responder,
            $userApi,
        );

        self::assertSame($response, $action($request));
    }
}
