<?php

declare(strict_types=1);

namespace App\Http\Software;

use App\Content\Documentation\DocumentationPagePayload;
use App\Content\Documentation\DocumentationVersionPayload;
use App\Content\Documentation\DocumentationVersionsPayload;
use App\Content\Meta\MetaPayload;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\StandardResponderConstructor;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetDocumentationPageResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        string $uriPath,
        MetaPayload $metaPayload,
        DocumentationPagePayload $activePage,
        DocumentationVersionPayload $activeVersion,
        DocumentationVersionsPayload $versions,
        SoftwareInfoPayload $softwareInfoPayload
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse()
            ->withHeader('EnableStaticCache', 'true');

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Software/DocumentationPage.twig',
            [
                'uriPath' => $uriPath,
                'activeNavHref' => $uriPath . '/documentation',
                'metaPayload' => $metaPayload,
                'activePage' => $activePage,
                'activeVersion' => $activeVersion,
                'versions' => $versions,
                'softwareInfoPayload' => $softwareInfoPayload,
            ]
        ));

        return $response;
    }
}
