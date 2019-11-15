<?php

declare(strict_types=1);

namespace App\Http\Software;

use App\Content\Meta\MetaPayload;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\StandardResponderConstructor;
use MJErwin\ParseAChangelog\Release;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetChangelogItemResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        MetaPayload $metaPayload,
        Release $release,
        SoftwareInfoPayload $softwareInfoPayload,
        string $uriPath
    ) : ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write(($this->minifier)(
            $this->twigEnvironment->render('SoftwareChangelogItemPage.twig', [
                'metaPayload' => $metaPayload,
                'release' => $release,
                'softwareInfoPayload' => $softwareInfoPayload,
                'uriPath' => $uriPath,
                'activeHref' => $uriPath . '/changelog',
            ])
        ));

        return $response;
    }
}
