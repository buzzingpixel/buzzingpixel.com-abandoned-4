<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use App\Content\Modules\ExtractorMethods\ExtractSimpleTextInterrupter;
use App\Content\Modules\Payloads\SimpleTextInterrupterPayload;
use Throwable;

class ExtractSimpleTextInterrupterImplementation
{
    use ExtractSimpleTextInterrupter;

    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function runTest(array $parsedYaml): SimpleTextInterrupterPayload
    {
        return $this->extractSimpleTextInterrupter($parsedYaml);
    }
}
