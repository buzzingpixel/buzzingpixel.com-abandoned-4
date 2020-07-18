<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

trait Headline
{
    private string $headline = '';

    protected function setHeadline(string $headline): void
    {
        $this->headline = $headline;
    }

    public function getHeadline(): string
    {
        return $this->headline;
    }
}
