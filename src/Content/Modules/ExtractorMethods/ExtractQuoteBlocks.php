<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\ImagePayload;
use App\Content\Modules\Payloads\QuoteBlockPayload;
use App\Content\Modules\Payloads\QuoteBlocksPayload;
use Throwable;
use function array_map;
use function is_array;

trait ExtractQuoteBlocks
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    public function extractQuoteBlocks(array $parsedYaml) : QuoteBlocksPayload
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
    private function mapYamlQuoteBlockToPayload(array $block) : QuoteBlockPayload
    {
        $image = isset($block['image']) && is_array($block['image']) ?
            $block['image'] :
            [];

        return new QuoteBlockPayload([
            'image' => new ImagePayload([
                'oneX' => (string) ($image['1x'] ?? ''),
                'twoX' => (string) ($image['2x'] ?? ''),
                'alt' => (string) ($image['alt'] ?? ''),
            ]),
            'personName' => (string) ($block['name'] ?? ''),
            'personNameHref' => (string) ($block['nameHref'] ?? ''),
            'position' => (string) ($block['position'] ?? ''),
            'positionHref' => (string) ($block['positionHref'] ?? ''),
            'content' => (string) ($block['content'] ?? ''),
        ]);
    }
}
