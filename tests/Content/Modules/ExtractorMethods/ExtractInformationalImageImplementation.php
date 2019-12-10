<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use App\Content\Modules\CommonTraits\MapYamlImageToPayload;
use App\Content\Modules\ExtractorMethods\ExtractInformationalImage;
use App\Content\Modules\Payloads\InformationalImagePayload;
use cebe\markdown\GithubMarkdown;
use Throwable;

class ExtractInformationalImageImplementation
{
    use MapYamlImageToPayload;
    use ExtractInformationalImage;

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
    public function runTest(array $parsedYaml) : InformationalImagePayload
    {
        return $this->extractInformationalImage($parsedYaml);
    }
}
