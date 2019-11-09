<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\Content;
use App\Content\PropertyTraits\Ctas;
use App\Content\PropertyTraits\Headline;
use App\Content\PropertyTraits\Image;
use App\Payload\SpecificPayload;

class ImageCallOutPayload extends SpecificPayload
{
    use Headline;
    use Content;
    use Ctas;
    use Image;
}
