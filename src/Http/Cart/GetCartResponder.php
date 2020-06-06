<?php

declare(strict_types=1);

namespace App\Http\Cart;

use App\Cart\Models\CartModel;
use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetCartResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(CartModel $cart) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Cart.twig',
            [
                'metaPayload' => new MetaPayload(['metaTitle' => 'Your Cart']),
                'cart' => $cart,
            ]
        ));

        return $response;
    }
}
