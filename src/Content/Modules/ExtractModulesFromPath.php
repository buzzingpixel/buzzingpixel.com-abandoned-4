<?php

declare(strict_types=1);

namespace App\Content\Modules;

use App\Content\Modules\CommonTraits\MapYamlCtaToPayload;
use App\Content\Modules\CommonTraits\MapYamlImageToPayload;
use App\Content\Modules\ExtractorMethods\ExtractCtaCards;
use App\Content\Modules\ExtractorMethods\ExtractCtas;
use App\Content\Modules\ExtractorMethods\ExtractImage;
use App\Content\Modules\ExtractorMethods\ExtractImageCallOut;
use App\Content\Modules\ExtractorMethods\ExtractInformationalImage;
use App\Content\Modules\ExtractorMethods\ExtractPrimaryImageTextHalfBlack;
use App\Content\Modules\ExtractorMethods\ExtractQuoteBlocks;
use App\Content\Modules\ExtractorMethods\ExtractShowCase;
use App\Content\Modules\ExtractorMethods\ExtractTextColumns;
use cebe\markdown\GithubMarkdown;
use DirectoryIterator;
use IteratorIterator;
use RegexIterator;
use Symfony\Component\Yaml\Yaml;
use Throwable;
use function array_map;
use function array_values;
use function assert;
use function is_array;
use function iterator_to_array;
use function Safe\ksort;
use function ucfirst;
use const SORT_NATURAL;

class ExtractModulesFromPath
{
    use MapYamlCtaToPayload;
    use MapYamlImageToPayload;
    use ExtractCtaCards;
    use ExtractCtas;
    use ExtractImage;
    use ExtractImageCallOut;
    use ExtractInformationalImage;
    use ExtractPrimaryImageTextHalfBlack;
    use ExtractQuoteBlocks;
    use ExtractShowCase;
    use ExtractTextColumns;

    private string $pathToContentDirectory;
    protected GithubMarkdown $markdownParser;

    public function __construct(
        string $pathToContentDirectory,
        GithubMarkdown $markdownParser
    ) {
        $this->pathToContentDirectory = $pathToContentDirectory;
        $this->markdownParser         = $markdownParser;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string $contentPath) : ModulePayload
    {
        $modulesPath = $this->pathToContentDirectory . '/' . $contentPath . '/modules';

        $directory = new DirectoryIterator($modulesPath);

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
            $fileNames[$item[0]] = $item[0];
        }

        ksort($fileNames, SORT_NATURAL);

        return new ModulePayload([
            'items' => array_values(array_map(
                /**
                 * @return mixed
                 */
                function (string $item) use ($modulesPath) {
                    $fullPath = $modulesPath . '/' . $item;

                    /** @psalm-suppress MixedAssignment */
                    $parsedYaml = Yaml::parseFile($fullPath);
                    assert(is_array($parsedYaml));

                    $type = (string) $parsedYaml['type'];

                    $method = 'extract' . ucfirst($type);

                    return $this->{$method}($parsedYaml);
                },
                $fileNames
            )),
        ]);
    }
}
