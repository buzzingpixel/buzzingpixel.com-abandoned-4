<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\Content;
use App\Content\PropertyTraits\Ctas;
use App\Content\PropertyTraits\FooterContent;
use App\Content\PropertyTraits\Heading;
use App\Content\PropertyTraits\TextBullets;
use App\Payload\SpecificPayload;

class CtaCardItemPayload extends SpecificPayload
{
    use Heading;
    use Content;
    use TextBullets;
    use Ctas;
    use FooterContent;
}
