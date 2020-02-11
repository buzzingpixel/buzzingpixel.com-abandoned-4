<?php

declare(strict_types=1);

namespace Tests\Orders\Services\SaveOrder;

use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Orders\Services\SaveOrder\SaveExistingOrder;
use App\Orders\Services\SaveOrder\SaveNewOrder;
use App\Orders\Services\SaveOrder\SaveOrderItemMaster;
use App\Orders\Services\SaveOrder\SaveOrderMaster;
use App\Payload\Payload;
use Exception;
use PDO;
use PHPUnit\Framework\TestCase;

class SaveOrderMasterTest extends TestCase
{
    public function testSaveNewOrder() : void
    {
        $orderItemModel1 = new OrderItemModel();
        $orderItemModel2 = new OrderItemModel();
        $orderModel      = new OrderModel();
        $orderModel->addItem($orderItemModel1);
        $orderModel->addItem($orderItemModel2);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo->expects(self::at(1))
            ->method('commit')
            ->willReturn(true);

        $saveNewOrder = $this->createMock(
            SaveNewOrder::class
        );

        $saveNewOrder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($orderModel));

        $saveExistingOrder = $this->createMock(
            SaveExistingOrder::class
        );

        $saveExistingOrder->expects(self::never())->method(self::anything());

        $saveOrderItemMaster = $this->createMock(
            SaveOrderItemMaster::class
        );

        $saveOrderItemMaster->expects(self::at(0))
            ->method('__invoke')
            ->with(self::equalTo($orderItemModel1));

        $saveOrderItemMaster->expects(self::at(1))
            ->method('__invoke')
            ->with(self::equalTo($orderItemModel2));

        $saveOrder = new SaveOrderMaster(
            $pdo,
            $saveNewOrder,
            $saveExistingOrder,
            $saveOrderItemMaster
        );

        $payload = $saveOrder($orderModel);

        self::assertSame(
            Payload::STATUS_CREATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());
    }

    public function testSaveExistingOrder() : void
    {
        $orderItemModel1 = new OrderItemModel();
        $orderModel      = new OrderModel();
        $orderModel->id  = 'foo';
        $orderModel->addItem($orderItemModel1);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo->expects(self::at(1))
            ->method('commit')
            ->willReturn(true);

        $saveNewOrder = $this->createMock(
            SaveNewOrder::class
        );

        $saveNewOrder->expects(self::never())->method(self::anything());

        $saveExistingOrder = $this->createMock(
            SaveExistingOrder::class
        );

        $saveExistingOrder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($orderModel));

        $saveOrderItemMaster = $this->createMock(
            SaveOrderItemMaster::class
        );

        $saveOrderItemMaster->expects(self::at(0))
            ->method('__invoke')
            ->with(self::equalTo($orderItemModel1));

        $saveOrder = new SaveOrderMaster(
            $pdo,
            $saveNewOrder,
            $saveExistingOrder,
            $saveOrderItemMaster
        );

        $payload = $saveOrder($orderModel);

        self::assertSame(
            Payload::STATUS_UPDATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());
    }

    public function testSaveWhenException() : void
    {
        $orderItemModel1 = new OrderItemModel();
        $orderModel      = new OrderModel();
        $orderModel->id  = 'foo';
        $orderModel->addItem($orderItemModel1);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willThrowException(new Exception());

        $pdo->expects(self::at(1))
            ->method('rollBack')
            ->willReturn(true);

        $saveNewOrder = $this->createMock(
            SaveNewOrder::class
        );

        $saveNewOrder->expects(self::never())->method(self::anything());

        $saveExistingOrder = $this->createMock(
            SaveExistingOrder::class
        );

        $saveExistingOrder->expects(self::never())->method(self::anything());

        $saveOrderItemMaster = $this->createMock(
            SaveOrderItemMaster::class
        );

        $saveOrderItemMaster->expects(self::never())->method(self::anything());

        $saveOrder = new SaveOrderMaster(
            $pdo,
            $saveNewOrder,
            $saveExistingOrder,
            $saveOrderItemMaster
        );

        $payload = $saveOrder($orderModel);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'An unknown error occurred'],
            $payload->getResult()
        );
    }
}
