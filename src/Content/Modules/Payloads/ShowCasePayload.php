<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\Ctas;
use App\Content\PropertyTraits\Headline;
use App\Content\PropertyTraits\PreHeadline;
use App\Content\PropertyTraits\ShowCaseImage;
use App\Content\PropertyTraits\SubHeadline;
use App\Payload\SpecificPayload;

class ShowCasePayload extends SpecificPayload
{
    use PreHeadline;
    use Headline;
    use SubHeadline;
    use Ctas;
    use ShowCaseImage;
}
