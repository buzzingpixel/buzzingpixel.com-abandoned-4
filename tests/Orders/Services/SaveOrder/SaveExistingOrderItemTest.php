<?php

declare(strict_types=1);

namespace Tests\Orders\Services\SaveOrder;

use App\Licenses\Models\LicenseModel;
use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Orders\Services\SaveOrder\SaveExistingOrderItem;
use App\Orders\Transformers\TransformOrderItemModelToRecord;
use App\Payload\Payload;
use App\Persistence\Orders\OrderItemRecord;
use App\Persistence\SaveExistingRecord;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;
use function assert;

class SaveExistingOrderItemTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSaveNewOrderItem() : void
    {
        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $saveCallHolder = new stdClass();

        $saveCallHolder->record = null;

        $saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    OrderItemRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_UPDATED);
                }
            );

        $saveExistingOrderItem = new SaveExistingOrderItem(
            $saveExistingRecord,
            new TransformOrderItemModelToRecord(),
        );

        $orderModel     = new OrderModel();
        $orderModel->id = 'FooOrderId';

        $licenseModel     = new LicenseModel();
        $licenseModel->id = 'FooLicenseId';

        $orderItemModel               = new OrderItemModel();
        $orderItemModel->id           = 'fooItemId';
        $orderItemModel->order        = $orderModel;
        $orderItemModel->license      = $licenseModel;
        $orderItemModel->majorVersion = 'Foo';

        $saveExistingOrderItem($orderItemModel);

        assert($saveCallHolder->record instanceof OrderItemRecord);

        self::assertSame(
            $orderItemModel->id,
            $saveCallHolder->record->id,
        );

        self::assertSame(
            $orderModel->id,
            $saveCallHolder->record->order_id,
        );

        self::assertSame(
            $licenseModel->id,
            $saveCallHolder->record->license_id,
        );

        self::assertSame(
            $orderItemModel->majorVersion,
            $saveCallHolder->record->major_version,
        );
    }

    /**
     * @throws Exception
     */
    public function testSaveNewOrderItemInvalidPayloadReturnStatus() : void
    {
        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $saveCallHolder = new stdClass();

        $saveCallHolder->record = null;

        $saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    OrderItemRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_CREATED);
                }
            );

        $saveExistingOrderItem = new SaveExistingOrderItem(
            $saveExistingRecord,
            new TransformOrderItemModelToRecord(),
        );

        $orderModel     = new OrderModel();
        $orderModel->id = 'FooOrderId';

        $licenseModel     = new LicenseModel();
        $licenseModel->id = 'FooLicenseId';

        $orderItemModel               = new OrderItemModel();
        $orderItemModel->id           = 'fooItemId';
        $orderItemModel->order        = $orderModel;
        $orderItemModel->license      = $licenseModel;
        $orderItemModel->majorVersion = 'Foo';

        $exception = null;

        try {
            $saveExistingOrderItem($orderItemModel);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof Exception);

        self::assertSame(
            'Unknown error saving order item',
            $exception->getMessage(),
        );

        assert($saveCallHolder->record instanceof OrderItemRecord);

        self::assertSame(
            $orderItemModel->id,
            $saveCallHolder->record->id,
        );

        self::assertSame(
            $orderModel->id,
            $saveCallHolder->record->order_id,
        );

        self::assertSame(
            $licenseModel->id,
            $saveCallHolder->record->license_id,
        );

        self::assertSame(
            $orderItemModel->majorVersion,
            $saveCallHolder->record->major_version,
        );
    }
}
