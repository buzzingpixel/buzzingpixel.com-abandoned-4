<?php

declare(strict_types=1);

namespace Tests\App\Content\Modules\ExtractorMethods;

use App\Content\Modules\CommonTraits\MapYamlImageToPayload;
use App\Content\Modules\ExtractorMethods\ExtractPrimaryImageTextHalfBlack;
use App\Content\Modules\Payloads\PrimaryImageTextHalfBlack;
use cebe\markdown\GithubMarkdown;
use Throwable;

class ExtractPrimaryImageTextHalfBlackImplementation
{
    use MapYamlImageToPayload;
    use ExtractPrimaryImageTextHalfBlack;

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
    public function runTest(array $parsedYaml) : PrimaryImageTextHalfBlack
    {
        return $this->extractPrimaryImageTextHalfBlack($parsedYaml);
    }
}
