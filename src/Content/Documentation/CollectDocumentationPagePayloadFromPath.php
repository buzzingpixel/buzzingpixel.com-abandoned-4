<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use Config\General;
use DirectoryIterator;
use IteratorIterator;
use RegexIterator;
use Symfony\Component\Yaml\Yaml;
use Throwable;
use const SORT_NATURAL;
use function array_map;
use function array_values;
use function implode;
use function iterator_to_array;
use function Safe\ksort;

class CollectDocumentationPagePayloadFromPath
{
    /** @var General */
    private $generalConfig;
    /** @var CollectDocumentationPageSectionFromPath */
    private $collectDocumentationPageSectionFromPath;

    public function __construct(
        General $generalConfig,
        CollectDocumentationPageSectionFromPath $collectDocumentationPageSectionFromPath
    ) {
        $this->generalConfig                           = $generalConfig;
        $this->collectDocumentationPageSectionFromPath = $collectDocumentationPageSectionFromPath;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string $contentPath) : DocumentationPagePayload
    {
        $pathArray = [
            $this->generalConfig->pathToContentDirectory(),
            $contentPath,
        ];

        $path = implode('/', $pathArray);

        $directory = new DirectoryIterator($path);

        $iterator = new IteratorIterator($directory);

        $finalIterator = new RegexIterator(
            $iterator,
            '/^.+\.yml/i',
            RegexIterator::GET_MATCH
        );

        /** @var array<int, array<int, string>> $items */
        $items = iterator_to_array($finalIterator);

        $fileNames = [];

        foreach ($items as $item) {
            $fileName = $item[0];

            if ($fileName === 'info.yml') {
                continue;
            }

            $fileNames[$fileName] = $fileName;
        }

        ksort($fileNames, SORT_NATURAL);

        /** @var mixed[] $infoYaml */
        $infoYaml = Yaml::parseFile($path . '/info.yml');

        return new DocumentationPagePayload([
            'title' => (string) ($infoYaml['title'] ?? ''),
            'slug' => (string) ($infoYaml['slug'] ?? ''),
            'sections' => array_values(array_map(
                function (string $fileName) use ($contentPath) {
                    return ($this->collectDocumentationPageSectionFromPath)(
                        $contentPath . '/' . $fileName
                    );
                },
                $fileNames
            )),
        ]);
    }
}
