<?php

declare(strict_types=1);

namespace Tests\Orders\Services\SaveOrder;

use App\Orders\Models\OrderModel;
use App\Orders\Services\SaveOrder\SaveNewOrder;
use App\Orders\Transformers\TransformOrderModelToRecord;
use App\Payload\Payload;
use App\Persistence\Orders\OrderRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\TestConfig;
use Throwable;
use function assert;

class SaveNewOrderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSaveNewOrder() : void
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
                    OrderRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_CREATED);
                }
            );

        $saveNewOrder = new SaveNewOrder(
            $saveNewRecord,
            new TransformOrderModelToRecord(),
            $uuidFactory,
        );

        $orderModel = new OrderModel();

        $orderModel->name = 'U.S.S. Enterprise';

        $saveNewOrder($orderModel);

        self::assertSame(
            $uuid->toString(),
            $orderModel->id,
        );

        assert($saveCallHolder->record instanceof OrderRecord);

        self::assertSame(
            $uuid->toString(),
            $saveCallHolder->record->id,
        );

        self::assertSame(
            $orderModel->name,
            $saveCallHolder->record->name,
        );
    }

    /**
     * @throws Exception
     */
    public function testSaveNewOrderInvalidPayloadReturnStatus() : void
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
                    OrderRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $saveNewOrder = new SaveNewOrder(
            $saveNewRecord,
            new TransformOrderModelToRecord(),
            $uuidFactory,
        );

        $orderModel = new OrderModel();

        $orderModel->name = 'U.S.S. Enterprise';

        $exception = null;

        try {
            $saveNewOrder($orderModel);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof Exception);

        self::assertSame(
            'Unknown error saving order',
            $exception->getMessage(),
        );

        self::assertSame(
            $uuid->toString(),
            $orderModel->id,
        );

        assert($saveCallHolder->record instanceof OrderRecord);

        self::assertSame(
            $uuid->toString(),
            $saveCallHolder->record->id,
        );

        self::assertSame(
            $orderModel->name,
            $saveCallHolder->record->name,
        );
    }
}
