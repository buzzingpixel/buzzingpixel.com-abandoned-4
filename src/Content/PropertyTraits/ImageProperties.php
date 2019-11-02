<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait ImageProperties
{
    /** @var string */
    private $src = '';

    protected function setSrc(string $src) : void
    {
        $this->src = $src;
    }

    public function getSrc() : string
    {
        return $this->src;
    }

    /** @var string */
    private $srcset = '';

    protected function setSrcset(string $srcset) : void
    {
        $this->srcset = $srcset;
    }

    public function getSrcset() : string
    {
        return $this->srcset;
    }

    /** @var string */
    private $alt = '';

    protected function setAlt(string $alt) : void
    {
        $this->alt = $alt;
    }

    public function getAlt() : string
    {
        return $this->alt;
    }
}
