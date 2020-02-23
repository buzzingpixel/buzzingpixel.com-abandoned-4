<?php

declare(strict_types=1);

namespace App\Http\Cart;

use App\Cart\CartApi;
use App\Software\SoftwareApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class GetCartUpdateQuantityAction
{
    private SoftwareApi $softwareApi;
    private CartApi $cartApi;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        SoftwareApi $softwareApi,
        CartApi $cartApi,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->softwareApi     = $softwareApi;
        $this->cartApi         = $cartApi;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $software = $this->softwareApi->fetchSoftwareBySlug(
            (string) $request->getAttribute('slug')
        );

        if ($software === null) {
            throw new HttpNotFoundException($request);
        }

        $this->cartApi->updateCartItemQuantity(
            (int) $request->getAttribute('quantity'),
            $software
        );

        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', '/cart');
    }
}
