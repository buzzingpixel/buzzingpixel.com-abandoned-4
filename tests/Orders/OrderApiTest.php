<?php

declare(strict_types=1);

namespace Tests\Orders;

use App\Orders\Models\OrderModel;
use App\Orders\OrderApi;
use App\Orders\Services\SaveOrder\SaveOrderMaster;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class OrderApiTest extends TestCase
{
    public function testSaveOrder() : void
    {
        $payload = new Payload(Payload::STATUS_CREATED);

        $orderModel = new OrderModel();

        $saveOrder = $this->createMock(SaveOrderMaster::class);

        $saveOrder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($orderModel))
            ->willReturn($payload);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(SaveOrderMaster::class))
            ->willReturn($saveOrder);

        $api = new OrderApi($di);

        $returnPayload = $api->saveOrder($orderModel);

        self::assertSame($payload, $returnPayload);
    }
}
