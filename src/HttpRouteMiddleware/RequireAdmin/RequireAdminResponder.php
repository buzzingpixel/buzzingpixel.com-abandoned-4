<?php

declare(strict_types=1);

namespace App\HttpRouteMiddleware\RequireAdmin;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RequireAdminResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(MetaPayload $metaPayload): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Unauthorized.twig',
            ['metaPayload' => $metaPayload]
        ));

        return $response;
    }
}
