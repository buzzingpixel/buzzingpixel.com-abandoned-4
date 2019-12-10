<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use App\Content\Modules\CommonTraits\MapYamlImageToPayload;
use App\Content\Modules\ExtractorMethods\ExtractImage;
use App\Content\Modules\Payloads\ImageModulePayload;
use Throwable;

class ExtractImageImplementation
{
    use MapYamlImageToPayload;
    use ExtractImage;

    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function runTest(array $parsedYaml) : ImageModulePayload
    {
        return $this->extractImage($parsedYaml);
    }
}
