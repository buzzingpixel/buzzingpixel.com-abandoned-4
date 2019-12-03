<?php

declare(strict_types=1);

namespace App\Http\Admin;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class GetAdminAction
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function __invoke() : ResponseInterface
    {
        return $this->responseFactory->createResponse(303)
            ->withHeader('Location', '/admin/software');
    }
}
