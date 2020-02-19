<?php

declare(strict_types=1);

namespace Tests\Http\Admin\Users;

use App\Http\Admin\Users\PostAdminUserCreateAction;
use App\Http\Admin\Users\PostAdminUserCreateResponder;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostAdminUserCreateActionTest extends TestCase
{
    public function testInvalidInput() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminUserCreateResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Payload $payload) use ($response) {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => [
                                'email_address' => 'A valid email is required',
                                'password' => 'Password must be at least 8 characters',
                                'timezone' => 'Timezone is required',
                            ],
                            'inputValues' => [
                                'is_admin' => false,
                                'email_address' => '',
                                'password' => '',
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

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::never())
            ->method(self::anything());

        $action = new PostAdminUserCreateAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([]);

        $returnResponse = $action($request);

        self::assertSame(
            $response,
            $returnResponse
        );
    }

    public function testInvalidPostalCodeCountryComboInvalidTimeZone() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminUserCreateResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Payload $payload) use ($response) {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => [
                                'email_address' => 'A valid email is required',
                                'password' => 'Password must be at least 8 characters',
                                'timezone' => 'A valid timezone is required',
                                'billing_country' => 'If a postal code is supplied, a country must also be supplied',
                            ],
                            'inputValues' => [
                                'is_admin' => false,
                                'email_address' => '',
                                'password' => '',
                                'timezone' => 'foo-timezone',
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

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::never())
            ->method(self::anything());

        $action = new PostAdminUserCreateAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'timezone' => 'foo-timezone',
                'billing_postal_code' => 'foo-postal-code',
            ]);

        $returnResponse = $action($request);

        self::assertSame(
            $response,
            $returnResponse
        );
    }

    public function testInvalidPostalCode() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminUserCreateResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Payload $payload) use ($response) {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [
                            'message' => 'There were errors with your submission',
                            'inputMessages' => [
                                'email_address' => 'A valid email is required',
                                'password' => 'Password must be at least 8 characters',
                                'timezone' => 'Timezone is required',
                                'billing_postal_code' => 'Postal code is invalid',
                            ],
                            'inputValues' => [
                                'is_admin' => false,
                                'email_address' => '',
                                'password' => '',
                                'timezone' => '',
                                'first_name' => '',
                                'last_name' => '',
                                'display_name' => '',
                                'billing_name' => '',
                                'billing_company' => '',
                                'billing_phone' => '',
                                'billing_address' => '',
                                'billing_country' => 'foo-billing-country',
                                'billing_postal_code' => 'foo-postal-code',
                            ],
                        ],
                        $payload->getResult(),
                    );

                    return $response;
                }
            );

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('validatePostalCode')
            ->with(
                self::equalTo('foo-postal-code'),
                self::equalTo('foo-billing-country'),
            )
            ->willReturn(false);

        $userApi->expects(self::never())
            ->method('saveUser');

        $action = new PostAdminUserCreateAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'billing_postal_code' => 'foo-postal-code',
                'billing_country' => 'foo-billing-country',
            ]);

        $returnResponse = $action($request);

        self::assertSame(
            $response,
            $returnResponse
        );
    }

    public function testUnsuccessful() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminUserCreateResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Payload $payload) use ($response) {
                    self::assertSame(
                        Payload::STATUS_NOT_CREATED,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        ['message' => 'An unknown error occurred'],
                        $payload->getResult(),
                    );

                    return $response;
                }
            );

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('validatePostalCode')
            ->with(
                self::equalTo('foo-postal-code'),
                self::equalTo('foo-billing-country'),
            )
            ->willReturn(true);

        $userApi->expects(self::once())
            ->method('fillModelFromPostalCode')
            ->willReturnCallback(
                static function (UserModel $user) : void {
                    $user->billingCity = 'foo-city-fill';

                    $user->billingStateAbbr = 'foo-state-abbr-fill';
                }
            );

        $userApi->expects(self::once())
            ->method('saveUser')
            ->willReturnCallback(
                static function (UserModel $user) : Payload {
                    self::assertTrue($user->isAdmin);

                    self::assertSame(
                        'foo@bar.baz',
                        $user->emailAddress,
                    );

                    self::assertSame(
                        'foo-password',
                        $user->newPassword,
                    );

                    self::assertSame(
                        'America/Chicago',
                        $user->timezone->getName(),
                    );

                    self::assertSame(
                        'Foo First Name',
                        $user->firstName,
                    );

                    self::assertSame(
                        'Foo Last Name',
                        $user->lastName,
                    );

                    self::assertSame(
                        'Foo Display Name',
                        $user->displayName,
                    );

                    self::assertSame(
                        'Foo Billing Name',
                        $user->billingName,
                    );

                    self::assertSame(
                        'Foo Billing Company',
                        $user->billingCompany,
                    );

                    self::assertSame(
                        'Foo Billing Phone',
                        $user->billingPhone,
                    );

                    self::assertSame(
                        'Foo Billing Address',
                        $user->billingAddress,
                    );

                    self::assertSame(
                        'foo-billing-country',
                        $user->billingCountry,
                    );

                    self::assertSame(
                        'foo-postal-code',
                        $user->billingPostalCode,
                    );

                    self::assertSame(
                        'foo-city-fill',
                        $user->billingCity,
                    );

                    self::assertSame(
                        'foo-state-abbr-fill',
                        $user->billingStateAbbr,
                    );

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $action = new PostAdminUserCreateAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'is_admin' => 'true',
                'email_address' => 'foo@bar.baz',
                'password' => 'foo-password',
                'timezone' => 'America/Chicago',
                'first_name' => 'Foo First Name',
                'last_name' => 'Foo Last Name',
                'display_name' => 'Foo Display Name',
                'billing_name' => 'Foo Billing Name',
                'billing_company' => 'Foo Billing Company',
                'billing_phone' => 'Foo Billing Phone',
                'billing_address' => 'Foo Billing Address',
                'billing_country' => 'foo-billing-country',
                'billing_postal_code' => 'foo-postal-code',
            ]);

        $returnResponse = $action($request);

        self::assertSame(
            $response,
            $returnResponse
        );
    }

    public function test() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            PostAdminUserCreateResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Payload $payload) use ($response) {
                    self::assertSame(
                        Payload::STATUS_CREATED,
                        $payload->getStatus(),
                    );

                    self::assertSame(
                        [],
                        $payload->getResult(),
                    );

                    return $response;
                }
            );

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('validatePostalCode')
            ->with(
                self::equalTo('foo-postal-code'),
                self::equalTo('foo-billing-country'),
            )
            ->willReturn(true);

        $userApi->expects(self::once())
            ->method('fillModelFromPostalCode')
            ->willReturnCallback(
                static function (UserModel $user) : void {
                    $user->billingCity = 'foo-city-fill';

                    $user->billingStateAbbr = 'foo-state-abbr-fill';
                }
            );

        $userApi->expects(self::once())
            ->method('saveUser')
            ->willReturnCallback(
                static function (UserModel $user) : Payload {
                    self::assertTrue($user->isAdmin);

                    self::assertSame(
                        'foo@bar.baz',
                        $user->emailAddress,
                    );

                    self::assertSame(
                        'foo-password',
                        $user->newPassword,
                    );

                    self::assertSame(
                        'America/Chicago',
                        $user->timezone->getName(),
                    );

                    self::assertSame(
                        'Foo First Name',
                        $user->firstName,
                    );

                    self::assertSame(
                        'Foo Last Name',
                        $user->lastName,
                    );

                    self::assertSame(
                        'Foo Display Name',
                        $user->displayName,
                    );

                    self::assertSame(
                        'Foo Billing Name',
                        $user->billingName,
                    );

                    self::assertSame(
                        'Foo Billing Company',
                        $user->billingCompany,
                    );

                    self::assertSame(
                        'Foo Billing Phone',
                        $user->billingPhone,
                    );

                    self::assertSame(
                        'Foo Billing Address',
                        $user->billingAddress,
                    );

                    self::assertSame(
                        'foo-billing-country',
                        $user->billingCountry,
                    );

                    self::assertSame(
                        'foo-postal-code',
                        $user->billingPostalCode,
                    );

                    self::assertSame(
                        'foo-city-fill',
                        $user->billingCity,
                    );

                    self::assertSame(
                        'foo-state-abbr-fill',
                        $user->billingStateAbbr,
                    );

                    return new Payload(Payload::STATUS_CREATED);
                }
            );

        $action = new PostAdminUserCreateAction(
            $responder,
            $userApi,
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn([
                'is_admin' => 'true',
                'email_address' => 'foo@bar.baz',
                'password' => 'foo-password',
                'timezone' => 'America/Chicago',
                'first_name' => 'Foo First Name',
                'last_name' => 'Foo Last Name',
                'display_name' => 'Foo Display Name',
                'billing_name' => 'Foo Billing Name',
                'billing_company' => 'Foo Billing Company',
                'billing_phone' => 'Foo Billing Phone',
                'billing_address' => 'Foo Billing Address',
                'billing_country' => 'foo-billing-country',
                'billing_postal_code' => 'foo-postal-code',
            ]);

        $returnResponse = $action($request);

        self::assertSame(
            $response,
            $returnResponse
        );
    }
}
