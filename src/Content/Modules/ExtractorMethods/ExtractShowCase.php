<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\ImageSourcePayload;
use App\Content\Modules\Payloads\ShowCaseImagePayload;
use App\Content\Modules\Payloads\ShowCasePayload;
use Throwable;
use function array_map;
use function is_array;

/**
 * Requires parent to have:
 * `use \App\Content\Modules\CommonTraits\MapYamlCtaToPayload;`
 */
trait ExtractShowCase
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    protected function extractShowCase(array $parsedYaml) : ShowCasePayload
    {
        $yamlCtas = isset($parsedYaml['ctas']) && is_array($parsedYaml['ctas']) ?
            $parsedYaml['ctas'] :
            [];

        $yamlShowCaseImage = isset($parsedYaml['showCaseImage']) && is_array($parsedYaml['showCaseImage']) ?
            $parsedYaml['showCaseImage'] :
            [];

        $yamlShowCaseImageSources = isset($yamlShowCaseImage['sources']) && is_array($yamlShowCaseImage['sources']) ?
            $yamlShowCaseImage['sources'] :
            [];

        return new ShowCasePayload([
            'preHeadline' => (string) ($parsedYaml['preHeadline'] ?? ''),
            'headline' => (string) ($parsedYaml['headline'] ?? ''),
            'subHeadline' => (string) ($parsedYaml['subHeadline'] ?? ''),
            'ctas' => array_map([$this, 'mapYamlCtaToPayload'], $yamlCtas),
            'showCaseImage' => new ShowCaseImagePayload([
                'src' => (string) ($yamlShowCaseImage['src'] ?? ''),
                'srcset' => (string) ($yamlShowCaseImage['srcset'] ?? ''),
                'alt' => (string) ($yamlShowCaseImage['alt'] ?? ''),
                'sources' => array_map(
                    [$this, 'mapYamlShowCaseImageSourceToPayload'],
                    $yamlShowCaseImageSources
                ),
            ]),
        ]);
    }

    /**
     * @param array<string, string> $source
     *
     * @throws Throwable
     */
    private function mapYamlShowCaseImageSourceToPayload(array $source) : ImageSourcePayload
    {
        return new ImageSourcePayload([
            'oneX' => (string) ($source['1x'] ?? ''),
            'twoX' => (string) ($source['2x'] ?? ''),
            'mediaQuery' => (string) ($source['mediaQuery'] ?? ''),
        ]);
    }
}
