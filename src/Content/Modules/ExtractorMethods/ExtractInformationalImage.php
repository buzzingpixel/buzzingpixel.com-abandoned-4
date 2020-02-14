<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\InformationalImagePayload;
use cebe\markdown\GithubMarkdown;
use Throwable;
use function is_array;
use function is_string;

/**
 * Requires parent to have:
 * `use \App\Content\Modules\CommonTraits\MapYamlImageToPayload;`
 *
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
        /** @var array<string, mixed> $image */
        $image = isset($parsedYaml['image']) && is_array($parsedYaml['image']) ?
            $parsedYaml['image'] :
            [];

        /** @psalm-suppress MixedAssignment */
        $headline = $parsedYaml['headline'] ?? '';
        /** @psalm-suppress MixedAssignment */
        $subHeadline = $parsedYaml['subHeadline'] ?? '';
        /** @psalm-suppress MixedAssignment */
        $content = $parsedYaml['content'] ?? '';

        return new InformationalImagePayload([
            'headline' => is_string($headline) ? $headline : '',
            'subHeadline' => is_string($subHeadline) ? $subHeadline : '',
            'content' => $this->markdownParser->parse(
                is_string($content) ? $content : ''
            ),
            'image' => $this->mapYamlImageToPayload($image),
        ]);
    }
}
