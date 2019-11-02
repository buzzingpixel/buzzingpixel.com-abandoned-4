<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\Content;
use App\Content\PropertyTraits\Href;
use App\Payload\SpecificPayload;

class CtaPayload extends SpecificPayload
{
    use Href;
    use Content;
}
