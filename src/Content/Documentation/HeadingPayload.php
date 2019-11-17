<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Content\PropertyTraits\Content;
use App\Payload\SpecificPayload;

class HeadingPayload extends SpecificPayload
{
    use Content;

    /** @var int */
    private $level = 3;

    protected function setLevel(int $level) : void
    {
        $this->level = $level;
    }

    public function getLevel() : int
    {
        return $this->level;
    }
}
