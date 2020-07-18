<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Content\PropertyTraits\Content;
use App\Content\PropertyTraits\Heading;
use App\Payload\SpecificPayload;

class CodeblockPayload extends SpecificPayload
{
    use Heading;
    use Content;

    private string $lang = '';

    protected function setLang(string $lang): void
    {
        $this->lang = $lang;
    }

    public function getLang(): string
    {
        return $this->lang;
    }
}
