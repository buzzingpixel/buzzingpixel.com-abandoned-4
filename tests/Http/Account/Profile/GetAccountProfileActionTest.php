<?php

declare(strict_types=1);

namespace Tests\Http\Account\Profile;

use App\Http\Account\Profile\GetAccountProfileAction;
use App\Http\Account\Profile\GetAccountProfileResponder;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountProfileActionTest extends TestCase
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
            GetAccountProfileResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willReturn($response);

        $action = new GetAccountProfileAction(
            $responder,
            $userApi,
        );

        self::assertSame($response, $action());
    }
}
