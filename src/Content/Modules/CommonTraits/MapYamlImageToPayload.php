<?php

declare(strict_types=1);

namespace App\Content\Modules\CommonTraits;

use App\Content\Modules\Payloads\ImagePayload;
use App\Content\Modules\Payloads\ImageSourcePayload;
use Throwable;
use function array_map;
use function is_array;
use function is_string;

trait MapYamlImageToPayload
{
    /**
     * @param array<string, mixed> $yamlImage
     *
     * @throws Throwable
     */
    protected function mapYamlImageToPayload(array $yamlImage) : ImagePayload
    {
        $imageSources = isset($yamlImage['sources']) && is_array($yamlImage['sources']) ?
            $yamlImage['sources'] :
            [];

        /** @psalm-suppress MixedAssignment */
        $oneX = $yamlImage['1x'] ?? '';
        /** @psalm-suppress MixedAssignment */
        $twoX = $yamlImage['2x'] ?? '';
        /** @psalm-suppress MixedAssignment */
        $alt = $yamlImage['alt'] ?? '';

        return new ImagePayload([
            'oneX' => is_string($oneX) ? $oneX : '',
            'twoX' => is_string($twoX) ? $twoX : '',
            'alt' => is_string($alt) ? $alt : '',
            'sources' => array_map(
                [$this, 'mapYamlImageSourceToPayload'],
                $imageSources
            ),
        ]);
    }

    /**
     * @param array<string, mixed> $source
     *
     * @throws Throwable
     */
    private function mapYamlImageSourceToPayload(array $source) : ImageSourcePayload
    {
        return new ImageSourcePayload([
            'oneX' => (string) ($source['1x'] ?? ''),
            'twoX' => (string) ($source['2x'] ?? ''),
            'mediaQuery' => (string) ($source['mediaQuery'] ?? ''),
        ]);
    }
}
