<?php

declare(strict_types=1);

namespace Tests\Orders\Transformers;

use App\Licenses\Models\LicenseModel;
use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Orders\Transformers\TransformOrderItemModelToRecord;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use function assert;

class TransformOrderItemModelToRecordTest extends TestCase
{
    public function testTransformer() : void
    {
        $model = new OrderItemModel();

        $model->id = 'barId';

        $order        = new OrderModel();
        $model->id    = 'fooId';
        $model->order = $order;

        $license        = new LicenseModel();
        $license->id    = 'bazId';
        $model->license = $license;

        $model->itemKey = 'kirk';

        $model->itemTitle = 'Kirk';

        $model->majorVersion = '2';

        $model->version = '2.3.4.';

        $model->price = (4.56);

        $model->originalPrice = 7.89;

        $model->isUpgrade = true;

        $model->hasBeenUpgraded = true;

        $expires = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            '2010-02-11T01:26:03+00:00',
        );

        assert($expires instanceof DateTimeImmutable);

        $model->expires = $expires;

        $transformer = new TransformOrderItemModelToRecord();

        $record = $transformer($model);

        self::assertSame(
            $model->id,
            $record->id,
        );

        self::assertSame(
            $order->id,
            $record->order_id,
        );

        self::assertSame(
            $license->id,
            $record->license_id,
        );

        self::assertSame(
            $model->itemKey,
            $record->item_key,
        );

        self::assertSame(
            $model->itemTitle,
            $record->item_title,
        );

        self::assertSame(
            $model->majorVersion,
            $record->major_version,
        );

        self::assertSame(
            $model->version,
            $record->version,
        );

        self::assertSame(
            $model->price,
            $record->price,
        );

        self::assertSame(
            $model->originalPrice,
            $record->original_price,
        );

        self::assertSame(
            '1',
            $record->is_upgrade,
        );

        self::assertSame(
            '1',
            $record->has_been_upgraded,
        );

        self::assertSame(
            $model->expires->format(DateTimeInterface::ATOM),
            $record->expires,
        );
    }
}
