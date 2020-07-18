<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\CtasPayload;
use Throwable;

use function array_map;
use function is_array;

/**
 * Requires parent to have:
 * `use \App\Content\Modules\CommonTraits\MapYamlCtaToPayload;`
 */
trait ExtractCtas
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    protected function extractCtas(array $parsedYaml): CtasPayload
    {
        $yamlCtas = isset($parsedYaml['ctas']) && is_array($parsedYaml['ctas']) ?
            $parsedYaml['ctas'] :
            [];

        return new CtasPayload([
            'ctas' => array_map([$this, 'mapYamlCtaToPayload'], $yamlCtas),
        ]);
    }
}
