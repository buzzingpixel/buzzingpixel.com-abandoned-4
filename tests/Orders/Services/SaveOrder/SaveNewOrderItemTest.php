<?php

declare(strict_types=1);

namespace Tests\Orders\Services\SaveOrder;

use App\Licenses\Models\LicenseModel;
use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Orders\Services\SaveOrder\SaveNewOrderItem;
use App\Orders\Transformers\TransformOrderItemModelToRecord;
use App\Payload\Payload;
use App\Persistence\Orders\OrderItemRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\TestConfig;
use Throwable;
use function assert;

class SaveNewOrderItemTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSaveNewOrderItem() : void
    {
        $uuid = TestConfig::$di->get(UuidFactoryWithOrderedTimeCodec::class)
            ->uuid1();

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::once())
            ->method('uuid1')
            ->willReturn($uuid);

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class,
        );

        $saveCallHolder = new stdClass();

        $saveCallHolder->record = null;

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    OrderItemRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_CREATED);
                }
            );

        $saveNewOrderItem = new SaveNewOrderItem(
            $saveNewRecord,
            new TransformOrderItemModelToRecord(),
            $uuidFactory
        );

        $orderModel     = new OrderModel();
        $orderModel->id = 'FooOrderId';

        $licenseModel     = new LicenseModel();
        $licenseModel->id = 'FooLicenseId';

        $orderItemModel               = new OrderItemModel();
        $orderItemModel->order        = $orderModel;
        $orderItemModel->license      = $licenseModel;
        $orderItemModel->majorVersion = 'Foo';

        $saveNewOrderItem($orderItemModel);

        self::assertSame(
            $uuid->toString(),
            $orderItemModel->id,
        );

        assert($saveCallHolder->record instanceof OrderItemRecord);

        self::assertSame(
            $uuid->toString(),
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
        $uuid = TestConfig::$di->get(UuidFactoryWithOrderedTimeCodec::class)
            ->uuid1();

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->expects(self::once())
            ->method('uuid1')
            ->willReturn($uuid);

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class,
        );

        $saveCallHolder = new stdClass();

        $saveCallHolder->record = null;

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (
                    OrderItemRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $saveNewOrderItem = new SaveNewOrderItem(
            $saveNewRecord,
            new TransformOrderItemModelToRecord(),
            $uuidFactory
        );

        $orderModel     = new OrderModel();
        $orderModel->id = 'FooOrderId';

        $licenseModel     = new LicenseModel();
        $licenseModel->id = 'FooLicenseId';

        $orderItemModel               = new OrderItemModel();
        $orderItemModel->order        = $orderModel;
        $orderItemModel->license      = $licenseModel;
        $orderItemModel->majorVersion = 'Foo';

        $exception = null;

        try {
            $saveNewOrderItem($orderItemModel);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof Exception);

        self::assertSame(
            'Unknown error saving order item',
            $exception->getMessage(),
        );

        self::assertSame(
            $uuid->toString(),
            $orderItemModel->id,
        );

        assert($saveCallHolder->record instanceof OrderItemRecord);

        self::assertSame(
            $uuid->toString(),
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
