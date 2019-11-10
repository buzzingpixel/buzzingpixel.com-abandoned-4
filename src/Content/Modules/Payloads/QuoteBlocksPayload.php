<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\QuoteBlocks;
use App\Payload\SpecificPayload;

class QuoteBlocksPayload extends SpecificPayload
{
    use QuoteBlocks;
}
