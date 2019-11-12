<?php

declare(strict_types=1);

namespace Tests\App\Content\Modules\ExtractorMethods;

use App\Content\Modules\ExtractorMethods\ExtractTextColumns;
use App\Content\Modules\Payloads\TextColumnsPayload;
use cebe\markdown\GithubMarkdown;
use Throwable;

class ExtractTextColumnsImplementation
{
    use ExtractTextColumns;

    /** @var GithubMarkdown */
    protected $markdownParser;

    public function __construct(GithubMarkdown $markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function runTest(array $parsedYaml) : TextColumnsPayload
    {
        return $this->extractTextColumns($parsedYaml);
    }
}