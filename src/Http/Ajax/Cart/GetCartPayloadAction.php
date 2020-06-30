<?php

declare(strict_types=1);

namespace App\Http\Ajax\Cart;

use App\Cart\CartApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function number_format;
use function Safe\json_encode;

class GetCartPayloadAction
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

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        $currentUserCart = $this->cartApi->fetchCurrentUserCart();

        $response = $this->responseFactory->createResponse()
            ->withHeader(
                'Content-type',
                'application/json'
            );

        $response->getBody()->write(json_encode(
            [
                'totalQuantity' => $currentUserCart->totalQuantity,
                'subTotal' => '$' . number_format(
                    $currentUserCart->calculateSubTotal(),
                    2
                ),
                'tax' => '$' . number_format(
                    $currentUserCart->calculateTax(),
                    2
                ),
                'total' => '$' . number_format(
                    $currentUserCart->calculateTotal(),
                    2
                ),
            ]
        ));

        return $response;
    }
}
