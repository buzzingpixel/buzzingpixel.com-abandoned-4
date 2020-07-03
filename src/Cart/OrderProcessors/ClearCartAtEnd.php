<?php

declare(strict_types=1);

namespace App\Cart\OrderProcessors;

use App\Cart\CartApi;
use App\Cart\Models\ProcessOrderModel;

class ClearCartAtEnd
{
    private CartApi $cartApi;

    public function __construct(CartApi $cartApi)
    {
        $this->cartApi = $cartApi;
    }

    public function __invoke(
        ProcessOrderModel $processOrderModel
    ): ProcessOrderModel {
        $this->cartApi->clearCart();

        return $processOrderModel;
    }
}
