<?php

declare(strict_types=1);

namespace Tests\Orders\Services\SaveOrder;

use App\Orders\Models\OrderItemModel;
use App\Orders\Services\SaveOrder\SaveExistingOrderItem;
use App\Orders\Services\SaveOrder\SaveNewOrderItem;
use App\Orders\Services\SaveOrder\SaveOrderItemMaster;
use PHPUnit\Framework\TestCase;

class SaveOrderItemMasterTest extends TestCase
{
    public function testSaveNewOrderItem() : void
    {
        $model = new OrderItemModel();

        $saveNewOrderItem = $this->createMock(
            SaveNewOrderItem::class
        );

        $saveNewOrderItem->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($model));

        $saveExistingOrderItem = $this->createMock(
            SaveExistingOrderItem::class
        );

        $saveExistingOrderItem->expects(self::never())
            ->method(self::anything());

        $saveOrderItemMaster = new SaveOrderItemMaster(
            $saveNewOrderItem,
            $saveExistingOrderItem
        );

        $saveOrderItemMaster($model);
    }

    public function testSaveExistingOrderItem() : void
    {
        $model = new OrderItemModel();

        $model->id = 'foo';

        $saveNewOrderItem = $this->createMock(
            SaveNewOrderItem::class
        );

        $saveNewOrderItem->expects(self::never())
            ->method(self::anything());

        $saveExistingOrderItem = $this->createMock(
            SaveExistingOrderItem::class
        );

        $saveExistingOrderItem->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($model));

        $saveOrderItemMaster = new SaveOrderItemMaster(
            $saveNewOrderItem,
            $saveExistingOrderItem
        );

        $saveOrderItemMaster($model);
    }
}
