<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait PositionHref
{
    /** @var string */
    private $positionHref = '';

    protected function setPositionHref(string $positionHref) : void
    {
        $this->positionHref = $positionHref;
    }

    public function getPositionHref() : string
    {
        return $this->positionHref;
    }
}
