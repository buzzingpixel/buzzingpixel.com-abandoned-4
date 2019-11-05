<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\ImagePayload;
use App\Content\Modules\Payloads\ImageSourcePayload;
use App\Content\Modules\Payloads\InformationalImagePayload;
use cebe\markdown\GithubMarkdown;
use Throwable;
use function array_map;
use function is_array;

/**
 * Requires $markdownParser property on parent
 *
 * @property GithubMarkdown $markdownParser
 */
trait ExtractInformationalImage
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    protected function extractInformationalImage(array $parsedYaml) : InformationalImagePayload
    {
        $image = isset($parsedYaml['image']) && is_array($parsedYaml['image']) ?
            $parsedYaml['image'] :
            [];

        $imageSources = isset($image['sources']) && is_array($image['sources']) ?
            $image['sources'] :
            [];

        return new InformationalImagePayload([
            'headline' => (string) ($parsedYaml['headline'] ?? ''),
            'subHeadline' => (string) ($parsedYaml['subHeadline'] ?? ''),
            'content' => $this->markdownParser->parse((string) ($parsedYaml['content'] ?? '')),
            'image' => new ImagePayload([
                'oneX' => (string) ($image['1x'] ?? ''),
                'twoX' => (string) ($image['2x'] ?? ''),
                'alt' => (string) ($image['alt'] ?? ''),
                'sources' => array_map(
                    [$this, 'mapYamlInformationalImageSourceToPayload'],
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
    private function mapYamlInformationalImageSourceToPayload(array $source) : ImageSourcePayload
    {
        return new ImageSourcePayload([
            'oneX' => (string) ($source['1x'] ?? ''),
            'twoX' => (string) ($source['2x'] ?? ''),
            'mediaQuery' => (string) ($source['mediaQuery'] ?? ''),
        ]);
    }
}
