<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use cebe\markdown\GithubMarkdown;
use Config\General;
use DirectoryIterator;
use Symfony\Component\Yaml\Yaml;
use Throwable;

use function array_map;
use function array_values;
use function assert;
use function end;
use function explode;
use function implode;
use function Safe\ksort;

use const SORT_NATURAL;

class CollectDocumentationVersionPayloadFromPath
{
    private General $generalConfig;
    private CollectDocumentationPagePayloadFromPath $collectDocumentationPagePayloadFromPath;
    protected GithubMarkdown $markdownParser;

    public function __construct(
        General $generalConfig,
        CollectDocumentationPagePayloadFromPath $collectDocumentationPagePayloadFromPath,
        GithubMarkdown $markdownParser
    ) {
        $this->generalConfig                           = $generalConfig;
        $this->collectDocumentationPagePayloadFromPath = $collectDocumentationPagePayloadFromPath;
        $this->markdownParser                          = $markdownParser;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string $contentPath): DocumentationVersionPayload
    {
        $pathArray = [
            $this->generalConfig->pathToContentDirectory(),
            $contentPath,
        ];

        $path = implode('/', $pathArray);

        $contentPathArray = explode('/', $contentPath);

        $version = end($contentPathArray);

        $infoPathArray = [
            $path,
            'info.yml',
        ];

        $infoPath = implode('/', $infoPathArray);

        $directory = new DirectoryIterator($path);

        $directories = [];

        foreach ($directory as $fileInfo) {
            /** @psalm-suppress RedundantCondition */
            assert($fileInfo instanceof DirectoryIterator);

            if ($fileInfo->isDot() || ! $fileInfo->isDir()) {
                continue;
            }

            $directories[$fileInfo->getBasename()] = $fileInfo->getBasename();
        }

        ksort($directories, SORT_NATURAL);

        /** @var mixed[] $infoYaml */
        $infoYaml = Yaml::parseFile($infoPath);

        return new DocumentationVersionPayload([
            'title' => $this->markdownParser->parseParagraph(
                (string) ($infoYaml['title'] ?? '')
            ),
            'slug' => 'documentation' . ($version !== 'primary' ? '-' . $version : ''),
            'version' => $version,
            'pages' => array_values(array_map(
                function (string $pagePath) use (
                    $contentPath
                ): DocumentationPagePayload {
                    return ($this->collectDocumentationPagePayloadFromPath)(
                        $contentPath . '/' . $pagePath
                    );
                },
                $directories
            )),
        ]);
    }
}
