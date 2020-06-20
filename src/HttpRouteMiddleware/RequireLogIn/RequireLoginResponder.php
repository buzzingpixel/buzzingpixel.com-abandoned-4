<?php

declare(strict_types=1);

namespace App\HttpRouteMiddleware\RequireLogIn;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RequireLoginResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        MetaPayload $metaPayload,
        string $redirectTo
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/LogIn.twig',
            [
                'metaPayload' => $metaPayload,
                'redirectTo' => $redirectTo,
            ]
        ));

        return $response;
    }
}
