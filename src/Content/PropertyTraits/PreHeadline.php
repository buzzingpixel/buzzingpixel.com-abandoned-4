<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait PreHeadline
{
    /** @var string */
    private $preHeadline = '';

    protected function setPreHeadline(string $preHeadline) : void
    {
        $this->preHeadline = $preHeadline;
    }

    public function getPreHeadline() : string
    {
        return $this->preHeadline;
    }
}
