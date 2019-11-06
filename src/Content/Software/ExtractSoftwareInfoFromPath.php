<?php

declare(strict_types=1);

namespace App\Content\Software;

use App\Content\Modules\CommonTraits\MapYamlCtaToPayload;
use Symfony\Component\Yaml\Yaml;
use Throwable;
use function array_map;
use function is_array;

class ExtractSoftwareInfoFromPath
{
    use MapYamlCtaToPayload;

    /** @var string */
    private $pathToContentDirectory;

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

        /** @var array|null $vars */
        $vars = Yaml::parseFile($fullPath);

        $ctas = isset($vars['actionButtons']) && is_array($vars['actionButtons']) ?
            $vars['actionButtons'] :
            [];

        return new SoftwareInfoPayload([
            'title' => isset($vars['title']) ? ((string) $vars['title']) : '',
            'subTitle' => isset($vars['subTitle']) ? ((string) $vars['subTitle']) : '',
            'forSale' => isset($vars['forSale']) ? ((bool) $vars['forSale']) : false,
            'hasChangelog' => isset($vars['hasChangelog']) ? ((bool) $vars['hasChangelog']) : false,
            'hasDocumentation' => isset($vars['hasDocumentation']) ? ((bool) $vars['hasDocumentation']) : false,
            'actionButtons' => array_map([$this, 'mapYamlCtaToPayload'], $ctas),
        ]);
    }
}
