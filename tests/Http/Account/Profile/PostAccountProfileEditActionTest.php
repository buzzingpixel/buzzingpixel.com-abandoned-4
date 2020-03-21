<?php

declare(strict_types=1);

namespace Tests\Http\Account\Profile;

use App\Http\Account\Profile\PostAccountProfileEditAction;
use App\Http\Account\Profile\PostAccountProfileEditResponder;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class PostAccountProfileEditActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testInvalid1() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAccountProfileEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload
                ) use ($response) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => ['timezone' => 'Timezone is required'],
                            'inputValues' => [
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

                    return $response;
                }
            );

        $user = new UserModel();

        $user->id = 'foo-user-id';

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $action = new PostAccountProfileEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

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
            PostAccountProfileEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload
                ) use ($response) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => [
                                'timezone' => 'A valid timezone is required',
                                'billing_country' => 'If a postal code is supplied, a country must also be supplied',
                            ],
                            'inputValues' => [
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

                    return $response;
                }
            );

        $user = new UserModel();

        $user->id = 'foo-user-id';

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $action = new PostAccountProfileEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

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
            PostAccountProfileEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload
                ) use ($response) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => ['billing_postal_code' => 'Postal code is invalid'],
                            'inputValues' => [
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

                    return $response;
                }
            );

        $user = new UserModel();

        $user->id = 'foo-user-id';

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('validatePostalCode')
            ->with(
                self::equalTo('foo-postal-code'),
                self::equalTo('foo-postal-country')
            )
            ->willReturn(false);

        $action = new PostAccountProfileEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

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
            PostAccountProfileEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload
                ) use ($response) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_NOT_UPDATED,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        ['message' => 'An unknown error occurred'],
                        $payload->getResult(),
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
            ->method('fetchLoggedInUser')
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

        $action = new PostAccountProfileEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
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
            PostAccountProfileEditResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    Payload $payload
                ) use ($response) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_UPDATED,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [],
                        $payload->getResult(),
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
            ->method('fetchLoggedInUser')
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

        $action = new PostAccountProfileEditAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
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
