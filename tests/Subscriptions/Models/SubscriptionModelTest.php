<?php

declare(strict_types=1);

namespace Tests\Subscriptions\Models;

use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Subscriptions\Models\SubscriptionModel;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;
use TypeError;

class SubscriptionModelTest extends TestCase
{
    public function testIsSetIncorrectProperty(): void
    {
        $model = new SubscriptionModel();

        self::assertFalse(isset($model->foo));

        self::assertFalse(isset($model->bar));

        self::assertTrue(isset($model->orders));
    }

    public function testAddOrderWhenNotOrder(): void
    {
        $exception = null;

        $model = new SubscriptionModel();

        try {
            /** @phpstan-ignore-next-line */
            $model->orders = [
                new OrderModel(),
                new OrderItemModel(),
                'foo-test',
            ];
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(
            TypeError::class,
            $exception
        );
    }

    public function testAddOrderWhenNotArray(): void
    {
        $exception = null;

        $model = new SubscriptionModel();

        try {
            /** @phpstan-ignore-next-line */
            $model->orders = '';
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertNotNull($exception);
    }

    public function testGetInvalidProperty(): void
    {
        $exception = null;

        $model = new SubscriptionModel();

        try {
            /** @phpstan-ignore-next-line */
            $foo = $model->foo;
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(
            RuntimeException::class,
            $exception
        );
    }

    public function testAddInvalidProperty(): void
    {
        $exception = null;

        $model = new SubscriptionModel();

        try {
            /** @phpstan-ignore-next-line */
            $model->foo = 'bar';
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(
            RuntimeException::class,
            $exception
        );
    }

    public function testAddOrders(): void
    {
        $model = new SubscriptionModel();

        $order1 = new OrderModel();

        $order2 = new OrderModel();

        $model->orders = [
            $order1,
            $order2,
        ];

        self::assertSame(
            [$order1, $order2],
            $model->orders,
        );
    }

    public function testGetOrders(): void
    {
        $model = new SubscriptionModel();

        $order1 = new OrderModel();

        $order2 = new OrderModel();

        $model->addOrder($order1);

        $model->addOrder($order2);

        self::assertSame(
            [$order1, $order2],
            $model->orders,
        );
    }
}
