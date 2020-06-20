<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Content\Software\ExtractSoftwareInfoFromPath;
use Config\General;
use DirectoryIterator;
use Throwable;
use function array_map;
use function array_merge;
use function array_reverse;
use function assert;
use function implode;
use function Safe\ksort;
use const SORT_NATURAL;

class CollectDocumentationVersionsFromPath
{
    private General $generalConfig;
    private ExtractSoftwareInfoFromPath $extractSoftwareInfoFromPath;
    private CollectDocumentationVersionPayloadFromPath $collectDocumentationVersionPayloadFromPath;

    public function __construct(
        General $generalConfig,
        ExtractSoftwareInfoFromPath $extractSoftwareInfoFromPath,
        CollectDocumentationVersionPayloadFromPath $collectDocumentationVersionPayloadFromPath
    ) {
        $this->generalConfig                              = $generalConfig;
        $this->extractSoftwareInfoFromPath                = $extractSoftwareInfoFromPath;
        $this->collectDocumentationVersionPayloadFromPath = $collectDocumentationVersionPayloadFromPath;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string $contentPath) : DocumentationVersionsPayload
    {
        $fullDirectoryArray = [
            $this->generalConfig->pathToContentDirectory(),
            $contentPath,
            'documentation',
        ];

        $hasPrimary = false;

        $versions = [];

        $fullDirectoryPath = implode('/', $fullDirectoryArray);

        $directory = new DirectoryIterator($fullDirectoryPath);

        foreach ($directory as $fileInfo) {
            assert($fileInfo instanceof DirectoryIterator);

            if ($fileInfo->isDot() || ! $fileInfo->isDir()) {
                continue;
            }

            if ($fileInfo->getBasename() === 'primary') {
                $hasPrimary = true;

                continue;
            }

            $versions[$fileInfo->getBasename()] = $fileInfo->getBasename();
        }

        ksort($versions, SORT_NATURAL);

        $versions = array_reverse($versions);

        if ($hasPrimary) {
            $versions = array_merge(
                ['primary' => 'primary'],
                $versions
            );
        }

        return new DocumentationVersionsPayload([
            'softwareInfo' => ($this->extractSoftwareInfoFromPath)($contentPath),
            'versions' => array_map(
                function (string $version) use (
                    $contentPath
                ) : DocumentationVersionPayload {
                    return ($this->collectDocumentationVersionPayloadFromPath)(
                        $contentPath . '/documentation/' . $version
                    );
                },
                $versions
            ),
        ]);
    }
}
