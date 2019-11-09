<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\ImageCallOutPayload;
use App\Content\Modules\Payloads\ImagePayload;
use App\Content\Modules\Payloads\ImageSourcePayload;
use cebe\markdown\GithubMarkdown;
use Throwable;
use function array_map;
use function is_array;

/**
 * Requires parent to have:
 * `use \App\Content\Modules\CommonTraits\MapYamlCtaToPayload;`
 *
 * Requires $markdownParser property on parent
 *
 * @property GithubMarkdown $markdownParser
 */
trait ExtractImageCallOut
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    protected function extractImageCallOut(array $parsedYaml) : ImageCallOutPayload
    {
        $ctas = isset($parsedYaml['ctas']) && is_array($parsedYaml['ctas']) ?
            $parsedYaml['ctas'] :
            [];

        $image = isset($parsedYaml['image']) && is_array($parsedYaml['image']) ?
            $parsedYaml['image'] :
            [];

        $imageSources = isset($image['sources']) && is_array($image['sources']) ?
            $image['sources'] :
            [];

        return new ImageCallOutPayload([
            'headline' => (string) ($parsedYaml['headline'] ?? ''),
            'content' => $this->markdownParser->parse((string) ($parsedYaml['content'] ?? '')),
            'ctas' => array_map([$this, 'mapYamlCtaToPayload'], $ctas),
            'image' => new ImagePayload([
                'oneX' => (string) ($image['1x'] ?? ''),
                'twoX' => (string) ($image['2x'] ?? ''),
                'alt' => (string) ($image['alt'] ?? ''),
                'sources' => array_map(
                    [$this, 'mapImageCallOutImageSourceToPayload'],
                    $imageSources
                ),
            ]),
        ]);
    }

    /**
     * @param array<string, string> $source
     *
     * @throws Throwable
     */
    private function mapImageCallOutImageSourceToPayload(array $source) : ImageSourcePayload
    {
        return new ImageSourcePayload([
            'oneX' => (string) ($source['1x'] ?? ''),
            'twoX' => (string) ($source['2x'] ?? ''),
            'mediaQuery' => (string) ($source['mediaQuery'] ?? ''),
        ]);
    }
}
