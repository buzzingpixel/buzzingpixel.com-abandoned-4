<?php

declare(strict_types=1);

namespace App\Http\Error;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class Error500Responder
{
    private ResponseFactoryInterface $responseFactory;
    private LoggerInterface $logger;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        LoggerInterface $logger
    ) {
        $this->responseFactory = $responseFactory;
        $this->logger          = $logger;
    }

    public function __invoke(Throwable $exception) : ResponseInterface
    {
        $this->logger->error(
            'An exception was thrown',
            ['exception' => $exception]
        );

        // TODO: Implement 500 page
        $response = $this->responseFactory->createResponse(
            500,
            'An internal server error occurred'
        );

        $response->getBody()->write('An internal server error occurred');

        return $response;
    }
}
