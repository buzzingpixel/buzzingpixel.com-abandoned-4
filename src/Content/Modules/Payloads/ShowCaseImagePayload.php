<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\ImageProperties;
use App\Payload\SpecificPayload;

class ShowCaseImagePayload extends SpecificPayload
{
    use ImageProperties;
}
