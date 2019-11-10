<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait Position
{
    /** @var string */
    private $position = '';

    protected function setPosition(string $position) : void
    {
        $this->position = $position;
    }

    public function getPosition() : string
    {
        return $this->position;
    }
}
