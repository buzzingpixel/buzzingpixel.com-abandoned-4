<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use App\Content\Modules\CommonTraits\MapYamlCtaToPayload;
use App\Content\Modules\ExtractorMethods\ExtractCtaCards;
use App\Content\Modules\Payloads\CtaCardsPayload;
use cebe\markdown\GithubMarkdown;
use Throwable;

class ExtractCtaCardsImplementation
{
    use MapYamlCtaToPayload;
    use ExtractCtaCards;

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
    public function runTest(array $parsedYaml) : CtaCardsPayload
    {
        return $this->extractCtaCards($parsedYaml);
    }
}
