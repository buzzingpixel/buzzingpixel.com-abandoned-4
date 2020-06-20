<?php

declare(strict_types=1);

namespace App\Http\Cart;

use App\Cart\CartApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class GetClearCartAction
{
    private CartApi $cartApi;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        CartApi $cartApi,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->cartApi         = $cartApi;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(): ResponseInterface
    {
        $this->cartApi->clearCart();

        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', '/cart');
    }
}
