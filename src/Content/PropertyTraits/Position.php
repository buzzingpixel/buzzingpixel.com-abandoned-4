<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait Position
{
    private string $position = '';

    protected function setPosition(string $position) : void
    {
        $this->position = $position;
    }

    public function getPosition() : string
    {
        return $this->position;
    }
}
