<?php

declare(strict_types=1);

namespace App\Content\Meta;

use App\Content\PropertyTraits\ImageProperties;
use App\Payload\SpecificPayload;

class HeadingBackgroundPayload extends SpecificPayload
{
    use ImageProperties;
}
