<?php

declare(strict_types=1);

namespace App\Http\Software;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class GetChangelogRawResponder
{
    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(string $content) : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response = $response->withHeader(
            'Content-Type',
            'text/plain'
        );

        $response->getBody()->write($content);

        return $response;
    }
}
