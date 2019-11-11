<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\Content;
use App\Content\PropertyTraits\Headline;
use App\Content\PropertyTraits\Image;
use App\Payload\SpecificPayload;

class PrimaryImageTextHalfBlack extends SpecificPayload
{
    use Headline;
    use Image;
    use Content;
}
