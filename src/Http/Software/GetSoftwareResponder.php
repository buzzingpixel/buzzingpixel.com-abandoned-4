<?php

declare(strict_types=1);

namespace App\Http\Software;

use App\Content\Meta\MetaPayload;
use App\Content\Modules\ModulePayload;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\StandardResponderConstructor;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetSoftwareResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        MetaPayload $metaPayload,
        ModulePayload $modulePayload,
        SoftwareInfoPayload $softwareInfoPayload,
        string $uriPath
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse()
            ->withHeader('EnableStaticCache', 'true');

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Software/SoftwarePage.twig',
            [
                'metaPayload' => $metaPayload,
                'modulePayload' => $modulePayload,
                'softwareInfoPayload' => $softwareInfoPayload,
                'uriPath' => $uriPath,
                'activeNavHref' => $uriPath,
            ]
        ));

        return $response;
    }
}
