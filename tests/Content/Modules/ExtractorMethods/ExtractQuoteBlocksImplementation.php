<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use App\Content\Modules\CommonTraits\MapYamlImageToPayload;
use App\Content\Modules\ExtractorMethods\ExtractQuoteBlocks;
use App\Content\Modules\Payloads\QuoteBlocksPayload;
use Throwable;

class ExtractQuoteBlocksImplementation
{
    use MapYamlImageToPayload;
    use ExtractQuoteBlocks;

    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function runTest(array $parsedYaml) : QuoteBlocksPayload
    {
        return $this->extractQuoteBlocks($parsedYaml);
    }
}
