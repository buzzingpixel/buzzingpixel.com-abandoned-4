<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\BackgroundColor;
use App\Content\PropertyTraits\ContentWithHeadlineItems;
use App\Payload\SpecificPayload;

class TextColumnsPayload extends SpecificPayload
{
    use BackgroundColor;
    use ContentWithHeadlineItems;
}
