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

use function assert;

class CartApi
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function fetchCurrentUserCart(): CartModel
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(FetchCurrentUserCart::class);

        assert($service instanceof FetchCurrentUserCart);

        return $service();
    }

    public function saveCart(CartModel $cart): Payload
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(SaveCart::class);

        assert($service instanceof SaveCart);

        return $service($cart);
    }

    public function addItemToCurrentUsersCart(SoftwareModel $software): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(AddItemToCurrentUsersCart::class);

        assert($service instanceof AddItemToCurrentUsersCart);

        $service($software);
    }

    public function updateCartItemQuantity(
        int $quantity,
        SoftwareModel $software
    ): void {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(UpdateCartItemQuantity::class);

        assert($service instanceof UpdateCartItemQuantity);

        $service($quantity, $software);
    }

    public function clearCart(): void
    {
        /** @psalm-suppress MixedAssignment */
        $service = $this->di->get(ClearCart::class);

        assert($service instanceof ClearCart);

        $service();
    }
}
