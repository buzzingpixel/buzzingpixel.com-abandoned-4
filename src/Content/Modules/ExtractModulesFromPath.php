<?php

declare(strict_types=1);

namespace App\Content\Modules;

use DirectoryIterator;
use IteratorIterator;
use RegexIterator;
use Symfony\Component\Yaml\Yaml;
use Throwable;
use function array_map;
use function array_values;
use function iterator_to_array;
use function ucfirst;

class ExtractModulesFromPath
{
    /** @var string */
    private $pathToContentDirectory;

    public function __construct(string $pathToContentDirectory)
    {
        $this->pathToContentDirectory = $pathToContentDirectory;
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

        return new ModulePayload([
            'items' => array_values(array_map(
                /**
                 * @param string[] $item
                 *
                 * @return mixed
                 */
                function (array $item) use ($modulesPath) {
                    $fullPath = $modulesPath . '/' . (string) $item[0];

                    /** @var array $parsedYaml */
                    $parsedYaml = Yaml::parseFile($fullPath);

                    $type = (string) $parsedYaml['type'];

                    $method = 'extract' . ucfirst($type);

                    return $this->{$method}($parsedYaml);
                },
                iterator_to_array($finalIterator)
            )),
        ]);
    }
}
