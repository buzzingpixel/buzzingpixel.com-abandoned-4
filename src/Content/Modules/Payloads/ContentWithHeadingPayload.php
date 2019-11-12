<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\Content;
use App\Content\PropertyTraits\Headline;
use App\Payload\SpecificPayload;

class ContentWithHeadingPayload extends SpecificPayload
{
    use Headline;
    use Content;
}
