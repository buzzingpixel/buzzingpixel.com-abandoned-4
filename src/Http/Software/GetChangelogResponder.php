<?php

declare(strict_types=1);

namespace App\Http\Software;

use App\Content\Changelog\ChangelogPayload;
use App\Content\Meta\MetaPayload;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\StandardResponderConstructor;
use App\HttpHelpers\Pagination\Pagination;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetChangelogResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        MetaPayload $metaPayload,
        ChangelogPayload $allChangelogPayload,
        ChangelogPayload $changelogPayload,
        Pagination $pagination,
        SoftwareInfoPayload $softwareInfoPayload,
        string $uriPath
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse()
            ->withHeader('EnableStaticCache', 'true');

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Software/ChangelogPage.twig',
            [
                'metaPayload' => $metaPayload,
                'allChangelogPayload' => $allChangelogPayload,
                'changelogPayload' => $changelogPayload,
                'pagination' => $pagination,
                'softwareInfoPayload' => $softwareInfoPayload,
                'uriPath' => $uriPath,
                'activeNavHref' => $uriPath . '/changelog',
            ]
        ));

        return $response;
    }
}
