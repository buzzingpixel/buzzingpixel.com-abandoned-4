<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Content\PropertyTraits\Content;
use App\Content\PropertyTraits\Heading;
use App\Payload\SpecificPayload;

class NotePayload extends SpecificPayload
{
    use Heading;
    use Content;
}
