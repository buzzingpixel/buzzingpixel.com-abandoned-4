<?php

declare(strict_types=1);

namespace Tests\Http\Account\PaymentMethods;

use App\Http\Account\PaymentMethods\GetAccountPaymentMethodsAction;
use App\Http\Account\PaymentMethods\GetAccountPaymentMethodsResponder;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountPaymentMethodsActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $userCards = [new UserCardModel()];

        $user = new UserModel();

        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(
            GetAccountPaymentMethodsResponder::class,
        );

        $userApi = $this->createMock(
            UserApi::class,
        );

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $userApi->expects(self::once())
            ->method('fetchUserCards')
            ->with(self::equalTo($user))
            ->willReturn($userCards);

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($userCards))
            ->willReturn($response);

        $action = new GetAccountPaymentMethodsAction(
            $responder,
            $userApi
        );

        self::assertSame(
            $response,
            $action(),
        );
    }
}
