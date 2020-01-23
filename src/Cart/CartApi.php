<?php

declare(strict_types=1);

namespace App\Cart;

use App\Cart\Models\CartModel;
use App\Cart\Services\AddItemToCurrentUsersCart;
use App\Cart\Services\ClearCart;
use App\Cart\Services\FetchCurrentUserCart;
use App\Cart\Services\SaveCart;
use App\Cart\Services\UpdateCartItemQuantity;
use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use Psr\Container\ContainerInterface;

class CartApi
{
    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function fetchCurrentUserCart() : CartModel
    {
        /** @var FetchCurrentUserCart $service */
        $service = $this->di->get(FetchCurrentUserCart::class);

        return $service();
    }

    public function saveCart(CartModel $cart) : Payload
    {
        /** @var SaveCart $service */
        $service = $this->di->get(SaveCart::class);

        return $service($cart);
    }

    public function addItemToCurrentUsersCart(SoftwareModel $software) : void
    {
        /** @var AddItemToCurrentUsersCart $service */
        $service = $this->di->get(AddItemToCurrentUsersCart::class);

        $service($software);
    }

    public function updateCartItemQuantity(
        int $quantity,
        SoftwareModel $software
    ) : void {
        /** @var UpdateCartItemQuantity $service */
        $service = $this->di->get(UpdateCartItemQuantity::class);

        $service($quantity, $software);
    }

    public function clearCart() : void
    {
        /** @var ClearCart $service */
        $service = $this->di->get(ClearCart::class);

        $service();
    }
}
