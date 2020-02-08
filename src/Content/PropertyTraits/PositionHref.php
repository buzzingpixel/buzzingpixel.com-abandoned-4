<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait PositionHref
{
    private string $positionHref = '';

    protected function setPositionHref(string $positionHref) : void
    {
        $this->positionHref = $positionHref;
    }

    public function getPositionHref() : string
    {
        return $this->positionHref;
    }
}
