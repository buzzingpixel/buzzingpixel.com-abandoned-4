<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait BackgroundColor
{
    private string $backgroundColor = '';

    protected function setBackgroundColor(string $backgroundColor) : void
    {
        $this->backgroundColor = $backgroundColor;
    }

    public function getBackgroundColor() : string
    {
        return $this->backgroundColor;
    }
}
