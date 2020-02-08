<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\QuoteBlockPayload;
use function array_walk;

trait QuoteBlocks
{
    /** @var QuoteBlockPayload[] */
    private array $quoteBlocks = [];

    /**
     * @param QuoteBlockPayload[] $quoteBlocks
     */
    protected function setQuoteBlocks(array $quoteBlocks) : void
    {
        array_walk($quoteBlocks, [$this, 'addQuoteBlock']);
    }

    private function addQuoteBlock(QuoteBlockPayload $quoteBlock) : void
    {
        $this->quoteBlocks[] = $quoteBlock;
    }

    /**
     * @return QuoteBlockPayload[]
     */
    public function getQuoteBlocks() : array
    {
        return $this->quoteBlocks;
    }
}
