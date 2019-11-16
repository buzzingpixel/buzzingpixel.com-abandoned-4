<?php

declare(strict_types=1);

namespace App\Http\Software;

use App\Content\Changelog\ParseChangelogFromMarkdownFile;
use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Software\ExtractSoftwareInfoFromPath;
use App\HttpHelpers\Segments\ExtractUriSegments;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class GetChangelogItemAction
{
    /** @var GetChangelogItemResponder */
    private $responder;
    /** @var ExtractUriSegments */
    private $extractUriSegments;
    /** @var ExtractSoftwareInfoFromPath */
    private $extractSoftwareInfoFromPath;
    /** @var ParseChangelogFromMarkdownFile */
    private $parseChangelog;
    /** @var ExtractMetaFromPath */
    private $extractMetaFromPath;

    public function __construct(
        GetChangelogItemResponder $responder,
        ExtractUriSegments $extractUriSegments,
        ExtractSoftwareInfoFromPath $extractSoftwareInfoFromPath,
        ParseChangelogFromMarkdownFile $parseChangelog,
        ExtractMetaFromPath $extractMetaFromPath
    ) {
        $this->responder                   = $responder;
        $this->extractUriSegments          = $extractUriSegments;
        $this->extractSoftwareInfoFromPath = $extractSoftwareInfoFromPath;
        $this->parseChangelog              = $parseChangelog;
        $this->extractMetaFromPath         = $extractMetaFromPath;
    }

    /**
     * @throws Throwable
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $uriSegments = ($this->extractUriSegments)($request->getUri());

        $contentPath = PathMap::PATH_MAP['/' . $uriSegments->getPathFromSegmentSlice(2)];

        $softwareInfoPayload = ($this->extractSoftwareInfoFromPath)(
            $contentPath
        );

        if ($softwareInfoPayload->getChangelogExternalUrl() !== '') {
            $changelogPayload = ($this->parseChangelog)(
                $softwareInfoPayload->getChangelogExternalUrl()
            );
        } else {
            $changelogPayload = ($this->parseChangelog)(
                $contentPath . '/changelog.md'
            );
        }

        $release = null;

        foreach ($changelogPayload->getReleases() as $loopRelease) {
            if ($loopRelease->getVersion() !== $uriSegments->getLastSegment()) {
                continue;
            }

            $release = $loopRelease;
        }

        if ($release === null) {
            throw new HttpNotFoundException($request);
        }

        return ($this->responder)(
            ($this->extractMetaFromPath)($contentPath),
            $release,
            $softwareInfoPayload,
            '/' . $uriSegments->getPathFromSegmentSlice(2)
        );
    }
}
