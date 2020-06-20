<?php

declare(strict_types=1);

namespace App\Content\Meta;

use Symfony\Component\Yaml\Yaml;
use Throwable;
use function assert;
use function is_array;

class ExtractMetaFromPath
{
    private string $pathToContentDirectory;

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

        /** @psalm-suppress MixedAssignment */
        $parsedYaml = Yaml::parseFile($fullPath);

        assert(is_array($parsedYaml) || $parsedYaml === null);

        return new MetaPayload([
            'noIndex' => isset($parsedYaml['noIndex']) ? ((bool) $parsedYaml['noIndex']) : false,
            'metaTitle' => isset($parsedYaml['metaTitle']) ? ((string) $parsedYaml['metaTitle']) : '',
            'metaDescription' => isset($parsedYaml['metaDescription']) ? ((string) $parsedYaml['metaDescription']) : '',
            'ogType' => isset($parsedYaml['ogType']) ? ((string) $parsedYaml['ogType']) : 'website',
            'twitterCardType' => isset($parsedYaml['twitterCardType']) ?
                ((string) $parsedYaml['twitterCardType']) :
                'summary',
            'headingBackground' => new HeadingBackgroundPayload([
                /** @phpstan-ignore-next-line */
                'oneX' => $parsedYaml['headingBackground']['1x'] ?? '',
                /** @phpstan-ignore-next-line */
                'twoX' => $parsedYaml['headingBackground']['2x'] ?? '',
                /** @phpstan-ignore-next-line */
                'alt' => $parsedYaml['headingBackground']['alt'] ?? '',
            ]),
        ]);
    }
}
