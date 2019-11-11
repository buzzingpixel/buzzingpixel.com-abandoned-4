<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\PrimaryImageTextHalfBlack;
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
trait ExtractPrimaryImageTextHalfBlack
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    protected function extractPrimaryImageTextHalfBlack(array $parsedYaml) : PrimaryImageTextHalfBlack
    {
        $image = isset($parsedYaml['image']) && is_array($parsedYaml['image']) ?
            $parsedYaml['image'] :
            [];

        return new PrimaryImageTextHalfBlack([
            'headline' => (string) ($parsedYaml['headline'] ?? ''),
            'image' => $this->mapYamlImageToPayload($image),
            'content' => $this->markdownParser->parse((string) ($parsedYaml['content'] ?? '')),
        ]);
    }
}
