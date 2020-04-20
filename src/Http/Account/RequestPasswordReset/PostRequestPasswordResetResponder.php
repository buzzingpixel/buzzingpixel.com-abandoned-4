<?php

declare(strict_types=1);

namespace App\Http\Account\RequestPasswordReset;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class PostRequestPasswordResetResponder
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function __invoke() : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', '/account/request-password-reset/msg');
    }
}
