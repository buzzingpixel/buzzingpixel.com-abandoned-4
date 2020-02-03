<?php

declare(strict_types=1);

namespace App\Http\Ajax\User;

use App\Cart\CartApi;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function Safe\json_encode;

class GetUserPayloadAction
{
    /** @var CartApi */
    private $cartApi;
    /** @var ResponseFactoryInterface */
    private $responseFactory;

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
    public function __invoke() : ResponseInterface
    {
        $response = $this->responseFactory->createResponse()
            ->withHeader(
                'Content-type',
                'application/json'
            );

        $response->getBody()->write(json_encode(
            [
                'cart' => $this->cartApi->fetchCurrentUserCart()->asArray(),
            ]
        ));

        return $response;
    }
}
