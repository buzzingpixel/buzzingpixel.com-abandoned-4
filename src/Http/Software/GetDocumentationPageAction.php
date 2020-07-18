<?php

declare(strict_types=1);

namespace App\Http\Software;

use App\Content\Documentation\CollectDocumentationVersionsFromPath;
use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Software\ExtractSoftwareInfoFromPath;
use App\HttpHelpers\Segments\ExtractUriSegments;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class GetDocumentationPageAction
{
    private GetDocumentationPageResponder $responder;
    private ExtractUriSegments $extractUriSegments;
    private CollectDocumentationVersionsFromPath $collectDocumentationVersionsFromPath;
    private ExtractMetaFromPath $extractMetaFromPath;
    private ExtractSoftwareInfoFromPath $extractSoftwareInfoFromPath;

    public function __construct(
        GetDocumentationPageResponder $responder,
        ExtractUriSegments $extractUriSegments,
        CollectDocumentationVersionsFromPath $collectDocumentationVersionsFromPath,
        ExtractMetaFromPath $extractMetaFromPath,
        ExtractSoftwareInfoFromPath $extractSoftwareInfoFromPath
    ) {
        $this->responder                            = $responder;
        $this->extractUriSegments                   = $extractUriSegments;
        $this->collectDocumentationVersionsFromPath = $collectDocumentationVersionsFromPath;
        $this->extractMetaFromPath                  = $extractMetaFromPath;
        $this->extractSoftwareInfoFromPath          = $extractSoftwareInfoFromPath;
    }

    /**
     * @throws Throwable
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $versionString = (string) $request->getAttribute('versionString');

        $pageSlug = (string) $request->getAttribute('pageSlug');

        $uriSegments = ($this->extractUriSegments)($request->getUri());

        $contentPath = PathMap::PATH_MAP['/' . $uriSegments->getPathFromSegmentSlice(2)];

        $softwareInfoPayload = ($this->extractSoftwareInfoFromPath)(
            $contentPath
        );

        $versions = ($this->collectDocumentationVersionsFromPath)($contentPath);

        $activeVersion = $versions->getVersionBySlug($versionString);

        if ($activeVersion === null) {
            throw new HttpNotFoundException($request);
        }

        $activePage = $activeVersion->getPageBySlug($pageSlug);

        if ($activePage === null) {
            throw new HttpNotFoundException($request);
        }

        $metaPayload = ($this->extractMetaFromPath)($contentPath);

        $newMetaPayload = $metaPayload
            ->withMetaTitle(
                $activePage->getTitle() . ' | Documentation | ' . $metaPayload->getMetaTitle()
            )
            ->withMetaDescription('');

        return ($this->responder)(
            '/' . $uriSegments->getPathFromSegmentSlice(2),
            $newMetaPayload,
            $activePage,
            $activeVersion,
            $versions,
            $softwareInfoPayload
        );
    }
}
