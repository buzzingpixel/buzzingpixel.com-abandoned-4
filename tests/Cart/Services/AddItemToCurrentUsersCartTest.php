<?php

declare(strict_types=1);

namespace Tests\Cart\Services;

use App\Cart\CartApi;
use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Cart\Services\AddItemToCurrentUsersCart;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use PHPUnit\Framework\TestCase;

class AddItemToCurrentUsersCartTest extends TestCase
{
    public function testExistingItems1() : void
    {
        $software1       = new SoftwareModel();
        $software1->slug = 'foo-slug-1';
        $software2       = new SoftwareModel();
        $software2->slug = 'foo-slug-2';

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

        $service = new AddItemToCurrentUsersCart($cartApi);

        $service($software2);

        self::assertSame(1, $cartItem1->quantity);

        self::assertSame(2, $cartItem2->quantity);
    }

    public function testExistingItems2() : void
    {
        $software1       = new SoftwareModel();
        $software1->slug = 'foo-slug-1';
        $software2       = new SoftwareModel();
        $software2->slug = 'foo-slug-2';

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

        $service = new AddItemToCurrentUsersCart($cartApi);

        $service($software1);

        self::assertSame(2, $cartItem1->quantity);

        self::assertSame(1, $cartItem2->quantity);
    }

    public function testNewItem() : void
    {
        $software1       = new SoftwareModel();
        $software1->slug = 'foo-slug-1';
        $software2       = new SoftwareModel();
        $software2->slug = 'foo-slug-2';
        $software3       = new SoftwareModel();
        $software3->slug = 'foo-slug-3';

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

        $service = new AddItemToCurrentUsersCart($cartApi);

        $service($software3);

        self::assertSame(1, $cartItem1->quantity);

        self::assertSame(1, $cartItem2->quantity);

        self::assertCount(3, $cart->items);

        $lastItem = $cart->items[2];

        self::assertSame(1, $lastItem->quantity);

        self::assertSame($software3, $lastItem->software);
    }
}
