<?php

declare(strict_types=1);

namespace Tests\Http\Account\PaymentMethods\Create;

use App\Factories\ValidationFactory;
use App\Http\Account\PaymentMethods\Create\PostCreatePaymentMethodAction;
use App\Http\Account\PaymentMethods\Create\PostCreatePaymentMethodResponder;
use App\Payload\Payload;
use App\Users\Models\LoggedInUser;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestConfig;

class PostCreatePaymentMethodActionTest extends TestCase
{
    public function testInvalid() : void
    {
        $response = $this->createMock(
            ResponseInterface::class,
        );

        $user = new UserModel();

        $loggedInUser = new LoggedInUser($user);

        $responder = $this->createMock(
            PostCreatePaymentMethodResponder::class,
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Payload $payload) use (
                    $response
                ) : ResponseInterface {
                    self::assertSame(
                        Payload::STATUS_NOT_VALID,
                        $payload->getStatus()
                    );

                    self::assertSame(
                        $payload->getResult(),
                        [
                            'message' => 'The data provided was invalid',
                            'inputMessages' => [
                                'card_number' => ['Value must be a valid credit card number'],
                                'expiration_month' => [
                                    'Value must not be empty',
                                    'Value must be numeric',
                                ],
                                'expiration_year' => [
                                    'Value must not be empty',
                                    'Value must be numeric',
                                ],
                                'expiration_date' => 'Valid expiration required',
                            ],
                        ],
                    );

                    return $response;
                }
            );

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getParsedBody')
            ->willReturn(['card_number' => '4242424242424243']);

        $action = new PostCreatePaymentMethodAction(
            $responder,
            TestConfig::$di->get(
                ValidationFactory::class,
            ),
            $loggedInUser,
            $userApi,
        );

        self::assertSame(
            $response,
            $action($request),
        );
    }

    public function test() : void
    {
        $masterPayload = new Payload(Payload::STATUS_ERROR);

        $response = $this->createMock(
            ResponseInterface::class,
        );

        $user = new UserModel();

        $loggedInUser = new LoggedInUser($user);

        $responder = $this->createMock(
            PostCreatePaymentMethodResponder::class,
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (Payload $payload) use (
                    $response,
                    $masterPayload
                ) : ResponseInterface {
                    self::assertSame(
                        $masterPayload,
                        $payload,
                    );

                    return $response;
                }
            );

        $userApi = $this->createMock(
            UserApi::class
        );

        $userApi->expects(self::once())
            ->method('saveUserCard')
            ->willReturnCallback(
                static function (UserCardModel $card) use (
                    $user,
                    $masterPayload
                ) : Payload {
                    self::assertSame(
                        $user,
                        $card->user,
                    );

                    self::assertSame(
                        'foo-bar',
                        $card->nickname,
                    );

                    self::assertSame(
                        '4242424242424242',
                        $card->newCardNumber,
                    );

                    self::assertTrue($card->isDefault);

                    $month = $card->expiration->format('m');

                    self::assertSame(
                        '05',
                        $month,
                    );

                    $year = $card->expiration->format('Y');

                    self::assertSame(
                        '2030',
                        $year,
                    );

                    return $masterPayload;
                }
            );

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getParsedBody')
            ->willReturn([
                'card_number' => '4242424242424242',
                'expiration_date' => [
                    'month' => '05',
                    'year' => '2030',
                ],
                'default' => 'true',
                'nickname' => 'foo-bar',
            ]);

        $action = new PostCreatePaymentMethodAction(
            $responder,
            TestConfig::$di->get(
                ValidationFactory::class,
            ),
            $loggedInUser,
            $userApi,
        );

        self::assertSame(
            $response,
            $action($request),
        );
    }
}
