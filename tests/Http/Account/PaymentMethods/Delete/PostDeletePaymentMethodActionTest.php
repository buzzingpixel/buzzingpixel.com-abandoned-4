<?php

declare(strict_types=1);

namespace Tests\Http\Account\PaymentMethods\Delete;

use App\Http\Account\PaymentMethods\Delete\PostDeletePaymentMethodAction;
use App\Http\Account\PaymentMethods\Delete\PostDeletePaymentMethodResponder;
use App\Payload\Payload;
use App\Users\Models\LoggedInUser;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function assert;

class PostDeletePaymentMethodActionTest extends TestCase
{
    public function testWhenNoCard() : void
    {
        $id = 'foo-card-id';

        $userModel = new UserModel();
        $user      = new LoggedInUser($userModel);

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserCardById')
            ->with(
                self::equalTo($userModel),
                self::equalTo($id),
            )
            ->willReturn(null);

        $responder = $this->createMock(
            PostDeletePaymentMethodResponder::class,
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn($id);

        $action = new PostDeletePaymentMethodAction(
            $user,
            $userApi,
            $responder,
        );

        $exception = null;

        try {
            $action($request);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertSame($request, $exception->getRequest());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $response = $this->createMock(
            ResponseInterface::class,
        );

        $payload = new Payload(Payload::STATUS_DELETED);

        $id = 'foo-card-id';

        $userModel = new UserModel();
        $user      = new LoggedInUser($userModel);

        $userCard = new UserCardModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserCardById')
            ->with(
                self::equalTo($userModel),
                self::equalTo($id),
            )
            ->willReturn($userCard);

        $userApi->expects(self::once())
            ->method('deleteUserCard')
            ->with(
                self::equalTo($userCard),
            )
            ->willReturn($payload);

        $responder = $this->createMock(
            PostDeletePaymentMethodResponder::class,
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($payload))
            ->willReturn($response);

        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn($id);

        $action = new PostDeletePaymentMethodAction(
            $user,
            $userApi,
            $responder,
        );

        self::assertSame(
            $response,
            $action($request),
        );
    }
}
