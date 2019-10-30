<?php

declare(strict_types=1);

namespace App\Content\Meta;

use Symfony\Component\Yaml\Yaml;
use Throwable;

class ExtractMetaFromPath
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
    public function __invoke(string $contentPath) : MetaPayload
    {
        $fullPath = $this->pathToContentDirectory . '/' . $contentPath . '/meta.yml';

        /** @var array|null $parsedYaml */
        $parsedYaml = Yaml::parseFile($fullPath);

        return new MetaPayload([
            'metaTitle' => $parsedYaml['metaTitle'] ?? '',
        ]);
    }
}
