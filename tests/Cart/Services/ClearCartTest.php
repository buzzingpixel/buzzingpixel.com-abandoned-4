<?php

declare(strict_types=1);

namespace Tests\Cart\Services;

use App\Cart\CartApi;
use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Cart\Services\ClearCart;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use PHPUnit\Framework\TestCase;

class ClearCartTest extends TestCase
{
    public function testClearCart() : void
    {
        $software1 = new SoftwareModel();
        $software2 = new SoftwareModel();

        $cartItem1           = new CartItemModel();
        $cartItem1->software = $software1;
        $cartItem1->quantity = 1;
        $cartItem2           = new CartItemModel();
        $cartItem2->software = $software2;
        $cartItem2->quantity = 1;

        $cart = new CartModel();
        $cart->addItem($cartItem1);
        $cart->addItem($cartItem2);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::at(0))
            ->method('fetchCurrentUserCart')
            ->willReturn($cart);

        $cartApi->expects(self::at(1))
            ->method('saveCart')
            ->with(self::equalTo($cart))
            ->willReturn(new Payload(Payload::STATUS_SUCCESSFUL));

        $service = new ClearCart($cartApi);

        $service();

        self::assertSame(0, $cartItem1->quantity);

        self::assertSame(0, $cartItem2->quantity);
    }
}
