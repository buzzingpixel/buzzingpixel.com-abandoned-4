<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\Content;
use App\Payload\SpecificPayload;

class MarkdownPayload extends SpecificPayload
{
    use Content;
}
