<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\ImageCallOutPayload;
use cebe\markdown\GithubMarkdown;
use Throwable;
use function array_map;
use function is_array;

/**
 * Requires parent to have:
 * `use \App\Content\Modules\CommonTraits\MapYamlImageToPayload;`
 *
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

        /** @var array<string, mixed> $image */
        $image = isset($parsedYaml['image']) && is_array($parsedYaml['image']) ?
            $parsedYaml['image'] :
            [];

        return new ImageCallOutPayload([
            'headline' => (string) ($parsedYaml['headline'] ?? ''),
            'content' => $this->markdownParser->parse((string) ($parsedYaml['content'] ?? '')),
            'ctas' => array_map([$this, 'mapYamlCtaToPayload'], $ctas),
            'image' => $this->mapYamlImageToPayload($image),
        ]);
    }
}
