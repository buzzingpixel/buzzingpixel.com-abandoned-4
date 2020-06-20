<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\QuoteBlockPayload;
use App\Content\Modules\Payloads\QuoteBlocksPayload;
use Throwable;

use function array_map;
use function is_array;

/**
 * Requires parent to have:
 * `use \App\Content\Modules\CommonTraits\MapYamlImageToPayload;`
 */
trait ExtractQuoteBlocks
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function extractQuoteBlocks(array $parsedYaml): QuoteBlocksPayload
    {
        $blocks = isset($parsedYaml['blocks']) && is_array($parsedYaml['blocks']) ?
            $parsedYaml['blocks'] :
            [];

        return new QuoteBlocksPayload([
            'quoteBlocks' => array_map(
                [$this, 'mapYamlQuoteBlockToPayload'],
                $blocks
            ),
        ]);
    }

    /**
     * @param array<string, mixed> $block
     *
     * @throws Throwable
     */
    private function mapYamlQuoteBlockToPayload(array $block): QuoteBlockPayload
    {
        /** @var array<string, mixed> $image */
        $image = isset($block['image']) && is_array($block['image']) ?
            $block['image'] :
            [];

        return new QuoteBlockPayload([
            'image' => $this->mapYamlImageToPayload($image),
            'personName' => (string) ($block['name'] ?? ''),
            'personNameHref' => (string) ($block['nameHref'] ?? ''),
            'position' => (string) ($block['position'] ?? ''),
            'positionHref' => (string) ($block['positionHref'] ?? ''),
            'content' => (string) ($block['content'] ?? ''),
        ]);
    }
}
