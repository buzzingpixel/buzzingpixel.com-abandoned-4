<?php

declare(strict_types=1);

namespace Tests\Orders\Services\SaveOrder;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\SaveLicenseMaster;
use App\Orders\Models\OrderItemModel;
use App\Orders\Services\SaveOrder\SaveExistingOrderItem;
use App\Orders\Services\SaveOrder\SaveNewOrderItem;
use App\Orders\Services\SaveOrder\SaveOrderItemMaster;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Throwable;

class SaveOrderItemMasterTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testSaveNewOrderItem() : void
    {
        $licenseModel = new LicenseModel();

        $model = new OrderItemModel();

        $model->license = $licenseModel;

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

        $saveLicense = $this->createMock(
            SaveLicenseMaster::class
        );

        $saveLicense->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($licenseModel))
            ->willReturn(new Payload(Payload::STATUS_CREATED));

        $saveOrderItemMaster = new SaveOrderItemMaster(
            $saveNewOrderItem,
            $saveExistingOrderItem,
            $saveLicense
        );

        $saveOrderItemMaster($model);
    }

    /**
     * @throws Throwable
     */
    public function testSaveExistingOrderItem() : void
    {
        $licenseModel = new LicenseModel();

        $model = new OrderItemModel();

        $model->id = 'foo';

        $model->license = $licenseModel;

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

        $saveLicense = $this->createMock(
            SaveLicenseMaster::class
        );

        $saveLicense->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($licenseModel))
            ->willReturn(new Payload(Payload::STATUS_CREATED));

        $saveOrderItemMaster = new SaveOrderItemMaster(
            $saveNewOrderItem,
            $saveExistingOrderItem,
            $saveLicense
        );

        $saveOrderItemMaster($model);
    }
}
