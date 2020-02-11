<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use App\Content\Modules\CommonTraits\MapYamlCtaToPayload;
use App\Content\Modules\CommonTraits\MapYamlImageToPayload;
use App\Content\Modules\ExtractorMethods\ExtractImageCallOut;
use App\Content\Modules\Payloads\ImageCallOutPayload;
use cebe\markdown\GithubMarkdown;
use Throwable;

class ExtractImageCallOutImplementation
{
    use MapYamlCtaToPayload;
    use MapYamlImageToPayload;
    use ExtractImageCallOut;

    protected GithubMarkdown $markdownParser;

    public function __construct(GithubMarkdown $markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function runTest(array $parsedYaml) : ImageCallOutPayload
    {
        return $this->extractImageCallOut($parsedYaml);
    }
}
