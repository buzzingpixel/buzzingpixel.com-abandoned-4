<?php

declare(strict_types=1);

namespace Tests\Orders\Models;

use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Safe\DateTimeImmutable;
use Throwable;

class OrderModelTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testInitialDate() : void
    {
        $now = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        $model = new OrderModel();

        $formatString = 'Y-m-d h:i:s';

        self::assertSame(
            $now->format($formatString),
            $model->date->format($formatString),
        );
    }

    public function testIncorrectPropertySet() : void
    {
        $model = new OrderModel();

        self::expectException(RuntimeException::class);

        self::expectExceptionMessage('Property does not exist');

        $model->asdf = 'foo';
    }

    public function testGetIncorrectProperty() : void
    {
        $model = new OrderModel();

        self::expectException(RuntimeException::class);

        self::expectExceptionMessage('Property does not exist');

        $model->asdf;
    }

    public function testItemsNotArray() : void
    {
        $model = new OrderModel();

        $exception = null;

        try {
            $model->items = 'foo';
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(
            Throwable::class,
            $exception,
        );
    }

    public function testItemNotInstance() : void
    {
        $model = new OrderModel();

        $exception = null;

        try {
            $model->items = [
                new OrderItemModel(),
                'foo',
            ];
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(
            Throwable::class,
            $exception,
        );
    }

    public function testItems() : void
    {
        $items = [
            new OrderItemModel(),
            new OrderItemModel(),
        ];

        $model = new OrderModel();

        $model->items = $items;

        self::assertSame(
            $items,
            $model->items,
        );
    }

    public function testIsSet() : void
    {
        $model = new OrderModel();

        self::assertTrue(isset($model->items));

        self::assertFalse(isset($model->asdf));
    }
}
