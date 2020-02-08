<?php

declare(strict_types=1);

namespace App\Content\Software;

use App\Content\Modules\CommonTraits\MapYamlCtaToPayload;
use Symfony\Component\Yaml\Yaml;
use Throwable;
use function array_map;
use function assert;
use function is_array;

class ExtractSoftwareInfoFromPath
{
    use MapYamlCtaToPayload;

    private string $pathToContentDirectory;

    public function __construct(string $pathToContentDirectory)
    {
        $this->pathToContentDirectory = $pathToContentDirectory;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string $contentPath) : SoftwareInfoPayload
    {
        $fullPath = $this->pathToContentDirectory . '/' . $contentPath . '/software.yml';

        /** @psalm-suppress MixedAssignment */
        $vars = Yaml::parseFile($fullPath);
        assert(is_array($vars) || $vars === null);

        $ctas = isset($vars['actionButtons']) && is_array($vars['actionButtons']) ?
            $vars['actionButtons'] :
            [];

        return new SoftwareInfoPayload([
            'title' => isset($vars['title']) ? ((string) $vars['title']) : '',
            'subTitle' => isset($vars['subTitle']) ? ((string) $vars['subTitle']) : '',
            'forSale' => isset($vars['forSale']) ? ((bool) $vars['forSale']) : false,
            'hasChangelog' => isset($vars['hasChangelog']) ? ((bool) $vars['hasChangelog']) : false,
            'changelogExternalUrl' => isset($vars['changelogExternalUrl']) ?
                ((string) $vars['changelogExternalUrl']) :
                '',
            'hasDocumentation' => isset($vars['hasDocumentation']) ? ((bool) $vars['hasDocumentation']) : false,
            'actionButtons' => array_map([$this, 'mapYamlCtaToPayload'], $ctas),
        ]);
    }
}
