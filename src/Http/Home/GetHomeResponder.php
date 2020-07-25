<?php

declare(strict_types=1);

namespace App\Http\Home;

use App\Content\Meta\MetaPayload;
use App\Content\Modules\ModulePayload;
use App\Http\StandardResponderConstructor;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetHomeResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        MetaPayload $metaPayload,
        ModulePayload $modulePayload
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse()
            ->withHeader('EnableStaticCache', 'true');

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/StandardPage.twig',
            [
                'metaPayload' => $metaPayload,
                'modulePayload' => $modulePayload,
            ]
        ));

        return $response;
    }
}
