<?php

declare(strict_types=1);

namespace App\Http\Software;

use App\Content\Software\ExtractSoftwareInfoFromPath;
use App\HttpHelpers\Segments\ExtractUriSegments;
use Config\General;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use function Safe\file_get_contents;

class GetChangelogRawAction
{
    private GetChangelogRawResponder $responder;
    private General $generalConfig;
    private ExtractUriSegments $extractUriSegments;
    private ExtractSoftwareInfoFromPath $extractSoftwareInfoFromPath;

    public function __construct(
        GetChangelogRawResponder $responder,
        General $generalConfig,
        ExtractUriSegments $extractUriSegments,
        ExtractSoftwareInfoFromPath $extractSoftwareInfoFromPath
    ) {
        $this->responder                   = $responder;
        $this->generalConfig               = $generalConfig;
        $this->extractUriSegments          = $extractUriSegments;
        $this->extractSoftwareInfoFromPath = $extractSoftwareInfoFromPath;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $uriSegments = ($this->extractUriSegments)($request->getUri());

        $contentPath = PathMap::PATH_MAP['/' . $uriSegments->getPathFromSegmentSlice(2)];

        $softwareInfoPayload = ($this->extractSoftwareInfoFromPath)(
            $contentPath
        );

        if ($softwareInfoPayload->getChangelogExternalUrl() !== '') {
            return ($this->responder)(
                file_get_contents(
                    $softwareInfoPayload->getChangelogExternalUrl()
                )
            );
        }

        return ($this->responder)(file_get_contents(
            $this->generalConfig->pathToContentDirectory() .
            '/' .
            $contentPath . '/changelog.md'
        ));
    }
}
