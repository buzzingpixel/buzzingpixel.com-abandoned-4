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
    /** @var SoftwareApi */
    private $softwareApi;
    /** @var CartApi */
    private $cartApi;
    /** @var ResponseFactoryInterface */
    private $responseFactory;

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
        $slug = (string) $request->getAttribute('slug');

        $software = $this->softwareApi->fetchSoftwareBySlug($slug);

        $quantity = (int) $request->getAttribute('quantity');

        if ($software === null) {
            throw new HttpNotFoundException($request);
        }

        $this->cartApi->updateCartItemQuantity($quantity, $software);

        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', '/cart');
    }
}
