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
    private GetChangelogItemResponder $responder;
    private ExtractUriSegments $extractUriSegments;
    private ExtractSoftwareInfoFromPath $extractSoftwareInfoFromPath;
    private ParseChangelogFromMarkdownFile $parseChangelog;
    private ExtractMetaFromPath $extractMetaFromPath;

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
    public function __invoke(ServerRequestInterface $request): ResponseInterface
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

        $metaPayload = ($this->extractMetaFromPath)($contentPath);

        $newMetaPayload = $metaPayload
            ->withMetaTitle(
                'Version ' . $release->getVersion() . ' | Changelog | ' . $metaPayload->getMetaTitle()
            )
            ->withMetaDescription('');

        return ($this->responder)(
            $newMetaPayload,
            $release,
            $softwareInfoPayload,
            '/' . $uriSegments->getPathFromSegmentSlice(2)
        );
    }
}
