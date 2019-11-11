<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\InformationalImagePayload;
use cebe\markdown\GithubMarkdown;
use Throwable;
use function is_array;

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

        return new InformationalImagePayload([
            'headline' => (string) ($parsedYaml['headline'] ?? ''),
            'subHeadline' => (string) ($parsedYaml['subHeadline'] ?? ''),
            'content' => $this->markdownParser->parse((string) ($parsedYaml['content'] ?? '')),
            'image' => $this->mapYamlImageToPayload($image),
        ]);
    }
}
