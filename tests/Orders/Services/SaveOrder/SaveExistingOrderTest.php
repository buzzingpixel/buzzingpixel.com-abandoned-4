<?php

declare(strict_types=1);

namespace Tests\Orders\Services\SaveOrder;

use App\Orders\Models\OrderModel;
use App\Orders\Services\SaveOrder\SaveExistingOrder;
use App\Orders\Transformers\TransformOrderModelToRecord;
use App\Payload\Payload;
use App\Persistence\Orders\OrderRecord;
use App\Persistence\SaveExistingRecord;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;
use function assert;

class SaveExistingOrderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSaveExistingOrder() : void
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
                    OrderRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_UPDATED);
                }
            );

        $saveExistingOrder = new SaveExistingOrder(
            $saveExistingRecord,
            new TransformOrderModelToRecord(),
        );

        $orderModel = new OrderModel();

        $orderModel->name = 'U.S.S. Enterprise';

        $saveExistingOrder($orderModel);

        assert($saveCallHolder->record instanceof OrderRecord);

        self::assertSame(
            $orderModel->name,
            $saveCallHolder->record->name,
        );
    }

    /**
     * @throws Exception
     */
    public function testSaveExistingOrderInvalidPayloadReturnStatus() : void
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
                    OrderRecord $record
                ) use ($saveCallHolder) : Payload {
                    $saveCallHolder->record = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $saveExistingOrder = new SaveExistingOrder(
            $saveExistingRecord,
            new TransformOrderModelToRecord(),
        );

        $orderModel = new OrderModel();

        $orderModel->name = 'U.S.S. Enterprise';

        $exception = null;

        try {
            $saveExistingOrder($orderModel);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof Exception);

        self::assertSame(
            'Unknown error saving order',
            $exception->getMessage(),
        );

        assert($saveCallHolder->record instanceof OrderRecord);

        self::assertSame(
            $orderModel->name,
            $saveCallHolder->record->name,
        );
    }
}
