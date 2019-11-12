<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait NoShadow
{
    /** @var bool */
    private $noShadow = false;

    protected function setNoShadow(bool $noShadow) : void
    {
        $this->noShadow = $noShadow;
    }

    public function getNoShadow() : bool
    {
        return $this->noShadow;
    }
}
