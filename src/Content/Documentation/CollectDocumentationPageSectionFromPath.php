<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Content\Modules\CommonTraits\MapYamlImageToPayload;
use cebe\markdown\GithubMarkdown;
use Config\General;
use Symfony\Component\Yaml\Yaml;
use Throwable;
use function array_map;
use function array_values;
use function implode;
use function is_array;

class CollectDocumentationPageSectionFromPath
{
    use MapYamlImageToPayload;

    /** @var General */
    private $generalConfig;
    /** @var GithubMarkdown */
    protected $markdownParser;

    public function __construct(
        General $generalConfig,
        GithubMarkdown $markdownParser
    ) {
        $this->generalConfig  = $generalConfig;
        $this->markdownParser = $markdownParser;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string $contentPath) : DocumentationPageSectionPayload
    {
        $pathArray = [
            $this->generalConfig->pathToContentDirectory(),
            $contentPath,
        ];

        $path = implode('/', $pathArray);

        /** @var array $parsedYaml */
        $parsedYaml = Yaml::parseFile($path);

        return new DocumentationPageSectionPayload([
            'title' => $this->markdownParser->parseParagraph(
                (string) ($parsedYaml['title'] ?? '')
            ),
            'content' => array_values(array_map(
                /**
                 * @param array<string, mixed> $item
                 */
                function (array $item) {
                    $type = (string) $item['type'];

                    switch ($type) {
                        case 'heading':
                            return $this->mapHeadingTypeToPayload($item);
                        case 'list':
                            return $this->mapListTypeToPayload($item);
                        case 'content':
                            return $this->mapContentTypeToPayload($item);
                        case 'codeblock':
                            return $this->mapCodeblockTypeToPayload($item);
                        case 'image':
                            return $this->mapYamlImageToPayload($item);
                        case 'note':
                            return $this->mapNoteTypeToPayload($item);
                    }
                },
                is_array($parsedYaml['content']) ? $parsedYaml['content'] : []
            )),
        ]);
    }

    /**
     * @param mixed[] $item
     *
     * @throws Throwable
     */
    private function mapHeadingTypeToPayload(array $item) : HeadingPayload
    {
        return new HeadingPayload([
            'level' => (int) ($item['level'] ?? 3),
            'content' => $this->markdownParser->parseParagraph(
                (string) ($item['content'] ?? '')
            ),
        ]);
    }

    /**
     * @param mixed[] $item
     *
     * @throws Throwable
     */
    private function mapListTypeToPayload(array $item) : ListPayload
    {
        return new ListPayload([
            'listItems' => $item['content'] ?? [],
        ]);
    }

    /**
     * @param mixed[] $item
     *
     * @throws Throwable
     */
    private function mapContentTypeToPayload(array $item) : ContentPayload
    {
        return new ContentPayload([
            'content' => $this->markdownParser->parse(
                (string) ($item['content'] ?? '')
            ),
        ]);
    }

    /**
     * @param mixed[] $item
     *
     * @throws Throwable
     */
    private function mapCodeblockTypeToPayload(array $item) : CodeblockPayload
    {
        return new CodeblockPayload([
            'lang' => (string) ($item['lang'] ?? ''),
            'heading' => (string) ($item['heading'] ?? 'Example'),
            'content' => (string) ($item['content'] ?? ''),
        ]);
    }

    /**
     * @param mixed[] $item
     *
     * @throws Throwable
     */
    private function mapNoteTypeToPayload(array $item) : NotePayload
    {
        return new NotePayload([
            'heading' => (string) ($item['heading'] ?? 'Note'),
            'content' => $this->markdownParser->parse(
                (string) ($item['content'] ?? '')
            ),
        ]);
    }
}
