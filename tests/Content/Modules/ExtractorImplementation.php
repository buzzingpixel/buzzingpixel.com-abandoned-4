<?php

declare(strict_types=1);

namespace Tests\Content\Modules;

use App\Content\Modules\ExtractModulesFromPath;
use Throwable;

class ExtractorImplementation extends ExtractModulesFromPath
{
    /**
     * @param mixed[] $yaml
     *
     * @throws Throwable
     */
    protected function extractTestContent(array $yaml) : ExtractorImplementationPayload
    {
        return new ExtractorImplementationPayload(['yaml' => $yaml]);
    }
}
