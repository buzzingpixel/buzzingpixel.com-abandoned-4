<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Users;

use App\Http\Admin\Users\PostAdminUserEditAction;
use App\Http\Admin\Users\PostAdminUserEditResponder;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function assert;

class PostAdminUserEditActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNoUser() : void
    {
        $responder = $this->createMock(
            PostAdminUserEditResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo('foo-id'))
            ->willReturn(null);

        $action = new PostAdminUserEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $exception = null;

        try {
            $action($request);
        } catch (HttpNotFoundException $e) {
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
    public function testInvalid1() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminUserEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $userId
                ) use ($response) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => [
                                'email_address' => 'A valid email is required',
                                'timezone' => 'Timezone is required',
                            ],
                            'inputValues' => [
                                'is_admin' => false,
                                'email_address' => '',
                                'timezone' => '',
                                'first_name' => '',
                                'last_name' => '',
                                'display_name' => '',
                                'billing_name' => '',
                                'billing_company' => '',
                                'billing_phone' => '',
                                'billing_address' => '',
                                'billing_country' => '',
                                'billing_postal_code' => '',
                            ],
                        ],
                        $payload->getResult(),
                    );

                    self::assertSame(
                        'foo-user-id',
                        $userId,
                    );

                    return $response;
                }
            );

        $user = new UserModel();

        $user->id = 'foo-user-id';

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($user);

        $action = new PostAdminUserEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([]);

        self::assertSame(
            $response,
            $action($request),
        );
    }

    /**
     * @throws Throwable
     */
    public function testInvalid2() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminUserEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $userId
                ) use ($response) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => [
                                'email_address' => 'A valid email is required',
                                'timezone' => 'A valid timezone is required',
                                'billing_country' => 'If a postal code is supplied, a country must also be supplied',
                            ],
                            'inputValues' => [
                                'is_admin' => false,
                                'email_address' => '',
                                'timezone' => 'foo-invalid-timezone',
                                'first_name' => '',
                                'last_name' => '',
                                'display_name' => '',
                                'billing_name' => '',
                                'billing_company' => '',
                                'billing_phone' => '',
                                'billing_address' => '',
                                'billing_country' => '',
                                'billing_postal_code' => 'foo-postal-code',
                            ],
                        ],
                        $payload->getResult(),
                    );

                    self::assertSame(
                        'foo-user-id',
                        $userId,
                    );

                    return $response;
                }
            );

        $user = new UserModel();

        $user->id = 'foo-user-id';

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($user);

        $action = new PostAdminUserEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'timezone' => 'foo-invalid-timezone',
                'billing_postal_code' => 'foo-postal-code',
            ]);

        self::assertSame(
            $response,
            $action($request),
        );
    }

    /**
     * @throws Throwable
     */
    public function testInvalid3() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminUserEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $userId
                ) use ($response) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => [
                                'email_address' => 'A valid email is required',
                                'billing_postal_code' => 'Postal code is invalid',
                            ],
                            'inputValues' => [
                                'is_admin' => false,
                                'email_address' => '',
                                'timezone' => 'America/Chicago',
                                'first_name' => '',
                                'last_name' => '',
                                'display_name' => '',
                                'billing_name' => '',
                                'billing_company' => '',
                                'billing_phone' => '',
                                'billing_address' => '',
                                'billing_country' => 'foo-postal-country',
                                'billing_postal_code' => 'foo-postal-code',
                            ],
                        ],
                        $payload->getResult(),
                    );

                    self::assertSame(
                        'foo-user-id',
                        $userId,
                    );

                    return $response;
                }
            );

        $user = new UserModel();

        $user->id = 'foo-user-id';

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('validatePostalCode')
            ->with(
                self::equalTo('foo-postal-code'),
                self::equalTo('foo-postal-country')
            )
            ->willReturn(false);

        $action = new PostAdminUserEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'timezone' => 'America/Chicago',
                'billing_country' => 'foo-postal-country',
                'billing_postal_code' => 'foo-postal-code',
            ]);

        self::assertSame(
            $response,
            $action($request),
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenApiSaveFails() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminUserEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $userId
                ) use ($response) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_NOT_UPDATED,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        ['message' => 'An unknown error occurred'],
                        $payload->getResult(),
                    );

                    self::assertSame(
                        'foo-user-id',
                        $userId,
                    );

                    return $response;
                }
            );

        $user = new UserModel();

        $user->id = 'foo-user-id';

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('validatePostalCode')
            ->with(
                self::equalTo('foo-postal-code'),
                self::equalTo('foo-postal-country')
            )
            ->willReturn(true);

        $userApi->expects(self::once())
            ->method('fillModelFromPostalCode')
            ->with(self::equalTo($user));

        $userApi->expects(self::once())
            ->method('saveUser')
            ->with(self::equalTo($user))
            ->willReturn(new Payload(Payload::STATUS_ERROR));

        $action = new PostAdminUserEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'is_admin' => 'true',
                'email_address' => 'foo@bar.baz',
                'timezone' => 'US/Central',
                'first_name' => 'Test First Name',
                'last_name' => 'Test Last Name',
                'display_name' => 'Test Display Name',
                'billing_name' => 'Test Billing Name',
                'billing_company' => 'Test Billing Company',
                'billing_phone' => 'Test Billing Phone',
                'billing_address' => 'Test Billing Address',
                'billing_country' => 'foo-postal-country',
                'billing_postal_code' => 'foo-postal-code',
            ]);

        self::assertSame(
            $response,
            $action($request),
        );

        self::assertTrue($user->isAdmin);

        self::assertSame(
            'foo@bar.baz',
            $user->emailAddress,
        );

        self::assertSame(
            'US/Central',
            $user->timezone->getName(),
        );

        self::assertSame(
            'Test First Name',
            $user->firstName,
        );

        self::assertSame(
            'Test Last Name',
            $user->lastName,
        );

        self::assertSame(
            'Test Display Name',
            $user->displayName,
        );

        self::assertSame(
            'Test Billing Name',
            $user->billingName,
        );

        self::assertSame(
            'Test Billing Company',
            $user->billingCompany,
        );

        self::assertSame(
            'Test Billing Phone',
            $user->billingPhone,
        );

        self::assertSame(
            'Test Billing Address',
            $user->billingAddress,
        );

        self::assertSame(
            'foo-postal-country',
            $user->billingCountry,
        );

        self::assertSame(
            'foo-postal-code',
            $user->billingPostalCode,
        );
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminUserEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload,
                    string $userId
                ) use ($response) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_UPDATED,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [],
                        $payload->getResult(),
                    );

                    self::assertSame(
                        'foo-user-id',
                        $userId,
                    );

                    return $response;
                }
            );

        $user = new UserModel();

        $user->id = 'foo-user-id';

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('validatePostalCode')
            ->with(
                self::equalTo('foo-postal-code'),
                self::equalTo('foo-postal-country')
            )
            ->willReturn(true);

        $userApi->expects(self::once())
            ->method('fillModelFromPostalCode')
            ->with(self::equalTo($user));

        $userApi->expects(self::once())
            ->method('saveUser')
            ->with(self::equalTo($user))
            ->willReturn(new Payload(Payload::STATUS_UPDATED));

        $action = new PostAdminUserEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'is_admin' => 'true',
                'email_address' => 'foo@bar.baz',
                'timezone' => 'US/Central',
                'first_name' => 'Test First Name',
                'last_name' => 'Test Last Name',
                'display_name' => 'Test Display Name',
                'billing_name' => 'Test Billing Name',
                'billing_company' => 'Test Billing Company',
                'billing_phone' => 'Test Billing Phone',
                'billing_address' => 'Test Billing Address',
                'billing_country' => 'foo-postal-country',
                'billing_postal_code' => 'foo-postal-code',
            ]);

        self::assertSame(
            $response,
            $action($request),
        );

        self::assertTrue($user->isAdmin);

        self::assertSame(
            'foo@bar.baz',
            $user->emailAddress,
        );

        self::assertSame(
            'US/Central',
            $user->timezone->getName(),
        );

        self::assertSame(
            'Test First Name',
            $user->firstName,
        );

        self::assertSame(
            'Test Last Name',
            $user->lastName,
        );

        self::assertSame(
            'Test Display Name',
            $user->displayName,
        );

        self::assertSame(
            'Test Billing Name',
            $user->billingName,
        );

        self::assertSame(
            'Test Billing Company',
            $user->billingCompany,
        );

        self::assertSame(
            'Test Billing Phone',
            $user->billingPhone,
        );

        self::assertSame(
            'Test Billing Address',
            $user->billingAddress,
        );

        self::assertSame(
            'foo-postal-country',
            $user->billingCountry,
        );

        self::assertSame(
            'foo-postal-code',
            $user->billingPostalCode,
        );
    }
}
