<?php

declare(strict_types=1);

namespace Tests\Http\Account\Purchases;

use App\Http\Account\Purchases\GetAccountPurchasesAction;
use App\Http\Account\Purchases\GetAccountPurchasesResponder;
use App\Orders\Models\OrderModel;
use App\Orders\OrderApi;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAccountPurchasesActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $response = $this->createMock(ResponseInterface::class);

        $orders = [
            new OrderModel(),
            new OrderModel(),
        ];

        $orderApi = $this->createMock(OrderApi::class);

        $orderApi->expects(self::once())
            ->method('fetchCurrentUserOrders')
            ->willReturn($orders);

        $responder = $this->createMock(
            GetAccountPurchasesResponder::class
        );

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($orders))
            ->willReturn($response);

        $action = new GetAccountPurchasesAction(
            $responder,
            $orderApi,
        );

        self::assertSame($response, $action());
    }
}
