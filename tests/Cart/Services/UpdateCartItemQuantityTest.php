<?php

declare(strict_types=1);

namespace Tests\Cart\Services;

use App\Cart\CartApi;
use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Cart\Services\UpdateCartItemQuantity;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use PHPUnit\Framework\TestCase;

class UpdateCartItemQuantityTest extends TestCase
{
    public function test() : void
    {
        $software1       = new SoftwareModel();
        $software1->slug = 'foo-slug-1';
        $item1           = new CartItemModel();
        $item1->quantity = 2;
        $item1->software = $software1;

        $software2       = new SoftwareModel();
        $software2->slug = 'foo-slug-2';
        $item2           = new CartItemModel();
        $item2->quantity = 4;
        $item2->software = $software2;

        $cart = new CartModel();
        $cart->addItem($item1);
        $cart->addItem($item2);

        $cartApi = $this->createMock(CartApi::class);

        $cartApi->expects(self::once())
            ->method('fetchCurrentUserCart')
            ->willReturn($cart);

        $cartApi->expects(self::once())
            ->method('saveCart')
            ->with(self::equalTo($cart))
            ->willReturn(new Payload(Payload::STATUS_SUCCESSFUL));

        $service = new UpdateCartItemQuantity($cartApi);

        $service(18, $software1);

        self::assertSame(18, $item1->quantity);

        self::assertSame(4, $item2->quantity);
    }
}
