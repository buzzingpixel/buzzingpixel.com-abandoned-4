<?php

declare(strict_types=1);

namespace Tests\Http\Account\Purchases\Printing;

use App\Http\Account\Purchases\Printing\GetAccountPurchasePrintAction;
use App\Http\Account\Purchases\Printing\GetAccountPurchasePrintResponder;
use App\Orders\Models\OrderModel;
use App\Orders\OrderApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function assert;

class GetAccountPurchasePrintActionTest extends TestCase
{
    public function testWhenNoOrder() : void
    {
        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $orderApi = $this->createMock(OrderApi::class);

        $orderApi->expects(self::once())
            ->method('fetchCurrentUserOrderById')
            ->with(self::equalTo('foo-id'))
            ->willReturn(null);

        $responder = $this->createMock(
            GetAccountPurchasePrintResponder::class
        );

        $responder->expects(self::never())
            ->method(self::anything());

        $action = new GetAccountPurchasePrintAction(
            $responder,
            $orderApi,
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
        $response = $this->createMock(ResponseInterface::class);

        $order = new OrderModel();

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $request->expects(self::once())
            ->method('getAttribute')
            ->with(self::equalTo('id'))
            ->willReturn('foo-id');

        $orderApi = $this->createMock(OrderApi::class);

        $orderApi->expects(self::once())
            ->method('fetchCurrentUserOrderById')
            ->with(self::equalTo('foo-id'))
            ->willReturn($order);

        $responder = $this->createMock(
            GetAccountPurchasePrintResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($order))
            ->willReturn($response);

        $action = new GetAccountPurchasePrintAction(
            $responder,
            $orderApi,
        );

        self::assertSame($response, $action($request));
    }
}
