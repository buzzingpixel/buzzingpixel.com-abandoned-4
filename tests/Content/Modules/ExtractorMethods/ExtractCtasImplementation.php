<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use App\Content\Modules\CommonTraits\MapYamlCtaToPayload;
use App\Content\Modules\ExtractorMethods\ExtractCtas;
use App\Content\Modules\Payloads\CtasPayload;
use Throwable;

class ExtractCtasImplementation
{
    use MapYamlCtaToPayload;
    use ExtractCtas;

    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function runTest(array $parsedYaml) : CtasPayload
    {
        return $this->extractCtas($parsedYaml);
    }
}
