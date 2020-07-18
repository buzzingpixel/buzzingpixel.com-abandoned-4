<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\ShowCasePayload;
use Throwable;

use function array_map;
use function is_array;

/**
 * Requires parent to have:
 * `use \App\Content\Modules\CommonTraits\MapYamlImageToPayload;`
 *
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
    protected function extractShowCase(array $parsedYaml): ShowCasePayload
    {
        $yamlCtas = isset($parsedYaml['ctas']) && is_array($parsedYaml['ctas']) ?
            $parsedYaml['ctas'] :
            [];

        /** @var array<string, mixed> $yamlShowCaseImage */
        $yamlShowCaseImage = isset($parsedYaml['showCaseImage']) && is_array($parsedYaml['showCaseImage']) ?
            $parsedYaml['showCaseImage'] :
            [];

        return new ShowCasePayload([
            'headline' => (string) ($parsedYaml['headline'] ?? ''),
            'subHeadline' => (string) ($parsedYaml['subHeadline'] ?? ''),
            'content' => (string) ($parsedYaml['content'] ?? ''),
            'ctas' => array_map([$this, 'mapYamlCtaToPayload'], $yamlCtas),
            'showCaseImage' => $this->mapYamlImageToPayload($yamlShowCaseImage),
        ]);
    }
}
