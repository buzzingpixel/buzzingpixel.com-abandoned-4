<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait TextColor
{
    private string $textColor = '';

    protected function setTextColor(string $textColor): void
    {
        $this->textColor = $textColor;
    }

    public function getTextColor(): string
    {
        return $this->textColor;
    }
}
