<?php

declare(strict_types=1);

namespace Tests\Http\Account\ChangePassword;

use App\Http\Account\ChangePassword\GetChangePasswordAction;
use App\Http\Account\ChangePassword\GetChangePasswordResponder;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetChangePasswordActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $responder = $this->createMock(
            GetChangePasswordResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willReturn($response);

        $action = new GetChangePasswordAction(
            $userApi,
            $responder,
        );

        self::assertSame($response, $action());
    }
}
