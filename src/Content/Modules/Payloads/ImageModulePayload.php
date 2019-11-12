<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\BackgroundColor;
use App\Content\PropertyTraits\Image;
use App\Content\PropertyTraits\NoShadow;
use App\Payload\SpecificPayload;

class ImageModulePayload extends SpecificPayload
{
    use BackgroundColor;
    use NoShadow;
    use Image;
}
