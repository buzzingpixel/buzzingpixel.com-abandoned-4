<?php

declare(strict_types=1);

namespace App\Http\Cart;

use App\Cart\CartApi;
use App\Cart\Models\CartItemModel;
use App\Software\SoftwareApi;
use function dd;

class GetAddToCart
{
    /** @var CartApi */
    private $cartApi;
    /** @var SoftwareApi */
    private $softwareApi;

    public function __construct(CartApi $cartApi, SoftwareApi $softwareApi)
    {
        $this->cartApi     = $cartApi;
        $this->softwareApi = $softwareApi;
    }

    public function __invoke() : void
    {
        $cart = $this->cartApi->fetchCurrentUserCart();

        $anselCraft         = $this->softwareApi->fetchSoftwareBySlug('ansel-craft');
        $anselEe            = $this->softwareApi->fetchSoftwareBySlug('ansel-ee');
        $anselCraftCartItem = new CartItemModel([
            'software' => $anselCraft,
            'quantity' => 1,
        ]);
        $anselEeCartItem    = new CartItemModel([
            'software' => $anselEe,
            'quantity' => 2,
        ]);

        // For user cart
        // $cart->addItem($anselCraftCartItem);
        // $this->cartApi->saveCart($cart);

        // For Cookie Cart
        // $cart->addItem($anselCraftCartItem);
        // $cart->addItem($anselEeCartItem);
        // $this->cartApi->saveCart($cart);

        // Empty Cart
        // foreach ($cart->getItems() as $item) {
        //     $item->setQuantity(0);
        // }
        // $this->cartApi->saveCart($cart);

        dd($cart);
    }
}
