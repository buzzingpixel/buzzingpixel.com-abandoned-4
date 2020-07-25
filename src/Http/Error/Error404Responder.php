<?php

declare(strict_types=1);

namespace App\Http\Error;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class Error404Responder
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(): ResponseInterface
    {
        // TODO: Implement 404 page
        $response = $this->responseFactory->createResponse(
            404,
            'Page not found'
        )
            // We'll statically cache the response so 404s can't DDOS us
            ->withHeader('EnableStaticCache', 'true');

        $response->getBody()->write('Page not found');

        return $response;
    }
}
