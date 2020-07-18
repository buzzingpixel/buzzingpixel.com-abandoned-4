<?php

declare(strict_types=1);

namespace Tests\Cart\OrderProcessors;

use App\Cart\CartApi;
use App\Cart\Models\CartModel;
use App\Cart\Models\ProcessOrderModel;
use App\Cart\OrderProcessors\ClearCartAtEnd;
use App\Orders\Models\OrderModel;
use App\Users\Models\UserCardModel;
use PHPUnit\Framework\TestCase;

class ClearCartAtEndTest extends TestCase
{
    public function test(): void
    {
        $cart = new CartModel();

        $card = new UserCardModel();

        $order = new OrderModel();

        $processOrderModel = new ProcessOrderModel(
            $cart,
            $card,
            $order
        );

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('clearCart');

        $service = new ClearCartAtEnd($cartApi);

        self::assertSame(
            $processOrderModel,
            $service($processOrderModel),
        );
    }
}
