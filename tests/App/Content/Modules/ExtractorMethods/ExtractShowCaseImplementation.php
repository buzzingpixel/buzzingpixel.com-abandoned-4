<?php

declare(strict_types=1);

namespace Tests\App\Content\Modules\ExtractorMethods;

use App\Content\Modules\CommonTraits\MapYamlCtaToPayload;
use App\Content\Modules\CommonTraits\MapYamlImageToPayload;
use App\Content\Modules\ExtractorMethods\ExtractShowCase;
use App\Content\Modules\Payloads\ShowCasePayload;
use Throwable;

class ExtractShowCaseImplementation
{
    use MapYamlCtaToPayload;
    use MapYamlImageToPayload;
    use ExtractShowCase;

    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function runTest(array $parsedYaml) : ShowCasePayload
    {
        return $this->extractShowCase($parsedYaml);
    }
}
