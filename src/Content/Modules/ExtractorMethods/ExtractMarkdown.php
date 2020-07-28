<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\MarkdownPayload;
use Throwable;

trait ExtractMarkdown
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function extractMarkdown(array $parsedYaml): MarkdownPayload
    {
        return new MarkdownPayload([
            'content' => (string) ($parsedYaml['content'] ?? ''),
        ]);
    }
}
