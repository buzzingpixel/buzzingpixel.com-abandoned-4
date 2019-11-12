<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\Ctas;
use App\Payload\SpecificPayload;

class CtasPayload extends SpecificPayload
{
    use Ctas;
}
