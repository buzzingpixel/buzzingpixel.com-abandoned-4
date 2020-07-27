<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Content\PropertyTraits\BackgroundColor;
use App\Content\PropertyTraits\Headline;
use App\Content\PropertyTraits\SubHeadline;
use App\Content\PropertyTraits\TextColor;
use App\Payload\SpecificPayload;

class SimpleTextInterrupterPayload extends SpecificPayload
{
    use BackgroundColor;
    use TextColor;
    use Headline;
    use SubHeadline;

    private bool $isH1 = false;

    protected function setIsH1(bool $isH1): void
    {
        $this->isH1 = $isH1;
    }

    public function getIsH1(): bool
    {
        return $this->isH1;
    }
}
