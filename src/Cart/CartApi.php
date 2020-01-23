<?php

declare(strict_types=1);

namespace App\Cart;

use App\Cart\Models\CartModel;
use App\Cart\Services\AddItemToCurrentUsersCart;
use App\Cart\Services\FetchCurrentUserCart;
use App\Cart\Services\SaveCart;
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
        $service = $this->di->get(AddItemToCurrentUsersCart::class);

        $service($software);
    }
}
