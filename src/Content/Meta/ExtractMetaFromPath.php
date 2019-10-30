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
            'noIndex' => isset($parsedYaml['noIndex']) ? ((bool) $parsedYaml['noIndex']) : false,
            'metaTitle' => isset($parsedYaml['metaTitle']) ? ((string) $parsedYaml['metaTitle']) : '',
            'metaDescription' => isset($parsedYaml['metaDescription']) ? ((string) $parsedYaml['metaDescription']) : '',
            'ogType' => isset($parsedYaml['ogType']) ? ((string) $parsedYaml['ogType']) : 'website',
            'twitterCardType' => isset($parsedYaml['twitterCardType']) ?
                ((string) $parsedYaml['twitterCardType']) :
                'summary',
        ]);
    }
}
