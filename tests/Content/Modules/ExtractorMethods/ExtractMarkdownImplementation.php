<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use App\Content\Modules\ExtractorMethods\ExtractMarkdown;
use App\Content\Modules\Payloads\MarkdownPayload;
use Throwable;

class ExtractMarkdownImplementation
{
    use ExtractMarkdown;

    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function runTest(array $parsedYaml): MarkdownPayload
    {
        return $this->extractMarkdown($parsedYaml);
    }
}
