<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\Content;
use App\Content\PropertyTraits\Image;
use App\Content\PropertyTraits\PersonName;
use App\Content\PropertyTraits\PersonNameHref;
use App\Content\PropertyTraits\Position;
use App\Content\PropertyTraits\PositionHref;
use App\Payload\SpecificPayload;

class QuoteBlockPayload extends SpecificPayload
{
    use Image;
    use PersonName;
    use PersonNameHref;
    use Position;
    use PositionHref;
    use Content;
}
