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
use function assert;
use function implode;
use function is_array;

class CollectDocumentationPageSectionFromPath
{
    use MapYamlImageToPayload;

    private General $generalConfig;
    protected GithubMarkdown $markdownParser;

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

        /** @psalm-suppress MixedAssignment */
        $parsedYaml = Yaml::parseFile($path);
        assert(is_array($parsedYaml));

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
