<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Content\PropertyTraits\Content;
use App\Payload\SpecificPayload;

class ContentPayload extends SpecificPayload
{
    use Content;
}
