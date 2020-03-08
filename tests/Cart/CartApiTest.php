<?php

declare(strict_types=1);

namespace Tests\Cart;

use App\Cart\CartApi;
use App\Cart\Models\CartModel;
use App\Cart\Services\AddItemToCurrentUsersCart;
use App\Cart\Services\ClearCart;
use App\Cart\Services\FetchCurrentUserCart;
use App\Cart\Services\SaveCart;
use App\Cart\Services\UpdateCartItemQuantity;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class CartApiTest extends TestCase
{
    public function testFetchCurrentUserCart() : void
    {
        $cartModel = new CartModel();

        $service = $this->createMock(
            FetchCurrentUserCart::class
        );

        $service->method('__invoke')
            ->willReturn($cartModel);

        $di = $this->createMock(ContainerInterface::class);

        $di->method('get')
            ->with(self::equalTo(FetchCurrentUserCart::class))
            ->willReturn($service);

        self::assertSame(
            $cartModel,
            (new CartApi($di))->fetchCurrentUserCart()
        );
    }

    public function testSaveCart() : void
    {
        $payload = new Payload(Payload::STATUS_SUCCESSFUL);

        $cartModel = new CartModel();

        $service = $this->createMock(
            SaveCart::class
        );

        $service->method('__invoke')
            ->with(self::equalTo($cartModel))
            ->willReturn($payload);

        $di = $this->createMock(ContainerInterface::class);

        $di->method('get')
            ->with(self::equalTo(SaveCart::class))
            ->willReturn($service);

        self::assertSame(
            $payload,
            (new CartApi($di))->saveCart($cartModel)
        );
    }

    public function testAddItemToCurrentUsersCart() : void
    {
        $softwareModel = new SoftwareModel();

        $service = $this->createMock(
            AddItemToCurrentUsersCart::class
        );

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($softwareModel));

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(
                self::equalTo(AddItemToCurrentUsersCart::class)
            )
            ->willReturn($service);

        (new CartApi($di))->addItemToCurrentUsersCart(
            $softwareModel
        );
    }

    public function testUpdateCartItemQuantity() : void
    {
        $softwareModel = new SoftwareModel();

        $service = $this->createMock(
            UpdateCartItemQuantity::class
        );

        $service->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo(2),
                self::equalTo($softwareModel)
            );

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(
                self::equalTo(UpdateCartItemQuantity::class)
            )
            ->willReturn($service);

        (new CartApi($di))->updateCartItemQuantity(
            2,
            $softwareModel
        );
    }

    public function testClearCart() : void
    {
        $service = $this->createMock(
            ClearCart::class
        );

        $service->expects(self::once())
            ->method('__invoke');

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(ClearCart::class))
            ->willReturn($service);

        (new CartApi($di))->clearCart();
    }
}
