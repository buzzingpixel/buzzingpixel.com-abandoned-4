<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\Content;
use App\Content\PropertyTraits\Headline;
use App\Content\PropertyTraits\Image;
use App\Content\PropertyTraits\SubHeadline;
use App\Payload\SpecificPayload;

class InformationalImagePayload extends SpecificPayload
{
    use Headline;
    use SubHeadline;
    use Content;
    use Image;
}
