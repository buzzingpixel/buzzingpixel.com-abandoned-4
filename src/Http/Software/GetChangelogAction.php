<?php

declare(strict_types=1);

namespace App\Http\Software;

use App\Content\Changelog\ParseChangelogFromMarkdownFile;
use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Software\ExtractSoftwareInfoFromPath;
use App\HttpHelpers\Pagination\Pagination;
use App\HttpHelpers\Segments\ExtractUriSegments;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;
use function count;

class GetChangelogAction
{
    /** @var GetChangelogResponder */
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
        GetChangelogResponder $responder,
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
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $limit = 10;

        $uriSegments = ($this->extractUriSegments)($request->getUri());

        $pageZeroIndex = $uriSegments->getPageNum() - 1;

        $offset = $pageZeroIndex * $limit;

        $contentPath = PathMap::PATH_MAP['/' . $uriSegments->getPathFromSegmentSlice(2)];

        $softwareInfoPayload = ($this->extractSoftwareInfoFromPath)(
            $contentPath
        );

        if ($softwareInfoPayload->getChangelogExternalUrl() !== '') {
            $allChangelogPayload = ($this->parseChangelog)(
                $softwareInfoPayload->getChangelogExternalUrl()
            );
        } else {
            $allChangelogPayload = ($this->parseChangelog)(
                $contentPath . '/changelog.md'
            );
        }

        $pagination = (new Pagination())->withBase($uriSegments->getPathSansPagination())
            ->withCurrentPage($uriSegments->getPageNum())
            ->withPerPage($limit)
            ->withTotalResults(count($allChangelogPayload->getReleases()));

        $changelogPayload = $allChangelogPayload->withReleaseSlice($limit, $offset);

        if (count($changelogPayload->getReleases()) < 1) {
            throw new HttpNotFoundException($request);
        }

        $metaPayload = ($this->extractMetaFromPath)($contentPath);

        $metaTitle = 'Changelog | ' . $metaPayload->getMetaTitle();

        if ($uriSegments->getPageNum() > 1) {
            $metaTitle = 'Page ' . $uriSegments->getPageNum() . ' | ' . $metaTitle;
        }

        $newMetaPayload = $metaPayload->withMetaTitle($metaTitle)
            ->withMetaDescription('');

        return ($this->responder)(
            $newMetaPayload,
            $allChangelogPayload,
            $changelogPayload,
            $pagination,
            $softwareInfoPayload,
            '/' . $uriSegments->getPathFromSegmentSlice(2)
        );
    }
}
