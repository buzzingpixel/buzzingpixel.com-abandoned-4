<?php

declare(strict_types=1);

namespace Tests\Cart\OrderProcessors;

use App\Cart\Models\CartModel;
use App\Cart\Models\ProcessOrderModel;
use App\Cart\OrderProcessors\SaveOrderAfterPopulate;
use App\Orders\Models\OrderModel;
use App\Orders\Services\SaveOrder\SaveOrderMaster;
use App\Payload\Payload;
use App\Users\Models\UserCardModel;
use PHPUnit\Framework\TestCase;
use Throwable;

class SaveOrderAfterPopulateTest extends TestCase
{
    public function testWhenFail(): void
    {
        $cart = new CartModel();

        $card = new UserCardModel();

        $order     = new OrderModel();
        $order->id = 'foo-order-id';

        $processOrderModel = new ProcessOrderModel(
            $cart,
            $card,
            $order
        );

        $payload = new Payload(Payload::STATUS_ERROR);

        $saveOrder = $this->createMock(
            SaveOrderMaster::class
        );

        $saveOrder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($order),
                self::equalTo($order->id),
            )
            ->willReturn($payload);

        $service = new SaveOrderAfterPopulate($saveOrder);

        $exception = null;

        try {
            $service($processOrderModel);
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertInstanceOf(Throwable::class, $exception);
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $cart = new CartModel();

        $card = new UserCardModel();

        $order     = new OrderModel();
        $order->id = 'foo-order-id';

        $processOrderModel = new ProcessOrderModel(
            $cart,
            $card,
            $order
        );

        $payload = new Payload(Payload::STATUS_CREATED);

        $saveOrder = $this->createMock(
            SaveOrderMaster::class
        );

        $saveOrder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($order),
                self::equalTo($order->id),
            )
            ->willReturn($payload);

        $service = new SaveOrderAfterPopulate($saveOrder);

        self::assertSame(
            $processOrderModel,
            $service($processOrderModel),
        );
    }
}
