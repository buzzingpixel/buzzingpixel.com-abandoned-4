<?php

declare(strict_types=1);

namespace App\Http\Admin;

use App\Http\StandardResponderConstructor;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAdminResponder extends StandardResponderConstructor
{
    /**
     * @param array $context
     *
     * @throws Throwable
     */
    public function __invoke(
        string $template,
        array $context = []
    ) : ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twigEnvironment->render(
            $template,
            $context,
        ));

        return $response;
    }
}
