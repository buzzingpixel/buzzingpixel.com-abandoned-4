<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\ImageModulePayload;
use Throwable;
use function is_array;
use function is_bool;

/**
 * Requires parent to have:
 * `use \App\Content\Modules\CommonTraits\MapYamlImageToPayload;`
 */
trait ExtractImage
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    protected function extractImage(array $parsedYaml) : ImageModulePayload
    {
        /** @psalm-suppress MixedAssignment */
        $noShadow = ($parsedYaml['noShadow'] ?? false);
        $noShadow = is_bool($noShadow) ? $noShadow : false;

        /** @var array<string, mixed> $image */
        $image = isset($parsedYaml['image']) && is_array($parsedYaml['image']) ?
            $parsedYaml['image'] :
            [];

        return new ImageModulePayload([
            'backgroundColor' => (string) ($parsedYaml['backgroundColor'] ?? ''),
            'noShadow' => $noShadow === true,
            'image' => $this->mapYamlImageToPayload($image),
        ]);
    }
}
